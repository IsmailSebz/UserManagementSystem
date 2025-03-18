<?php
    include_once 'includes/header.php';
    $reset_email = $_POST["Reset_Email"] ?? null;
    $errors = [];

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // FIRST CHECK IF THE USER IS REGISTERED IN THE DATABASE
        if (!empty($reset_email)) {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $reset_email);
            $stmt->execute();
            $result = $stmt->get_result();  
            
            if ($result->num_rows != 1) {
                $errors[] = "No user Email found";
            } else {
                // Redirect to password_verify.php if the email is valid
                header("Location: password_verify.php?email=" . urlencode($reset_email));
                exit();
            }
        } else {
            $errors[] = "Please enter your email address.";
        }
    }
?>

<h2>Reset Password</h2>
<div class="form-container">

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <form action="password_reset.php" method="post">
            <label for="email">Email</label>
            <input type="email" name="Reset_Email" placeholder="Enter your Email" required><br><br>
            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</div>

<?php
    include_once 'includes/footer.php';
?>