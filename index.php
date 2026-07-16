<?php
session_start();
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

function showError($error) {
    return !empty($error) ? "<p class='text-danger small'>$error</p>" : '';
}

function getFormStyle($formName, $activeForm) {
    return $formName === $activeForm ? 'display: block;' : 'display: none;';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Hero Section Styling */
        .hero-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 50px 0;
        }

        .home-heading {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            color: #222;
        }

        .home-span {
            color: #C62828; /* The red from the image */
            display: block; /* Forces "PHILIPPINE SOUTHFIELD" to its own line like the image */
        }

        .para-home {
            font-size: 1.1rem;
            color: #666;
            max-width: 450px;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        /* The Red Button from the image */
        .btn-start-reading {
            background-color: #E53935;
            color: white;
            padding: 12px 40px;
            border-radius: 50px; /* Pill shape */
            font-weight: 500;
            border: none;
            transition: transform 0.2s, background-color 0.2s;
            box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
        }

        .btn-start-reading:hover {
            background-color: #C62828;
            color: white;
            transform: translateY(-2px);
        }

        /* Modal & Form Styling */
        .modal-content { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-box { padding: 40px; }
        .form-box h2 { font-weight: 700; color: #222; margin-bottom: 25px; }
        .form-box input, .form-box select { 
            width: 100%; margin-bottom: 15px; padding: 12px; 
            border: 1px solid #eee; border-radius: 8px; background: #fdfdfd;
        }
        .form-box button { 
            width: 100%; padding: 12px; background: #E53935; 
            color: white; border: none; border-radius: 8px; font-weight: 600; 
        }

        @media (max-width: 991px) {
            .home-heading { font-size: 2.5rem; }
            .hero-container { text-align: center; }
            .para-home { margin-left: auto; margin-right: auto; }
            .img-hero { margin-top: 40px; width: 100% !important; }
        }
    </style>
    <title>Delivery App</title>
</head>
<body>

<div class="container hero-container">
    <div class="row align-items-center">
        <div class="col-lg-6 col-md-12">
            <h1 class="home-heading">
                Welcome to 
                <span class="home-span">Delivery App</span>
            </h1>
            <p class="para-home">
                Explore our collection and find everything you need to bring your knowledge to the next level!
            </p>
            
            <button type="button" class="btn btn-start-reading" data-bs-toggle="modal" data-bs-target="#authModal">
                LOGIN
            </button>
        </div>

        <div class="col-lg-6 col-md-12 text-center">
            <img src="images/image 1.png" class="img-fluid img-hero" alt="E-Library Illustration" style="width: 90%; max-width: 550px;">
        </div>
    </div>
</div>

<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="form-box" id="login-form" style="<?= getFormStyle('login', $activeForm) ?>">
                <form action="login_register.php" method="POST">
                    <h2>Login</h2>
                    <?= showError($errors['login']) ?>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                    <p class="mt-4 text-center small">Don’t have an account? <a href="javascript:void(0)" onclick="showForm('register-form')" class="text-danger fw-bold">Register</a></p>
                </form>
            </div>

            <div class="form-box" id="register-form" style="<?= getFormStyle('register', $activeForm) ?>">
                <form action="login_register.php" method="POST">
                    <h2>Create Account</h2>
                    <?= showError($errors['register']) ?>
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="password" placeholder="Create Password" required>
                    <select name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Parent">Parent</option>
                    </select>
                    <button type="submit" name="register">Register</button>
                    <p class="mt-4 text-center small">Already have an account? <a href="javascript:void(0)" onclick="showForm('login-form')" class="text-danger fw-bold">Login</a></p>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    function showForm(formId) {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'none';
        document.getElementById(formId).style.display = 'block';
    }

    <?php if (!empty($errors['login']) || !empty($errors['register'])): ?>
        var myModal = new bootstrap.Modal(document.getElementById('authModal'));
        myModal.show();
    <?php endif; ?>
</script>

</body>
</html>