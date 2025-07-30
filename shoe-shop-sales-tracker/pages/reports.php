<?php 
include '../includes/auth.php'; 
include '../includes/header.php';

// Only owner can access reports
if ($_SESSION['role'] !== 'owner') {
    header("Location: dashboard.php");
    exit;
}

require_once '../config/db.php';

// Default date range (this month)
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Get sales summary
$salesSummary = $conn->prepare("
    SELECT 
        DATE(sale_time) as sale_date,
        COUNT(*) as total_sales,
        SUM(total_amount) as total_revenue,
        SUM(quantity_sold) as total_items_sold
    FROM sales
    WHERE sale_time BETWEEN ? AND ? + INTERVAL 1 DAY
    GROUP BY DATE(sale_time)
    ORDER BY sale_date ASC
");
$salesSummary->execute([$startDate, $endDate]);
$dailySales = $salesSummary->fetchAll();

// Get total summary
$totalSummary = $conn->prepare("
    SELECT 
        COUNT(*) as total_sales,
        SUM(total_amount) as total_revenue,
        SUM(quantity_sold) as total_items_sold,
        AVG(total_amount) as avg_sale_amount
    FROM sales
    WHERE sale_time BETWEEN ? AND ? + INTERVAL 1 DAY
");
$totalSummary->execute([$startDate, $endDate]);
$totals = $totalSummary->fetch();

// Get top selling products
$topProducts = $conn->prepare("
    SELECT 
        i.name,
        i.brand,
        SUM(s.quantity_sold) as total_sold,
        SUM(s.total_amount) as total_revenue
    FROM sales s
    JOIN inventory i ON s.inventory_id = i.id
    WHERE s.sale_time BETWEEN ? AND ? + INTERVAL 1 DAY
    GROUP BY s.inventory_id
    ORDER BY total_sold DESC
    LIMIT 5
");
$topProducts->execute([$startDate, $endDate]);
$topProducts = $topProducts->fetchAll();
?>

<div class="reports-page">
    <div class="page-header">
        <h2><i class="fas fa-chart-bar"></i> Sales Reports</h2>
    </div>

    <div class="report-filters card">
        <form method="get" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="<?= htmlspecialchars($startDate) ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="<?= htmlspecialchars($endDate) ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="reports.php" class="btn btn-outline">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="report-summary">
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-icon bg-primary">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="summary-info">
                    <h3><?= $totals['total_sales'] ?? 0 ?></h3>
                    <p>Total Sales</p>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon bg-success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="summary-info">
                    <h3>$<?= number_format($totals['total_revenue'] ?? 0, 2) ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon bg-warning">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="summary-info">
                    <h3><?= $totals['total_items_sold'] ?? 0 ?></h3>
                    <p>Items Sold</p>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="summary-info">
                    <h3>$<?= number_format($totals['avg_sale_amount'] ?? 0, 2) ?></h3>
                    <p>Avg. Sale</p>
                </div>
            </div>
        </div>
    </div>

    <div class="report-charts">
        <div class="chart-container card">
            <h3>Daily Sales Revenue</h3>
            <canvas id="dailySalesChart"></canvas>
        </div>
        
        <div class="chart-container card">
            <h3>Top Selling Products</h3>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

    <div class="report-tables">
        <div class="table-container card">
            <h3>Daily Sales Breakdown</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sales Count</th>
                            <th>Items Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dailySales as $day): ?>
                        <tr>
                            <td><?= date('M j, Y', strtotime($day['sale_date'])) ?></td>
                            <td><?= $day['total_sales'] ?></td>
                            <td><?= $day['total_items_sold'] ?></td>
                            <td>$<?= number_format($day['total_revenue'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($dailySales)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No sales data for selected period</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="table-container card">
            <h3>Top Selling Products</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($topProducts as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['brand']) ?></td>
                            <td><?= $product['total_sold'] ?></td>
                            <td>$<?= number_format($product['total_revenue'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($topProducts)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No product data for selected period</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for charts
    const dailySalesData = {
        labels: [<?php foreach($dailySales as $day): ?>"<?= date('M j', strtotime($day['sale_date'])) ?>", <?php endforeach; ?>],
        datasets: [{
            label: 'Daily Revenue',
            data: [<?php foreach($dailySales as $day): ?><?= $day['total_revenue'] ?>, <?php endforeach; ?>],
            backgroundColor: 'rgba(0, 95, 115, 0.2)',
            borderColor: 'rgba(0, 95, 115, 1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    };
    
    const topProductsData = {
        labels: [<?php foreach($topProducts as $product): ?>"<?= htmlspecialchars($product['name']) ?>", <?php endforeach; ?>],
        datasets: [{
            label: 'Units Sold',
            data: [<?php foreach($topProducts as $product): ?><?= $product['total_sold'] ?>, <?php endforeach; ?>],
            backgroundColor: 'rgba(10, 147, 150, 0.5)',
            borderColor: 'rgba(10, 147, 150, 1)',
            borderWidth: 1
        }]
    };
    
    // Create charts
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(dailySalesCtx, {
        type: 'line',
        data: dailySalesData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
    
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'bar',
        data: topProductsData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>