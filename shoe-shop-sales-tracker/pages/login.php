<?php include '../includes/header.php'; ?>
<h2>Login</h2>
<?php if (isset($_GET['error'])) echo "<p style='color:red;'>".htmlspecialchars($_GET['error'])."</p>"; ?>
<form action="../actions/login_action.php" method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
<?php include '../includes/footer.php'; ?>