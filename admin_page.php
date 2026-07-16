<?php
include 'config.php';

if(isset($_POST['add_book'])){
   $book_title = mysqli_real_escape_string($conn, $_POST['book_title']);
   $book_author = mysqli_real_escape_string($conn, $_POST['book_author']);
   $book_category = $_POST['category']; // Get the selected section
   $book_image = $_FILES['book_image']['name'];
   $book_image_tmp_name = $_FILES['book_image']['tmp_name'];
   $book_image_folder = 'uploads/'.$book_image;

   if(empty($book_title) || empty($book_author) || empty($book_category) || empty($book_image)){
      $message[] = 'Please fill out all fields!';
   }else{
      // Updated INSERT query to include the 'category' column
      $insert = "INSERT INTO books(title, author, image, category) VALUES('$book_title', '$book_author', '$book_image', '$book_category')";
      $upload = mysqli_query($conn, $insert);
      if($upload){
         move_uploaded_file($book_image_tmp_name, $book_image_folder);
         $message[] = 'New book added successfully to ' . $book_category . '!';
      }else{
         $message[] = 'Could not add the book.';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="./fontawesome-free-7.0.1-web/css/all.min.css" />
  <script defer src="js/bootstrap.bundle.min.js"></script>
    <title>LIBRARY | Admin Dashboard</title>
   <style>
      body { background-color: #f8f9fa; padding-top: 50px; }
      .form-container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
      .btn-add { background-color: #BE0000; color: white; border: none; }
      .btn-add:hover { background-color: #8B0000; color: white; }
   </style>
</head>
<body>


  
 
  <style>
    body { padding-top: 90px; background-color: #f8f8f8; }
    .stat-card { border-radius: 15px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); }
    .penalty-text { color: #dc3545; font-weight: bold; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="admin_home.php" style="color: #BE0000;">ADMIN E-library</a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="admin_home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_home.php">Dashboard</a></li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container">
   <?php
   if(isset($message)){
      foreach($message as $msg){
         echo '<div class="alert alert-info">'.$msg.'</div>';
      }
   }
   ?>

   <div class="form-container">
      <h3 class="text-center fw-bold mb-4">Add New Book</h3>
      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
         
         <div class="mb-3">
            <label class="form-label">Book Title</label>
            <input type="text" name="book_title" class="form-control" placeholder="Enter book title" required>
         </div>

         <div class="mb-3">
            <label class="form-label">Author Name</label>
            <input type="text" name="book_author" class="form-control" placeholder="Enter author name" required>
         </div>

         <div class="mb-3">
            <label class="form-label">Assign to Section/Category</label>
            <select name="category" class="form-select" required>
               <option value="" selected disabled>Choose a section...</option>
               <option value="Science">Science</option>
               <option value="Math">Math</option>
               <option value="History">History</option>
               <option value="English">English</option>
               
            </select>
         </div>

         <div class="mb-3">
            <label class="form-label">Book Cover Image</label>
            <input type="file" name="book_image" accept="image/png, image/jpeg, image/jpg" class="form-control" required>
         </div>

         <button type="submit" name="add_book" class="btn btn-add w-100">Upload Book</button>
         <a href="admin_home.php" class="btn btn-dark w-100 mt-2">Back to Dashboard</a>
      </form>
   </div>
</div>

</body>
</html>