<?php
session_start();
require_once 'config.php';

// Redirect if there is no email in the session
if (!isset($_SESSION['temp_email'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if (isset($_POST['verify'])) {
    $user_otp = $_POST['otp'];
    $email = $_SESSION['temp_email'];

    // Securely check if the OTP matches the email in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Success: Mark the account as verified and remove the temporary code
        $update = $conn->prepare("UPDATE users SET otp_code = NULL, is_verified = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        
        unset($_SESSION['temp_email']);
        $_SESSION['login_error'] = 'Account verified successfully! You may now login.';
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid or expired verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | PSS E-Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .otp-card { width: 400px; border-radius: 15px; border: none; }
        .btn-verify { background-color: #BE0000; color: white; transition: 0.3s; }
        .btn-verify:hover { background-color: #8B0000; color: white; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card otp-card shadow-lg p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #BE0000;">Account Verification</h3>
            <p class="text-muted small">Please enter the 6-digit code sent to:<br>
            <span class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['temp_email']); ?></span></p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <input type="text" name="otp" class="form-control text-center fs-3 fw-bold tracking-widest" 
                       placeholder="000000" maxlength="6" pattern="\d{6}" required autofocus>
            </div>
            <button type="submit" name="verify" class="btn btn-verify w-100 fw-bold py-2 shadow-sm">Verify Now</button>
            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none small text-muted">Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>