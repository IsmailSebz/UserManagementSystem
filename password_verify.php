
<?php
    include_once 'includes/header.php';

    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';
    require 'phpmailer/Exception.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $toEmail = sanitizeInput($_GET['email']);
    $mail = new PHPMailer(true);
    $pass_verified = false;



//FIRST CHECK IF THE USER IS REGISTERED IN THE DATABASE
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $toEmail);
$stmt->execute();
$result = $stmt->get_result();
$mail_sent = false;
$pass_verified = ($result->num_rows === 1); //if verified, true else false


$rand_code=null;
$expiry_time ="5 minutes";
$reset_code = random_int(100000,999999);
    try {
        // SMTP Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tsebz07@gmail.com';
        $mail->Password   = 'levv ktis wlif pwnd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email content
        $mail->setFrom('tsebz07@gmail.com', 'Group C');
        $mail->addAddress($toEmail); // Recipient email

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email';
        $mail->Body    = 'Hello! Here is your activation code: <br><b>'.$reset_code.'</b>';

        $mail->send();

        $mail_sent = true;

    } catch (Exception $e) {
        $mail_sent = false;
        echo "Error sending email: {$mail->ErrorInfo}";
        header("Location: password_reset.php");
        exit();
    }
?>
<?php

if($mail_sent){
    $expiry = date("Y-m-d H:i:s",strtotime("+".$expiry_time));

    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE email = ?");
    $stmt->bind_param("iss", $reset_code, $expiry, $toEmail);
    
    // $stmt->bind_param("iss", $reset_code, $expiry,$toEmail);
    $stmt->execute();

    require_once "password_reset_final.php";
}
?>

<!--
    <div class="form-group">
        <p>Check your email and enter the <b>six digit code </b>sent to your email to verify your account. <br>
            Code will expire in <b><?php echo $expiry_time ?></b> 
        </p>
        <form action="password_reset_final.php" method="post">
            <label for="Reset_Code" >Verification Code</label>
            <input type="text" name="Reset_Code" placeholder="Enter Reset code sent to your email" required><br><br>
            <input type="submit" value="Submit">
        </form>
    </div>
-->
<?php    
    include_once 'includes/footer.php';
?>
