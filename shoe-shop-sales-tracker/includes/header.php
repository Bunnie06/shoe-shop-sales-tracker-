<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Shoe Shop Sales Tracker'); ?></title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/style.css">
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/form.css">
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/tables.css">
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/utilities.css">
    
    <!-- Page-specific CSS -->
    <?php if (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false): ?>
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/dashboard.css">
    <?php elseif (strpos($_SERVER['REQUEST_URI'], 'inventory') !== false): ?>
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/inventory.css">
    <?php elseif (strpos($_SERVER['REQUEST_URI'], 'sales') !== false): ?>
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/sales.css">
    <?php elseif (strpos($_SERVER['REQUEST_URI'], 'reports') !== false): ?>
    <link rel="stylesheet" href="/shoe-shop-sales-tracker/assets/css/reports.css">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- JavaScript -->
    <script src="/shoe-shop-sales-tracker/assets/js/script.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    
    <!-- Favicon -->
    <link rel="icon" href="/shoe-shop-sales-tracker/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
<header class="main-header">
    <div class="header-container">
        <div class="branding">
            <a href="/shoe-shop-sales-tracker/index.php" class="logo-link">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                </div>
                <h1>Shoe Shop Sales Tracker</h1>
            </a>
        </div>
        
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="/shoe-shop-sales-tracker/index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a href="/shoe-shop-sales-tracker/pages/dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                
                <?php if ($_SESSION['role'] === 'owner'): ?>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-boxes"></i>
                        <span class="nav-text">Inventory</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/shoe-shop-sales-tracker/pages/inventory/"><i class="fas fa-list"></i> View Inventory</a></li>
                        <li><a href="/shoe-shop-sales-tracker/pages/inventory/add_stock.php"><i class="fas fa-plus"></i> Add Stock</a></li>
                        <li><a href="/shoe-shop-sales-tracker/pages/inventory/categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-text">Reports</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/shoe-shop-sales-tracker/pages/reports/sales.php"><i class="fas fa-chart-bar"></i> Sales Reports</a></li>
                        <li><a href="/shoe-shop-sales-tracker/pages/reports/inventory.php"><i class="fas fa-box-open"></i> Inventory Reports</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="/shoe-shop-sales-tracker/pages/users/" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span class="nav-text">Manage Users</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="/shoe-shop-sales-tracker/pages/sales/record_sale.php" class="nav-link">
                        <i class="fas fa-cash-register"></i>
                        <span class="nav-text">Record Sale</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="/shoe-shop-sales-tracker/pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="/shoe-shop-sales-tracker/pages/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="/shoe-shop-sales-tracker/actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/shoe-shop-sales-tracker/pages/auth/login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </div>
        
        <button class="mobile-menu-toggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<div class="content-wrapper">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button class="close-alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button class="close-alert">&times;</button>
        </div>
    <?php endif; ?>