<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Automatically assigns admin if ".admin" is included anywhere in the email prefix
    $role = (preg_match('/\.admin@/', $email)) ? "admin" : $_POST['role'];

    // REMOVED: The @ici.edu.ph strict email domain restriction check

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['register_error'] = 'Email already registered!';
        header("Location: index.php");
        exit();
    } else {
        $otp = rand(100000, 999999);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, otp_code, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $otp);
        
        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'cyrusmatt670@gmail.com'; 
                $mail->Password   = 'qdab hiif huun kklj';   
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('noreply@ici.edu.ph', 'PSS E-Library');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Library Account';
                $mail->Body    = "Hi $name, <br><br> Your verification code is: <b>$otp</b>";

                $mail->send();
                $_SESSION['temp_email'] = $email;
                header("Location: verify_otp.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['register_error'] = "Mail error: Please check your internet connection.";
                header("Location: index.php");
                exit();
            }
        }
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                $_SESSION['temp_email'] = $email;
                header("Location: verify_otp.php");
                exit();
            }

            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email']; 

            if ($user['role'] === 'admin') {
                header("Location: admin_home.php");
            } else {
                header("Location: user_page.php");
            }
            exit();
        }
    }
    $_SESSION['login_error'] = 'Incorrect email or password';
    header("Location: index.php");
    exit();
}
?>