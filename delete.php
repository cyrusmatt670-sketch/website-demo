<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $stmt = $conn->prepare("DELETE FROM borrow_form WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_home.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}
?>