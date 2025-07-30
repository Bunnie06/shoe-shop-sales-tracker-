<?php 
include '../includes/auth.php'; 
include '../includes/header.php'; 

// Get stats for dashboard
require_once '../config/db.php';

// Total sales today
$todaySales = $conn->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE DATE(sale_time) = CURDATE()")->fetch();

// Inventory status
$inventoryStats = $conn->query("SELECT COUNT(*) as total_items, SUM(quantity) as total_stock FROM inventory")->fetch();

// Low stock items
$lowStock = $conn->query("SELECT COUNT(*) as low_stock_count FROM inventory WHERE quantity < 10")->fetchColumn();

// Recent sales
$recentSales = $conn->query("SELECT s.id, i.name, s.quantity_sold, s.total_amount, s.sale_time 
                            FROM sales s JOIN inventory i ON s.inventory_id = i.id 
                            ORDER BY s.sale_time DESC LIMIT 5")->fetchAll();
?>

<div class="dashboard">
    <div class="welcome-banner">
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?>!</h2>
        <p>Here's what's happening with your store today.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3><?= $todaySales['count'] ?></h3>
                <p>Today's Sales</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>$<?= number_format($todaySales['total'] ?? 0, 2) ?></h3>
                <p>Today's Revenue</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <h3><?= $inventoryStats['total_items'] ?></h3>
                <p>Products in Stock</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?= $lowStock ?></h3>
                <p>Low Stock Items</p>
            </div>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="recent-sales">
            <h3><i class="fas fa-clock"></i> Recent Sales</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentSales as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['name']) ?></td>
                            <td><?= $sale['quantity_sold'] ?></td>
                            <td>$<?= number_format($sale['total_amount'], 2) ?></td>
                            <td><?= date('h:i A', strtotime($sale['sale_time'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="action-buttons">
                <a href="record_sale.php" class="btn btn-primary">
                    <i class="fas fa-cash-register"></i> Record Sale
                </a>
                
                <?php if($_SESSION['role'] === 'owner'): ?>
                <a href="add_stock.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add Stock
                </a>
                <a href="reports.php" class="btn btn-info">
                    <i class="fas fa-chart-pie"></i> View Reports
                </a>
                <?php endif; ?>
                
                <a href="inventory.php" class="btn btn-secondary">
                    <i class="fas fa-box-open"></i> View Inventory
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>