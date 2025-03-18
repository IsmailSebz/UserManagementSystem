<?php
    include_once 'includes/header.php';

    // Initialize variables
    $errors = [];
    $reset_code = $_POST['Reset_Code'] ?? null;
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Validate the reset code, new password, and confirm password
        if (empty($reset_code)) {
            $errors[] = "Reset code is required.";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required.";
        }
        if (empty($confirm_password)) {
            $errors[] = "Confirm password is required.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // If no errors, proceed with verification
        if (empty($errors)) {
            // Check if the reset code is valid and not expired
            $stmt = $conn->prepare("SELECT id, reset_token_expires_at FROM users WHERE reset_token = ?");
            $stmt->bind_param("s", $reset_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $expiry_time = $user['reset_token_expires_at'];

                // Check if the reset token has expired
                if (strtotime($expiry_time) > time()) {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the user's password and clear the reset token
                    $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed_password, $user['id']);
                    $update_stmt->execute();

                    if ($update_stmt->affected_rows === 1) {
                        // Password updated successfully
                        echo "<div class='alert success'>Password updated successfully. You can now <a href='login.php'>login</a> with your new password.</div>";
                    } else {
                        $errors[] = "Failed to update password.";
                    }
                } else {
                    $errors[] = "Reset code has expired.";
                }
            } else {
                $errors[] = "Invalid reset code.";
            }
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
        <form action="password_reset_final.php" method="post">
            <label for="Reset_Code">Verification Code</label>
            <input type="text" name="Reset_Code" placeholder="Enter Reset code sent to your email" required><br><br>

            <label for="new_password">New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" required><br><br>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Re-enter new password" required><br><br>

            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</div>

<?php
    include_once 'includes/footer.php';
?>