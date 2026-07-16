<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $mail = new PHPMailer(true);

    try {
        // --- Server Settings ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cyrusmatt670@gmail.com'; 
        $mail->Password   = 'qdab hiif huun kklj'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 30;

        // --- Recipients ---
        $mail->setFrom('cyrusmatt670@gmail.com', 'PSS E-Library');
        $mail->addAddress($_POST['email']); 

        // --- Content Logic ---
        $mail->isHTML(true);
        
        // Sanitize inputs
        $name = htmlspecialchars($_POST['name']);
        $book = htmlspecialchars($_POST['book']);
        $penalty = isset($_POST['penalty']) ? (int)$_POST['penalty'] : 0;

        // --- DYNAMIC MESSAGE SWITCH ---
        if ($penalty > 0) {
            // 1. OVERDUE MESSAGE (Sent if book is late)
            $mail->Subject = 'URGENT: Overdue Book & Penalty Notice';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; border: 2px solid #BE0000; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #BE0000;'>Overdue Notification</h2>
                    <p>Hi <b>$name</b>,</p>
                    <p>The book <b>'$book'</b> was not returned on time and is now overdue.</p>
                    <div style='background-color: #fff3f3; padding: 15px; border-left: 5px solid #BE0000; margin: 15px 0;'>
                        <p style='font-size: 18px; margin: 0;'>Total Penalty: <b>₱" . number_format($penalty, 2) . "</b></p>
                        <p style='margin-top: 10px; color: #333; font-weight: bold;'>
                            Please return the book immediately and settle your payment for the overdues at the PSS Cashier.
                        </p>
                    </div>
                    <hr>
                    <p style='font-size: 0.8em; color: #666;'>This is an automated message from the PSS E-Library System.</p>
                </div>";
        } else {
            // 2. REMINDER MESSAGE (Sent if due today or not yet late)
            $mail->Subject = 'Book Return Reminder - PSS E-Library';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; border: 2px solid #333; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #333;'>Library Reminder</h2>
                    <p>Hi <b>$name</b>,</p>
                    <p>This is a friendly reminder to return the book: <b>'$book'</b> today.</p>
                    <p>Please return it to the library at your earliest convenience to avoid daily late fees (₱50.00/day).</p>
                    <hr>
                    <p style='font-size: 0.8em; color: #666;'>This is an automated message from the PSS E-Library System.</p>
                </div>";
        }

        $mail->send();
        echo "Notification sent successfully to " . $name;

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } finally {
        $mail->smtpClose();
    }
}