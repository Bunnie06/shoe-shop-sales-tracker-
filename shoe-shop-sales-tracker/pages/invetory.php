<?php 
include '../includes/auth.php'; 
include '../includes/header.php';

require_once '../config/db.php';

// Handle search and filtering
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$lowStock = isset($_GET['low_stock']);

$query = "SELECT * FROM inventory WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR brand LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
}

if ($lowStock) {
    $query .= " AND quantity < 10";
}

$query .= " ORDER BY name ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$inventory = $stmt->fetchAll();
?>

<div class="inventory-management">
    <div class="page-header">
        <h2><i class="fas fa-boxes"></i> Inventory Management</h2>
        <div class="actions">
            <a href="add_stock.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Item
            </a>
        </div>
    </div>

    <div class="inventory-filters card">
        <form method="get" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search products..." 
                           value="<?= htmlspecialchars($search) ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="sneakers" <?= $type === 'sneakers' ? 'selected' : '' ?>>Sneakers</option>
                        <option value="boots" <?= $type === 'boots' ? 'selected' : '' ?>>Boots</option>
                        <option value="sandals" <?= $type === 'sandals' ? 'selected' : '' ?>>Sandals</option>
                        <option value="formal" <?= $type === 'formal' ? 'selected' : '' ?>>Formal</option>
                        <option value="athletic" <?= $type === 'athletic' ? 'selected' : '' ?>>Athletic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="low_stock" <?= $lowStock ? 'checked' : '' ?>>
                        <span class="checkmark"></span>
                        Show Low Stock Only
                    </label>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="inventory.php" class="btn btn-outline">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="inventory-table card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inventory as $item): 
                        $stockStatus = $item['quantity'] == 0 ? 'out-of-stock' : 
                                      ($item['quantity'] < 10 ? 'low-stock' : 'in-stock');
                        $statusText = $item['quantity'] == 0 ? 'Out of Stock' : 
                                      ($item['quantity'] < 10 ? 'Low Stock' : 'In Stock');
                    ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <?php if($item['description']): ?>
                            <small class="text-muted"><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['brand']) ?></td>
                        <td><?= ucfirst($item['type']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>
                            <span class="status-badge <?= $stockStatus ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit_stock.php?id=<?= $item['id'] ?>" class="btn-icon edit" 
                               data-tooltip="Edit Item">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../actions/delete_stock.php?id=<?= $item['id'] ?>" 
                               class="btn-icon delete" data-tooltip="Delete Item"
                               data-confirm="Are you sure you want to delete this item?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($inventory)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No inventory items found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>