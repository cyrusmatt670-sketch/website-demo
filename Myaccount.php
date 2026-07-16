<?php
session_start();
require_once 'config.php'; 

// Fix: Use 'email' instead of 'user_email' to match your login session
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$userEmail = $_SESSION['email'];
$username = $_SESSION['name'] ?? 'User';

// Fix: Use the correct table name 'borrow_form' and correct column names
// We join with the users table to filter by the logged-in user's email
$query = "SELECT b.* FROM borrow_form b 
          INNER JOIN users u ON b.studentName = u.name 
          WHERE u.email = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$borrowedBooks = [];
while ($row = $result->fetch_assoc()) {
    $borrowedBooks[] = $row;
}

// Fix: Define $activeCount to remove the "Undefined variable" warnings
$activeCount = count($borrowedBooks);
?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <title>Dashboard | Bugo Rush Logistics</title>

  <style>
    /* Premium Color Palette Archetype: Midnight Deep & Cyber Neon */
    :root {
      --bg-midnight: #030816;
      --bg-card: #0a1126;
      --cyber-pink: #E11299;
      --cyber-neon: #ff29b3;
      --clean-white: #ffffff;
      --text-muted: #8b9bb4;
    }

    body { 
      background-color: var(--bg-midnight); 
      color: var(--clean-white);
      overflow-x: hidden; 
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
    }

    /* Glassmorphism Navigation */
    .navbar {
      background: rgba(10, 17, 38, 0.75) !important;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(225, 18, 153, 0.15);
      transition: all 0.3s ease;
    }
    
    .nav-link {
      color: var(--clean-white) !important;
      font-weight: 500;
      opacity: 0.85;
      transition: 0.2s;
    }
    .nav-link:hover {
      color: var(--cyber-pink) !important;
      opacity: 1;
    }

    /* Custom Buttons styling */
    .btn-cyber {
      background: linear-gradient(135deg, var(--cyber-pink), #a0076a);
      color: var(--clean-white);
      border: none;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(225, 18, 153, 0.4);
      transition: all 0.3s ease;
    }
    .btn-cyber:hover {
      background: linear-gradient(135deg, var(--cyber-neon), var(--cyber-pink));
      color: var(--clean-white);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(225, 18, 153, 0.6);
    }
    .btn-outline-cyber {
      border: 2px solid var(--cyber-pink);
      color: var(--clean-white);
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-outline-cyber:hover {
      background: var(--cyber-pink);
      color: var(--clean-white);
      box-shadow: 0 0 15px rgba(225, 18, 153, 0.4);
    }

    /* Account Dashboard Container Panel */
    .account-card { 
      background: var(--bg-card);
      border: 1px solid rgba(255, 255, 255, 0.05); 
      border-radius: 20px; 
      box-shadow: 0 12px 30px rgba(3, 8, 22, 0.5); 
      transition: 0.3s; 
    }
    .account-card:hover {
      border-color: rgba(225, 18, 153, 0.25);
    }

    /* Dark Mode Table Reset */
    .table {
      color: var(--clean-white) !important;
    }
    .table-dark-custom th {
      background-color: rgba(255, 255, 255, 0.04) !important;
      color: var(--text-muted) !important;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .table td {
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      padding: 16px 12px;
    }

    /* High-End Status Pill Badges */
    .status-badge { 
      padding: 6px 14px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 700; 
      display: inline-block;
      letter-spacing: 0.5px;
    }
    .status-overdue { background: rgba(220, 53, 69, 0.15); color: #ff5f6e; border: 1px solid rgba(220, 53, 69, 0.3); }
    .status-today { background: rgba(255, 193, 7, 0.15); color: #ffca2c; border: 1px solid rgba(255, 193, 7, 0.3); }
    .status-active { background: rgba(25, 135, 84, 0.15); color: #3cd070; border: 1px solid rgba(25, 135, 84, 0.3); }

    /* Back to Top Smooth Button */
    #backToTop {
      position: fixed; bottom: 30px; right: 30px; display: none; z-index: 99; border: none;
      background: var(--cyber-pink); color: var(--clean-white); width: 50px; height: 50px; border-radius: 50%;
      box-shadow: 0 4px 15px rgba(225, 18, 153, 0.4); transition: all 0.3s ease;
    }
    #backToTop:hover { background: var(--cyber-neon); transform: scale(1.1); box-shadow: 0 6px 20px rgba(225, 18, 153, 0.6); }
  </style>
</head>
<body id="go_home">

  <!-- Back to Top Button -->
  <button id="backToTop" title="Go to top"><i class="fa-solid fa-arrow-up"></i></button>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark py-3">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center fw-bold fs-4" href="user_page.php" style="letter-spacing: 0.5px;">
        <span style="color: var(--clean-white);">BUGO</span>
        <span style="color: var(--cyber-pink); margin-left: 5px;">RUSH</span>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
          <li class="nav-item"><a class="nav-link px-3 active" style="color: var(--cyber-pink) !important;" href="#my_account">Account</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="user_page.php">Home</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="home.php#services_part">Services</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="home.php#about_part">About</a></li>         
          <li class="nav-item ms-lg-2 w-100 w-lg-auto text-center mt-2 mt-lg-0">
            <a href="logout.php" class="btn btn-outline-cyber rounded-pill px-4 btn-sm w-100">Log Out</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Account Control Hub -->
  <section id="my_account" class="container py-5" style="margin-top: 100px; margin-bottom: 60px;">
    <div class="row">
        <div class="col-12 mb-5 text-center text-md-start">
            <h2 class="fw-bold fs-1"><i class="fa-solid fa-circle-user me-2" style="color: var(--cyber-pink);"></i>My Account Control Hub</h2>
            <p style="color: var(--text-muted);">Manage your active system schedules, transport listings, and user identification markers.</p>
        </div>

        <!-- Profile Detail Panel Component -->
        <div class="col-lg-4 mb-4">
            <div class="card account-card p-4 text-center">
                <div class="position-relative d-inline-block mx-auto mb-3 mt-2">
                    <!-- Premium Initial Avatar Integration using matching brand hex accents -->
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=E11299&color=fff&size=128" 
                        class="rounded-circle border border-4 border-dark shadow-sm" alt="User Profile Profile Frame">
                    <span class="position-absolute bottom-0 end-0 bg-success p-2 border border-2 border-dark rounded-circle" title="System Connected"></span>
                </div>
                <h4 class="fw-bold mb-1 text-white"><?php echo htmlspecialchars($username); ?></h4>
                <p style="color: var(--text-muted);" class="small mb-3"><?php echo htmlspecialchars($userEmail); ?></p>
                
                <hr style="border-top: 1px solid rgba(255,255,255,0.08);">
                
                <div class="d-grid gap-2 pt-2">
                    <a href="logout.php" class="btn btn-cyber fw-bold rounded-pill py-2">
                        <i class="fa-solid fa-power-off me-2"></i> Terminate Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Interactive Lists Panel Component -->
        <div class="col-lg-8">
            <div class="card account-card p-4">
                <div class="d-flex flex-sm-row flex-column justify-content-between align-items-sm-center align-items-start gap-3 mb-4">
                    <h5 class="fw-bold mb-0 text-white fs-4">Active Dispatches & Orders</h5>
                    <span class="badge rounded-pill px-3 py-2" style="background-color: var(--cyber-pink); font-weight: 600;"><?php echo $activeCount; ?> Records Open</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle bg-transparent mb-0">
                        <thead class="table-dark-custom">
                            <tr>
                                <th class="border-0">Book Title / Item</th>
                                <th class="border-0">Return / Due Date</th>
                                <th class="border-0 text-end">Status Breakdown</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($activeCount > 0): ?>
                                <?php foreach ($borrowedBooks as $row): 
                                    $today = new DateTime();
                                    $due_date = new DateTime($row['returnDate']);
                                    $interval = $today->diff($due_date);
                                    $days_left = (int)$interval->format("%r%a"); 
                                    
                                    $penalty = 0;
                                    if ($days_left < 0) {
                                        $status_class = "status-overdue";
                                        $display_text = abs($days_left) . " Days Overdue";
                                        $penalty = abs($days_left) * 50; 
                                    } elseif ($days_left == 0) {
                                        $status_class = "status-today";
                                        $display_text = "Due Today";
                                    } else {
                                        $status_class = "status-active";
                                        $display_text = $days_left . " Days Left";
                                    }
                                ?>
                                <tr>
                                    <td class="fw-bold text-white"><?php echo htmlspecialchars($row['bookTitle']); ?></td>
                                    <td style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($row['returnDate'])); ?></td>
                                    <td class="text-end">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <i class="fa-regular fa-clock me-1"></i> <?php echo $display_text; ?>
                                            <?php if($penalty > 0) echo " (₱" . number_format($penalty) . ")"; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5" style="color: var(--text-muted);">
                                        <i class="fa-solid fa-box-archive d-block fs-2 mb-3 opacity-25"></i>
                                        No active bookings, dispatches, or borrowed resources detected for this profile.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </section>

  <!-- Footer Layout -->
  <footer class="py-4 text-center border-top border-dark" style="background-color: var(--bg-midnight); margin-top: auto;">
      <p class="mb-0 small" style="color: var(--text-muted);">&copy; 2026 Bugo Rush Logistics Network Inc. All Rights Reserved.</p>
  </footer>

  <script>
    // Elegant Structural Scroll Button Backplane
    const topBtn = document.getElementById("backToTop");
    window.onscroll = function() {
      if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        topBtn.style.display = "block";
      } else {
        topBtn.style.display = "none";
      }
    };
    topBtn.onclick = function() {
      window.scrollTo({top: 0, behavior: 'smooth'});
    };
  </script>
</body>
</html>