<?php
include_once 'includes/header.php';

// Check if remember me cookie exists
if (!isLoggedIn()) {
    checkRememberMeCookie($conn);
}
?>

<h2>Welcome to the User Management System</h2>

<?php if (isLoggedIn()): ?>
    <div class="form-container">
        <p>Hello, <?php echo $_SESSION['username']; ?>!</p>
        <p>You are logged in. You can:</p>
        <ul>
            <li><a href="profile.php">View your profile</a></li>
            <li><a href="edit-profile.php">Edit your profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
<?php else: ?>
    <div class="form-container">
        <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access your account.</p>
    </div>
<?php endif; ?>

<?php include_once 'includes/footer.php'; ?>