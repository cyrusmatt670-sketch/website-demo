<?php
session_start();
require_once 'config.php'; 
require_once 'fpdf.php';   

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate file upload
    if (!isset($_FILES['id_image']) || $_FILES['id_image']['error'] !== UPLOAD_ERR_OK) {
        die("Error: No file uploaded. Please ensure the form has enctype='multipart/form-data'.");
    }

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES["id_image"]["name"]);
    $targetPath = $uploadDir . $fileName;

    // Capture form data
    $bookTitle    = $_POST['bookTitle'] ?? 'N/A';
    $studentName  = $_POST['studentName'] ?? 'N/A';
    $gradeSection = $_POST['gradeSection'] ?? 'N/A'; // Captured from form
    $dateBorrowed = $_POST['dateBorrowed'] ?? 'N/A';
    $returnDate   = $_POST['returnDate'] ?? 'N/A';
    $phonenumber  = $_POST['phonenumber'] ?? 'N/A';

    if (move_uploaded_file($_FILES["id_image"]["tmp_name"], $targetPath)) {
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO borrow_form (bookTitle, studentName, gradeSection, dateBorrowed, returnDate, phonenumber, id_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $bookTitle, $studentName, $gradeSection, $dateBorrowed, $returnDate, $phonenumber, $fileName);

        if ($stmt->execute()) {
            // Generate PDF Receipt
            $pdf = new FPDF();
            $pdf->AddPage();
            
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'PSS E-Library Receipt', 0, 1, 'C');
            $pdf->Ln(5);
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(50, 10, 'Student Name:', 1);  $pdf->Cell(0, 10, $studentName, 1, 1);
            
            // --- ADDED GRADE & SECTION TO PDF ---
            $pdf->Cell(50, 10, 'Grade & Section:', 1); $pdf->Cell(0, 10, $gradeSection, 1, 1);
            
            $pdf->Cell(50, 10, 'Book Title:', 1);     $pdf->Cell(0, 10, $bookTitle, 1, 1);
            $pdf->Cell(50, 10, 'Return Date:', 1);   $pdf->Cell(0, 10, $returnDate, 1, 1);
            $pdf->Cell(50, 10, 'Phone Number:', 1);  $pdf->Cell(0, 10, $phonenumber, 1, 1);
            
            $pdf->Ln(10);
            $pdf->Cell(0, 10, 'School ID:', 0, 1);
            $pdf->Image($targetPath, 10, $pdf->GetY(), 60); 

            // Clear buffer to ensure PDF downloads correctly
            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('D', 'Receipt_' . $studentName . '.pdf'); 
            exit();
        } else {
            echo "Database Error: " . $stmt->error;
        }
    } else {
        echo "Failed to save the uploaded image.";
    }
}
?>