<?php

require 'config.php';

if(isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    
    
    // Image Upload Logic
    $imgName = $_FILES['book_image']['name'];
    $tmpName = $_FILES['book_image']['tmp_name'];
    $folder = "uploads/" . $imgName;

    if(move_uploaded_file($tmpName, $folder)) {
        $query = "INSERT INTO books (title, author, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $title, $author, $imgName);
        $stmt->execute();
        header("Location: admin_page.php?success=1");
        exit();
    }
}
?>