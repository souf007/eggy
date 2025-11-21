<?php
session_start();
// This line connects to your actual database defined in config.php.
// Ensure config.php creates a PDO object named $bdd.
include("config.php"); 

// For EGESTION.MA Only ALL RIGHT RESERVERD /////.

// --- Fetch settings for dynamic content ---
$cover_image_path = ''; // Initialize variable
try {
    // This query fetches the settings from your database.
    $settings_stmt = $bdd->query("SELECT * FROM settings WHERE id = 1");
    if ($settings_stmt) {
        $settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);
        // Check if a cover image is set and the file exists.
        if ($settings && isset($settings['cover']) && $settings['cover'] != "" && file_exists("uploads/" . $settings['cover'])) {
            $cover_image_path = "uploads/" . $settings['cover'];
        }
    }
} catch (Exception $e) {
    // If the settings table doesn't exist or there's an error, do nothing.
    $cover_image_path = 'images/default-cover.png'; // Fallback image
}


if(isset($_POST['username'])){
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

///////// BY THE BEST OF ALL TIMES SOUF - EGESTION.MA ///////////////////
    
    $stmt = $bdd->prepare("SELECT * FROM users WHERE email = :email AND password = :password AND trash = '1'");
    $stmt->execute(['email' => $email, 'password' => $password]);
    

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Password is correct, proceed with login.
        
        // Populate session variables
        $_SESSION['easybm_id'] = $row['id'];
        $_SESSION['easybm_fullname'] = $row['fullname'];
        $_SESSION['easybm_picture'] = $row['picture'];
        $_SESSION['easybm_phone'] = $row['phone'];
        $_SESSION['easybm_email'] = $row['email'];
        $_SESSION['easybm_password'] = $row['password'];
        $_SESSION['easybm_roles'] = $row['roles'];
        $_SESSION['easybm_companies'] = "0,".($row['companies']!=""?$row['companies']:0);
        $_SESSION['easybm_type'] = $row['type'];
        $_SESSION['easybm_superadmin'] = $row['superadmin'];

        // Handle "Remember Me" cookies
        if(isset($_POST['rememberme'])){
            $expiry = time() + (86400 * 2); // 2 days
            setcookie('rememberme', 'yes', $expiry);
            setcookie('id', $_SESSION['easybm_id'], $expiry);
        } else {
            $expiry = time() - 3600; // Expire now
            setcookie('rememberme', '', $expiry);
            setcookie('id', '', $expiry);
        }
        
        // Redirect based on user role
        $page = "login.php"; // Default page
        if(preg_match("#Consulter Tableau de bord#",$_SESSION['easybm_roles'])){	
            $page = "index.php";
        }
        // ... (add all other elseif conditions from your original code)
        
        header('Location: '.$page);
        exit();

    } else {
        $error = 'Login ou mot de passe est incorrect.';
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - eGestion</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --text-color-dark: #2c3e50;
            --text-color-light: #8a97a0;
            --card-bg: rgba(255, 255, 255, 0.6);
            --input-bg: rgba(224, 234, 252, 0.5);
            --white: #ffffff;
            --royal-blue: #4169e1;
            --orange: #ff8c00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(-45deg, #a8c0ff, #fdd3a2, #e0eafc, #fbc2eb);
            background-size: 400% 400%;
            animation: light-gradient-animation 25s ease infinite;
            color: var(--text-color-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }
        
        @keyframes light-gradient-animation {
            0%{background-position:0% 50%}
            50%{background-position:100% 50%}
            100%{background-position:0% 50%}
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            background: var(--card-bg);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        /* Left Side - Showcase */
        .left-section {
            flex: 1;
            background: url('<?php echo htmlspecialchars($cover_image_path); ?>') no-repeat center center;
            background-size: cover;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1); /* Subtle white glassy overlay */
        }

        /* Right Side - Form */
        .right-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .logo img {
            width: 220px; /* Increased logo size */
        }

        .form-container h2 {
            text-align: center;
            font-size: 1.7rem; /* Slightly reduced size for hierarchy */
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-color-dark);
        }
        
        .form-container p {
            text-align: center;
            margin-bottom: 35px;
            color: var(--text-color-light);
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            background: var(--input-bg);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            color: var(--text-color-dark);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .form-input::placeholder {
            color: var(--text-color-light);
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            background: var(--white);
            box-shadow: 0 0 15px rgba(58, 123, 213, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color-light);
            transition: color 0.3s ease;
        }
        
        .form-input:focus ~ .input-icon {
            color: var(--primary-color);
        }
        
        .options-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 25px;
            color: var(--text-color-light);
        }
        
        .remember-wrapper {
            display: flex;
            align-items: center;
        }
        
        .remember-wrapper input {
            margin-right: 8px;
            accent-color: var(--primary-color);
        }
        
        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color), var(--royal-blue), var(--orange));
            background-size: 300% auto;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0, 114, 255, 0.2);
        }
        
        .submit-btn:hover {
            background-position: right center;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.4);
        }
        
        .error-message {
            background: #ffebee;
            border: 1px solid #ffcdd2;
            color: #c62828;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
                max-width: 500px;
                background: transparent; /* Remove main container background on mobile */
                box-shadow: none;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }
            .left-section {
                display: none; /* Hide cover image on mobile */
            }
            .right-section {
                background: var(--card-bg); /* Apply glassy background to form section */
                border-radius: 20px;
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 0;
            }
            .login-container {
                border-radius: 0;
                height: 100vh;
                max-width: 100%;
            }
             .right-section {
                justify-content: center;
                border-radius: 0;
                height: 100%;
             }
        }

    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="login-container">
        <!-- Left Side -->
        <div class="left-section">
            <!-- The dynamic cover image is now the background of this div -->
        </div>
        <!-- Right Side -->
        <div class="right-section">
            <div class="form-container">
                <div class="logo">
                    <img src="/lm/images/logo.png" alt="eGestion Logo">
                </div>
                <h2>Bienvenue</h2>
                <p>Connectez-vous pour accéder à votre espace</p>
                
                <form method="post" action="" autocomplete="off">
                    <?php if(isset($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <input type="text" id="username" name="username" class="form-input" placeholder="Nom d'utilisateur" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Mot de passe" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>

                    <div class="options-wrapper">
                        <label class="remember-wrapper">
                            <input type="checkbox" name="rememberme"> Se souvenir de moi
                        </label>
                        <a href="#" class="forgot-link">Mot de passe oublié?</a>
                    </div>

                    <button type="submit" class="submit-btn">Se connecter</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Particles.js library for the background animation -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 40, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#888888" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false } },
                "size": { "value": 3, "random": true, "anim": { "enable": false } },
                "line_linked": { "enable": false },
                "move": { "enable": true, "speed": 1, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": false }, "onclick": { "enable": false }, "resize": true }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>
