<?php
include_once 'includes/header.php';
requireLogin();

// Get user data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    $_SESSION['message'] = "Error retrieving user data";
    $_SESSION['message_type'] = "error";
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate form data
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!validateEmail($email)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email is already used by another user
    if ($email !== $user['email']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email already in use by another account";
        }
    }
    
    // Handle password change if provided
    $updatePassword = false;
    if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
        if (empty($currentPassword)) {
            $errors[] = "Current password is required to change password";
        }
        
        if (empty($newPassword)) {
            $errors[] = "New password is required";
        } elseif (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = "New passwords do not match";
        }
        
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!password_verify($currentPassword, $row['password'])) {
            $errors[] = "Current password is incorrect";
        } else {
            $updatePassword = true;
        }
    }
    
    // Handle profile picture update if provided
    $profilePicture = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] !== '') {
        $uploadResult = handleFileUpload($_FILES['profile_picture']);
        if (is_array($uploadResult) && isset($uploadResult['error'])) {
            $errors[] = $uploadResult['error'];
        } else {
            // Delete old profile picture if exists
            if ($profilePicture) {
                deleteProfilePicture($profilePicture);
            }
            $profilePicture = $uploadResult;
        }
    }
    
    // If no errors, update user in database
    if (empty($errors)) {
        if ($updatePassword) {
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update user with new password
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $hashedPassword, $profilePicture, $userId);
        } else {
            // Update user without changing password
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $profilePicture, $userId);
        }
        
        if ($stmt->execute()) {
            // Update session username
            $_SESSION['username'] = $username;
            
            $_SESSION['message'] = "Profile updated successfully";
            $_SESSION['message_type'] = "success";
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = "Update failed. Please try again.";
            
            // Restore old profile picture if update fails
            if ($profilePicture !== $user['profile_picture']) {
                deleteProfilePicture($profilePicture);
                $profilePicture = $user['profile_picture'];
            }
        }
    }
}
?>

<h2>Edit Profile</h2>
<div class="form-container