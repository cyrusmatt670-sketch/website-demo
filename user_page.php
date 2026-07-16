<?php
session_start();
// Error handling for the modal
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

// Fetch real user name or use default fallback
$username = $_SESSION['name'] ?? 'Rider';
$userEmail = $_SESSION['email'] ?? 'delivery@bugorush.com';

unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

function showError($error) {
    return !empty($error) ? "<div class='alert alert-danger px-3 py-2 border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3'><i class='fa-solid fa-triangle-exclamation me-2'></i>$error</div>" : '';
}
?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <title>Bugo Rush | Quick Delivery Service</title>

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

    /* Cyber Search Input Layout */
    .search-wrapper {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 30px;
      padding: 4px 14px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .search-wrapper:focus-within {
      border-color: var(--cyber-pink);
      box-shadow: 0 0 12px rgba(225, 18, 153, 0.3);
      background: rgba(255, 255, 255, 0.08);
    }
    .search {
      outline: none;
      color: var(--clean-white);
      width: 160px;
      transition: width 0.3s ease;
    }
    .search::placeholder { color: var(--text-muted); }
    .search:focus { width: 220px; }

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

    /* Hero Section Card Glows */
    .hero-glow-box {
      position: relative;
    }
    .hero-glow-box::after {
      content: '';
      position: absolute;
      top: 10%; left: 10%; width: 80%; height: 80%;
      background: var(--cyber-pink);
      filter: blur(120px);
      opacity: 0.25;
      z-index: -1;
    }

    /* Modern Service Cards */
    .cat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 25px;
      margin-top: 40px;
    }
    .service-card {
      background: var(--bg-card);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 30px 20px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
      text-decoration: none !important;
      display: block;
    }
    .service-card:hover {
      transform: translateY(-8px);
      border-color: rgba(225, 18, 153, 0.4);
      box-shadow: 0 12px 30px rgba(3, 8, 22, 0.5), 0 0 20px rgba(225, 18, 153, 0.15);
    }
    .icon-box {
      width: 70px;
      height: 70px;
      margin: 0 auto 20px;
      background: rgba(225, 18, 153, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--cyber-pink);
      font-size: 1.8rem;
      transition: all 0.3s ease;
    }
    .service-card:hover .icon-box {
      background: var(--cyber-pink);
      color: var(--clean-white);
      box-shadow: 0 0 15px var(--cyber-pink);
      transform: scale(1.05);
    }
    .service-card h4 { color: var(--clean-white); font-weight: 600; font-size: 1.25rem; margin-bottom: 8px;}
    .service-card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0; }

    /* Glass Panels for About Section */
    .about-sec {
      background: linear-gradient(180deg, var(--bg-midnight), #060d22);
      padding: 100px 0;
      position: relative;
    }
    .panel-glass {
      background: rgba(10, 17, 38, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.04);
      border-radius: 24px;
      padding: 40px;
    }

    /* Back to Top Smooth Button */
    #backToTop {
      position: fixed; bottom: 30px; right: 30px; display: none; z-index: 99; border: none;
      background: var(--cyber-pink); color: var(--clean-white); width: 50px; height: 50px; border-radius: 50%;
      box-shadow: 0 4px 15px rgba(225, 18, 153, 0.4); transition: all 0.3s ease;
    }
    #backToTop:hover { background: var(--cyber-neon); transform: scale(1.1); box-shadow: 0 6px 20px rgba(225, 18, 153, 0.6); }
    
    .no-results { text-align: center; color: var(--text-muted); padding: 40px; display: none; font-size: 1.1rem; }
  </style>
</head>
<body id="go_home">

  <button id="backToTop" title="Go to top"><i class="fa-solid fa-arrow-up"></i></button>

  <nav class="navbar navbar-expand-lg fixed-top navbar-dark py-3">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center fw-bold fs-4" href="#go_home" style="letter-spacing: 0.5px;">
        <span style="color: var(--clean-white);">BUGO</span>
        <span style="color: var(--cyber-pink); margin-left: 5px;">RUSH</span>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
          <li class="nav-item"><a class="nav-link px-3" href="Myaccount.php">Account</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="#go_home">Home</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="#services_part">Services</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="#about_part">About</a></li>         

          <li class="nav-item mx-lg-2 my-2 my-lg-0">
            <div class="d-flex align-items-center search-wrapper">
                <input type="text" class="search border-0 bg-transparent" id="searchInput" placeholder="Search services..." />
                <i class="fa-solid fa-magnifying-glass text-muted ms-1"></i>
            </div>
          </li>
          <li class="nav-item ms-lg-2 w-100 w-lg-auto text-center mt-2 mt-lg-0">
            <a href="logout.php" class="btn btn-outline-cyber rounded-pill px-4 btn-sm w-100">Log Out</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container hero-glow-box" style="margin-top: 160px; margin-bottom: 100px;">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 text-center text-lg-start">
        <div class="badge bg-opacity-10 bg-info text-info px-3 py-2 rounded-pill mb-3 border border-info border-opacity-25" style="letter-spacing: 1px; font-weight: 600; font-size: 0.8rem;">
          <i class="fa-solid fa-bolt me-2 text-warning"></i>ULTRA-FAST LOGISTICS
        </div>
        <h2 class="display-4 fw-bold lh-base">Welcome back, <br><span style="background: linear-gradient(to right, var(--clean-white), var(--cyber-pink)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo htmlspecialchars($username); ?></span>!</h2>
        <p class="lead mt-3" style="color: var(--text-muted); max-width: 500px;">Track updates, coordinate drop-offs, and accelerate your dynamic schedule with premium city delivery infrastructure.</p>
        <div class="d-flex flex-sm-row flex-column gap-3 justify-content-center justify-content-lg-start mt-4">
          <a href="#services_part" class="btn btn-cyber btn-lg px-5 rounded-pill shadow-lg">Request Dispatch</a>
          <a href="Myaccount.php" class="btn btn-outline-secondary btn-lg px-4 rounded-pill text-white border-secondary border-opacity-50">View Dashboard</a>
        </div>
      </div>
      <div class="col-lg-6 text-center">
        <div class="p-4 rounded-4 bg-opacity-20 bg-dark inline-block shadow-lg border border-white border-opacity-5" style="background: radial-gradient(circle, #0e1938 0%, #04091a 100%);">
          <img src="images/design front" class="img-fluid rounded-4" alt="Bugo Rush Premium Identity" style="max-height: 280px; object-fit: contain;">
        </div>
      </div>
    </div>
  </div>

  <section class="container py-5" id="services_part">
    <div class="text-center mb-5">
      <h2 class="fw-bold fs-1">Our Core Services</h2>
      <p style="color: var(--text-muted);">Instant point-to-point fulfillment lines optimized dynamically.</p>
    </div>

    <div class="cat-grid">
      <a href="section.php?category=Express" class="service-card">
        <div class="icon-box"><i class="fa-solid fa-truck-fast"></i></div>
        <h4>Express Courier</h4>
        <p>Document & package deliveries inside localized operational bounds.</p>
      </a>
      
      <a href="section.php?category=Food" class="service-card">
        <div class="icon-box"><i class="fa-solid fa-motorcycle"></i></div>
        <h4>Food Fleet</h4>
        <p>Hot, insulation-shielded restaurant transport direct to hubs.</p>
      </a>
      
      <a href="section.php?category=Cargo" class="service-card">
        <div class="icon-box"><i class="fa-solid fa-box-open"></i></div>
        <h4>Bulk Cargo</h4>
        <p>Heavy merchant load allocation and distribution logistics.</p>
      </a>
      
      <a href="section.php?category=Secure" class="service-card">
        <div class="icon-box"><i class="fa-solid fa-shield-halved"></i></div>
        <h4>Secure Vault</h4>
        <p>High-value or delicate priority containment transfer operations.</p>
      </a>
    </div>
    <div id="noResults" class="no-results"><i class="fa-solid fa-magnifying-glass-blur me-2"></i>No service sectors match your tracking query.</div>
  </section>

  <div class="about-sec" id="about_part">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-5 text-center order-2 order-lg-1">
          <i class="fa-solid fa-map-location-dot text-opacity-10 text-white" style="font-size: 12rem; color: rgba(225,18,153, 0.15) !important;"></i>
        </div>
        <div class="col-lg-7 order-1 order-lg-2">
          <div class="panel-glass">
            <h6 style="color: var(--cyber-pink); font-weight: bold; letter-spacing: 2px;" class="text-uppercase mb-2">Operational Manifest</h6>
            <h2 class="fw-bold mb-4 text-white">ABOUT BUGO RUSH</h2>
            <p class="lead" style="color: rgba(255,255,255,0.85);">Engineered to optimize cross-city logistics. Bugo Rush empowers standard commercial networks and residential systems with hyper-reliable, on-demand shipping pipelines.</p>
            <p style="color: var(--text-muted); margin-bottom: 0;">We bridge the gap between regional storage facilities and localized drop points safely, utilizing cutting-edge routing applications to maintain top-tier service intervals.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="Contact_Us" class="py-5 text-center" style="background-color: #060c1d; border-top: 1px solid rgba(255,255,255,0.03);">
      <h3 class="fw-bold text-white mb-2">COMMUNICATION CHANNELS</h3>
      <p style="color: var(--text-muted);">System operational anomalies? Dispatch support networks are open at: <span class="fw-bold ms-1" style="color: var(--cyber-neon);">ops@bugorush.com</span></p>
  </div>

  <footer class="py-4 text-center border-top border-dark" style="background-color: var(--bg-midnight);">
      <p class="mb-0 small" style="color: var(--text-muted);">&copy; 2026 Bugo Rush Logistics Network Inc. All Rights Reserved.</p>
  </footer>

  <script>
    // Advanced Reactive Filtering Engine
    const searchInput = document.getElementById('searchInput');
    const serviceCards = document.querySelectorAll('.service-card');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();
      let visibleCount = 0;
      
      serviceCards.forEach((card) => {
        const header = card.querySelector('h4').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        
        if (header.includes(query) || description.includes(query)) {
          card.style.display = 'block';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });
      
      noResults.style.display = visibleCount === 0 && query !== '' ? 'block' : 'none';
      if (query.length > 0) {
        document.getElementById('services_part').scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });

    // Elegant Structural Scroll Button Backplane
    const topBtn = document.getElementById("backToTop");
    window.onscroll = function() {
      if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
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