<?php 
include '../includes/auth.php'; 
include '../includes/header.php';

require_once '../config/db.php';

// Get inventory items for dropdown
$inventory = $conn->query("SELECT id, name, price, quantity FROM inventory WHERE quantity > 0 ORDER BY name")->fetchAll();

// Get attendants for dropdown (if owner)
$attendants = [];
if ($_SESSION['role'] === 'owner') {
    $attendants = $conn->query("SELECT id, username, full_name FROM users WHERE role = 'attendant'")->fetchAll();
}
?>

<div class="sales-recording">
    <div class="page-header">
        <h2><i class="fas fa-cash-register"></i> Record New Sale</h2>
    </div>

    <div class="sale-form-container">
        <form id="saleForm" action="../actions/record_sale.php" method="POST">
            <?php if($_SESSION['role'] === 'owner'): ?>
            <div class="form-group">
                <label for="attendant">Attendant</label>
                <select name="attendant_id" id="attendant" class="form-control" required>
                    <option value="">Select Attendant</option>
                    <?php foreach($attendants as $attendant): ?>
                    <option value="<?= $attendant['id'] ?>">
                        <?= htmlspecialchars($attendant['full_name'] ?? $attendant['username']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <input type="hidden" name="attendant_id" value="<?= $_SESSION['user_id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="customer_name">Customer Name (Optional)</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control">
            </div>

            <div class="form-group">
                <label for="customer_contact">Customer Contact (Optional)</label>
                <input type="text" name="customer_contact" id="customer_contact" class="form-control">
            </div>

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="cash">Cash</option>
                    <option value="card">Credit/Debit Card</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>

            <div class="sale-items-container">
                <h3>Sale Items</h3>
                <div id="saleItems">
                    <div class="sale-item">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="items[0][inventory_id]" class="form-control item-select" required>
                                    <option value="">Select Product</option>
                                    <?php foreach($inventory as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-price="<?= $item['price'] ?>"
                                            data-stock="<?= $item['quantity'] ?>">
                                        <?= htmlspecialchars($item['name']) ?> - $<?= number_format($item['price'], 2) ?> (<?= $item['quantity'] ?> in stock)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" name="items[0][quantity]" min="1" value="1" 
                                       class="form-control item-quantity" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Unit Price</label>
                                <input type="number" name="items[0][unit_price]" step="0.01" 
                                       class="form-control item-price" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Total</label>
                                <input type="number" name="items[0][total]" step="0.01" 
                                       class="form-control item-total" readonly>
                            </div>
                            
                            <div class="form-group">
                                <button type="button" class="btn btn-danger remove-item" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="addItem" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>
            </div>

            <div class="sale-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                
                <div class="form-group discount-field">
                    <label for="discount">Discount</label>
                    <div class="input-group">
                        <input type="number" name="discount" id="discount" min="0" max="100" 
                               value="0" step="0.01" class="form-control">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                
                <div class="summary-row">
                    <span>Discount Amount:</span>
                    <span id="discountAmount">$0.00</span>
                </div>
                
                <div class="summary-row grand-total">
                    <span>Total Amount:</span>
                    <span id="grandTotal">$0.00</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Complete Sale
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the first item's price
    const firstItemSelect = document.querySelector('.item-select');
    if (firstItemSelect && firstItemSelect.value) {
        const price = firstItemSelect.options[firstItemSelect.selectedIndex].dataset.price;
        document.querySelector('.item-price').value = price;
        document.querySelector('.item-total').value = price;
        updateTotals();
    }
});
</script>

<?php include '../includes/footer.php'; ?>