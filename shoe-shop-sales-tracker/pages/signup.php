<?php include '../includes/auth.php'; ?>
<?php include '../includes/header.php'; ?>
<h2>Create Attendant Account</h2>
<?php
if (isset($_GET['success'])) echo "<p style='color: green;'>".htmlspecialchars($_GET['success'])."</p>";
if (isset($_GET['error'])) echo "<p style='color: red;'>".htmlspecialchars($_GET['error'])."</p>";
?>
<form action="../actions/signup_action.php" method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Create Account</button>
</form>
<?php include '../includes/footer.php'; ?>