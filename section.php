<?php 
include 'config.php'; 

// 1. Get the category from the URL safely
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'Science';

// 2. Fetch only books matching this specific category
$select_books = mysqli_query($conn, "SELECT * FROM books WHERE category = '$category'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="./fontawesome-free-7.0.1-web/css/all.min.css" />
    <script defer src="js/bootstrap.bundle.min.js"></script>
    <title>LIBRARY | <?php echo strtoupper($category); ?></title>

    <style>
        body { padding-top: 80px; background-color: #f8f9fa; }
        .search { border-radius: 20px; padding: 6px 15px; border: 1px solid #ccc; width: 250px; outline: none; }
        .search:focus { border-color: #BE0000; }
        
        /* Category Navigation Bar */
        .category-nav { background: #fff; padding: 10px 0; margin-bottom: 20px; border-bottom: 1px solid #ddd; }
        .category-link { text-decoration: none; color: #555; padding: 8px 15px; border-radius: 20px; font-weight: 500; transition: 0.3s; }
        .category-link:hover { background: #eee; }
        .category-link.active { background: #BE0000; color: #fff; }

        /* Book Item Styling */
        .book-item { 
            display: flex; align-items: center; background: white; 
            margin-bottom: 15px; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .book-rank { font-size: 1.5rem; font-weight: bold; margin-right: 20px; color: #bbb; width: 40px; text-align: center; }
        .book-cover { width: 80px; height: 110px; object-fit: cover; margin-right: 20px; border-radius: 4px; border: 1px solid #eee; }
        .book-info { flex-grow: 1; }
        .book-title { font-size: 1.2rem; font-weight: bold; color: #333; }
        .book-author { color: #777; margin: 0; }

        /* Borrow Popup */
        .borrow-modal {
            display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); justify-content: center; align-items: center;
        }
        .borrow-content { background: white; width: 95%; max-width: 500px; padding: 25px; border-radius: 12px; position: relative; max-height: 90vh; overflow-y: auto; }
        .close-btn { position: absolute; right: 20px; top: 10px; font-size: 28px; cursor: pointer; color: #999; }
        .borrow-content input { width: 100%; padding: 10px; margin-bottom: 12px; border-radius: 6px; border: 1px solid #ddd; }
        .form-label { font-weight: bold; margin-bottom: 5px; display: block; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="user_page.php" style="color: #BE0000;">PSS E-Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="user_page.php">Home</a></li>

                    <li class="nav-item me-3">
                        <div class="d-flex align-items-center bg-light rounded-pill px-2 border">
                            <input type="text" class="search border-0 bg-transparent" id="searchInput" placeholder="Search in <?php echo $category; ?>..." />
                            <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="category-nav shadow-sm sticky-top" style="top: 70px; z-index: 1020;">
        <div class="container d-flex justify-content-center gap-2">
            <a href="section.php?category=Science" class="category-link <?php echo ($category == 'Science') ? 'active' : ''; ?>">Science</a>
            <a href="section.php?category=Math" class="category-link <?php echo ($category == 'Math') ? 'active' : ''; ?>">Math</a>
            <a href="section.php?category=History" class="category-link <?php echo ($category == 'History') ? 'active' : ''; ?>">History</a>
            <a href="section.php?category=English" class="category-link <?php echo ($category == 'English') ? 'active' : ''; ?>">English</a>
        </div>
    </div>

    <div class="container mt-4">
        <h2 class="fw-bold text-center mb-4"><?php echo strtoupper($category); ?> SECTION</h2>
        
        <div class="book-list-container" id="bookList">
            <?php 
            $rank = 1;
            if(mysqli_num_rows($select_books) > 0) {
                while($row = mysqli_fetch_assoc($select_books)){ 
            ?>
            <div class="book-item">
                <div class="book-rank"><?php echo $rank++; ?></div>
                <img src="uploads/<?php echo $row['image']; ?>" class="book-cover">
                <div class="book-info">
                    <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    <p class="book-author">by <?php echo htmlspecialchars($row['author']); ?></p>
                </div>
                <button class="btn btn-primary btn-borrow-trigger" data-title="<?php echo htmlspecialchars($row['title']); ?>">
                    Borrow <i class="fa-solid fa-hand-holding"></i>
                </button>
            </div>
            <?php 
                }
            } else {
                echo "<div class='text-center py-5'><p class='text-muted'>No books found in this section.</p></div>";
            }
            ?>
        </div>
    </div>

    <div id="borrowModal" class="borrow-modal">
        <div class="borrow-content">
            <span class="close-btn">&times;</span>
            <h3 class="mb-4 fw-bold">Borrowing Form</h3>

            <form action="borrowform.php" method="POST" enctype="multipart/form-data" id="borrowForm">
                <label class="form-label">Book Title</label>
                <input type="text" name="bookTitle" id="bookTitle" readonly class="bg-light fw-bold">

                <label class="form-label">Full Name</label>
                <input type="text" name="studentName" placeholder="Enter your full name" required>

                <label class="form-label">Upload Student ID Photo</label>
                <input type="file" name="id_image" accept="image/*" required class="form-control">
                
                <label class="form-label">Grade & Section </label>
                <input type="text" name="gradeSection" placeholder="Grade - Section  " required>

                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Date Borrowed</label>
                        <input type="date" name="dateBorrowed" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Return Date</label>
                        <input type="date" name="returnDate" required>
                    </div>
                </div>

                <label class="form-label">Phone Number</label>
                <input type="text" name="phonenumber" placeholder="09xxxxxxxxx" required>

                <button type="submit" class="btn btn-success w-100 mt-3 p-2 fw-bold">Confirm Borrow Request</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("borrowModal");
        const bookTitleInput = document.getElementById("bookTitle");
        const closeBtn = document.querySelector(".close-btn");

        // Open Modal - FIXED: Matches the button class above
        document.querySelectorAll(".btn-borrow-trigger").forEach(button => {
            button.addEventListener("click", function () {
                const title = this.getAttribute("data-title");
                bookTitleInput.value = title;
                modal.style.display = "flex";
            });
        });

        // Close Modal
        closeBtn.onclick = () => modal.style.display = "none";
        window.onclick = (event) => { if (event.target === modal) modal.style.display = "none"; };

        // Search Filter Logic
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let books = document.querySelectorAll('.book-item');
            books.forEach(book => {
                let title = book.querySelector('.book-title').innerText.toLowerCase();
                book.style.display = title.includes(filter) ? "flex" : "none";
            });
        });
    </script>
</body>
</html>