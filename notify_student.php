<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $book = $_POST['book'];
    $penalty = (int)$_POST['penalty']; // Check the penalty sent from the dashboard

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cyrusmatt670@gmail.com'; 
        $mail->Password   = 'qdab hiif huun kklj'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        $mail->setFrom('cyrusmatt670@gmail.com', 'PSS E-Library');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);

        // --- SMART LOGIC ---
        if ($penalty > 0) {
            // MESSAGE FOR OVERDUE (1 day or more)
            $mail->Subject = 'URGENT: Overdue Book & Penalty Notice';
            $messageBody = "
                <div style='font-family: Arial, sans-serif; border: 2px solid #BE0000; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #BE0000;'>Overdue Notification</h2>
                    <p>Hi <strong>$name</strong>,</p>
                    <p>The book <strong>\"$book\"</strong> was not returned on time and is now overdue.</p>
                    <div style='background-color: #fff3f3; padding: 15px; border-left: 5px solid #BE0000; margin: 15px 0;'>
                        <p style='font-size: 18px; margin: 0;'>Current Penalty: <strong>₱" . number_format($penalty, 2) . "</strong></p>
                        <p style='margin-top: 10px; color: #333;'>Please <strong>return the book</strong> and <strong>pay for the overdues</strong> at the <strong>PSS Cashier</strong> immediately.</p>
                    </div>
                    <p>Thank you.</p>
                </div>";
        } else {
            // MESSAGE FOR REMINDER (Due today or not yet late)
            $mail->Subject = 'Reminder: Return your book today';
            $messageBody = "
                <div style='font-family: Arial, sans-serif; border: 2px solid #333; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #333;'>Library Reminder</h2>
                    <p>Hi <strong>$name</strong>,</p>
                    <p>This is a reminder that you need to <strong>return the book \"$book\" today</strong>.</p>
                    <p>Please return it to the library before closing to avoid a ₱50.00 daily penalty.</p>
                    <p>Thank you!</p>
                </div>";
        }

        $mail->Body = $messageBody;
        $mail->send();
        echo 'Success';
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
}
?>