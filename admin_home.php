<?php 
session_start();
if (!isset($_SESSION['email'])){
  header ("Location: index.php");
  exit();
}



require_once 'config.php'; 

// 1. Total Borrows
$total_orders_query = $conn->query("SELECT COUNT(*) as count FROM borrow_form");
$total_orders = ($total_orders_query) ? $total_orders_query->fetch_assoc()['count'] : 0;

// 2. Available Books
$total_books_query = $conn->query("SELECT COUNT(*) as qty FROM books");
$savebook = ($total_books_query) ? $total_books_query->fetch_assoc()['qty'] : 0;

// 3. Registered Users
$users_query = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = ($users_query) ? $users_query->fetch_assoc()['total'] : 0;

// 4. Fetch transactions with user email
$query = "SELECT b.*, u.email 
          FROM borrow_form b 
          LEFT JOIN users u ON b.studentName = u.name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="./fontawesome-free-7.0.1-web/css/all.min.css" />
  <script defer src="js/bootstrap.bundle.min.js"></script>
  <title>LIBRARY | Admin Dashboard</title>
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
      <a class="navbar-brand fw-bold" href="admin_home.php" style="color: #BE0000;">Admin Dashboard</a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="admin_page.php">Add books</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card stat-card text-white p-3 border-0 shadow-sm" style="background-color: #BE0000;">
          <h6>TOTAL BORROWS</h6>
          <h2 class="fw-bold"><?php echo $total_orders; ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card stat-card text-white p-3 border-0 shadow-sm" style="background-color: #343a40;">
          <h6>AVAILABLE BOOKS</h6>
          <h2 class="fw-bold"><?php echo $savebook; ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card stat-card text-white p-3 border-0 shadow-sm" style="background-color: #212529;">
          <h6>REGISTERED USERS</h6>
          <h2 class="fw-bold"><?php echo $total_users; ?></h2>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">STUDENT TRANSACTIONS & PENALTIES</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Student Email</th>
                    <th>Book Title</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Penalty (₱50/day)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    // --- CALCULATION LOGIC ---
                    $today = new DateTime();
                    $due_date = new DateTime($row['returnDate']);
                    $interval = $today->diff($due_date);
                    $days_left = (int)$interval->format("%r%a"); // negative means overdue
                    
                    $penalty = 0;
                    if ($days_left < 0) {
                        $status_class = "badge bg-danger";
                        $display_text = abs($days_left) . " Days Overdue";
                        $penalty = abs($days_left) * 50; // 50 pesos per day
                    } elseif ($days_left == 0) {
                        $status_class = "badge bg-warning text-dark";
                        $display_text = "Due Today";
                    } else {
                        $status_class = "badge bg-success";
                        $display_text = $days_left . " Days Left";
                    }
                ?>
                <tr>
                    <td class="text-primary small"><?php echo htmlspecialchars($row['email'] ?? 'No Account'); ?></td>
                    <td><?php echo htmlspecialchars($row['bookTitle'] ?? ''); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['returnDate'])); ?></td>
                    <td>
                        <span class="<?php echo $status_class; ?> px-3 py-2">
                            <?php echo $display_text; ?>
                        </span>
                    </td>
                    <td class="penalty-text">
                        <?php echo ($penalty > 0) ? "₱" . number_format($penalty) : "₱0"; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-warning notify-btn" 
                                    data-email="<?php echo $row['email']; ?>" 
                                    data-name="<?php echo htmlspecialchars($row['studentName']); ?>" 
                                    data-book="<?php echo htmlspecialchars($row['bookTitle']); ?>"
                                    data-penalty="<?php echo $penalty; ?>"> 
                                <i class="fa-solid fa-bell"></i>
                            </button>
                            <a href="uploads/<?php echo $row['id_image']; ?>" target="_blank" class="btn btn-sm btn-info text-white">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete record?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center py-4">No transactions found.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

<script>
document.querySelectorAll('.notify-btn').forEach(button => {
    button.addEventListener('click', function() {
        const email = this.getAttribute('data-email');
        const name = this.getAttribute('data-name');
        const book = this.getAttribute('data-book');
        const penalty = this.getAttribute('data-penalty');

        if (!email || email === 'No Account') {
            alert('Cannot notify: No email linked to this student.');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('notify_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&name=${encodeURIComponent(name)}&book=${encodeURIComponent(book)}&penalty=${encodeURIComponent(penalty)}`
        })
        .then(response => response.text())
        .then(data => {
            alert('Notification sent! Penalty of ₱' + penalty + ' included.');
            this.disabled = false;
            this.innerHTML = '<i class="fa-solid fa-bell"></i>';
        })
        .catch(error => {
            alert('Error sending notification.');
            this.disabled = false;
            this.innerHTML = '<i class="fa-solid fa-bell"></i>';
        });
    });
});
</script> 


<script>
  .then(data => {
    // Show a smart alert to the admin
    const penaltyValue = parseInt(penalty);
    const alertMsg = (penaltyValue > 0) 
        ? 'Overdue notice & penalty instruction sent to ' + name 
        : 'Return reminder (Due Today) sent to ' + name;

    alert(alertMsg);
    this.disabled = false;
    this.innerHTML = '<i class="fa-solid fa-bell"></i>';
})
</script>

</body>
</html>