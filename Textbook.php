<?php 
include 'config.php'; 
$select_books = mysqli_query($conn, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="Textbooks.css" />
    <link rel="stylesheet" href="./fontawesome-free-7.0.1-web/css/all.min.css" />
    <script defer src="js/bootstrap.bundle.min.js"></script>
    <title>LIBRARY | PSS E-Library</title>

    <style>
        body { padding-top: 80px; overflow-x: hidden; background-color: #f8f9fa; }
        .search { border-radius: 20px; padding: 6px 15px; border: 1px solid #ccc; outline: none; width: 200px; }
        
        /* Borrow Popup */
        .borrow-modal {
            display: none; 
            position: fixed;
            z-index: 9999;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center; align-items: center;
        }
        .borrow-content {
            background: white; width: 90%; max-width: 500px;
            padding: 25px; border-radius: 10px; position: relative;
        }
        .close-btn { position: absolute; right: 20px; top: 10px; font-size: 28px; cursor: pointer; }
        .borrow-content input { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ddd; }
        
        /* Book List Styling */
        .book-list-container { padding: 20px; max-width: 1200px; margin: auto; }
        .book-item { 
            display: flex; align-items: center; background: white; 
            margin-bottom: 15px; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .book-rank { font-size: 1.5rem; font-weight: bold; margin-right: 20px; color: #555; width: 30px; }
        .book-cover { width: 80px; height: 110px; object-fit: cover; margin-right: 20px; border-radius: 4px; }
        .book-info { flex-grow: 1; }
        .book-title { font-size: 1.2rem; font-weight: bold; text-decoration: none; color: #007bff; }
        .book-author { color: #666; margin: 0; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#go_home" style="color: #BE0000;">PSS E-Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="user_page.php">Home</a></li>
                    
                    <li class="nav-item">
                        <input type="text" class="search ms-lg-3" id="searchInput" placeholder="Search books..." />
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container text-center mt-4">
        <h1 class="fw-bold">HISTORY</h1>
        <hr>
    </div>

    <div class="book-list-container">
        <?php 
        $rank = 1;
        while($row = mysqli_fetch_assoc($select_books)){ 
        ?>
        <div class="book-item">
            <div class="book-rank"><?php echo $rank++; ?></div>
            <img src="uploads/<?php echo $row['image']; ?>" alt="Cover" class="book-cover">
            
            <div class="book-info">
                <a href="#" class="book-title"><?php echo htmlspecialchars($row['title']); ?></a>
                <p class="book-author">by <?php echo htmlspecialchars($row['author']); ?></p>
            </div>

            <div class="book-actions">
                <button class="btn btn-primary btn-read" data-title="<?php echo htmlspecialchars($row['title']); ?>">
                    Borrow <i class="fa-solid fa-hand-holding"></i>
                </button>
            </div>
        </div>
        <?php } ?>
    </div>

    <div id="borrowModal" class="borrow-modal">
        <div class="borrow-content">
            <span class="close-btn">&times;</span>
            <h3 class="mb-4">Borrowing Form</h3>

            <form action="borrowform.php" method="POST" enctype="multipart/form-data" id="borrowForm">
                <label class="form-label">Book Title</label>
                <input type="text" name="bookTitle" id="bookTitle" readonly class="bg-light">

                <label class="form-label">Full Name</label>
                <input type="text" name="studentName" placeholder ="unsa imong ngalan??" required>

                <label class="form-label">Upload Student ID:</label>
                <input type="file" name="id_image" accept="image/*" required class="form-control mb-3">
                
                <div class="mb-3">
                    <label>Grade & Section</label>
                    <input type="text" name="gradeSection" class="form-control" placeholder=" unsa Grade og Strand" required>
                </div>

                <div class="row">
                     
                    <div class="col-6">
                        <label class="form-label">Date Borrowed</label>
                        <input type="date" name="dateBorrowed" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Return Date</label>
                        <input type="date" name="returnDate" required>
                    </div>
                </div>

                <label class="form-label">Phone Number</label>
                <input type="text" name="phonenumber" placeholder="09xxxxxxxxx" required>

                <button type="submit" class="btn btn-success w-100 mt-2">Confirm Borrow</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("borrowModal");
        const bookTitleInput = document.getElementById("bookTitle");
        const closeBtn = document.querySelector(".close-btn");

        // Open Modal
        document.querySelectorAll(".btn-read").forEach(button => {
            button.addEventListener("click", function () {
                const title = this.getAttribute("data-title");
                bookTitleInput.value = title;
                modal.style.display = "flex";
            });
        });

        // Close Modal
        closeBtn.onclick = () => modal.style.display = "none";
        window.onclick = (event) => { if (event.target === modal) modal.style.display = "none"; };
    </script>
</body>
</html>