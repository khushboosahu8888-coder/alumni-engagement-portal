<?php
session_start();
require_once 'includes/db.php'; 

$error = "";

// LOGIN PROCESS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check email exists
    $stmt = $conn->prepare("SELECT id, name, email, user_password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($id, $name, $email_db, $hashed_pass, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_pass)) {

            $_SESSION['user_id'] = $id;
            $_SESSION['role']    = $role;
            $_SESSION['name']    = $name;

            // Redirect based on role
            if ($role === "admin") {
                header("Location: adminDashboard.php");
            } elseif ($role === "student") {
                header("Location: studentDashboard.php");
            } elseif ($role === "alumni") {
                header("Location: alumniDashboard.php");
            } else {
                $error = "Invalid user role.";
            }
            exit;

        } else {
            $error = "Incorrect password!";
        }

    } else {
        $error = "No account found with this email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #009e9a, #005f60);
            font-family: "Poppins", sans-serif;
            overflow: hidden;
        }
        .bubble {
            position: absolute;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }
        .bubble:nth-child(1) { top: 10%; left: 15%; animation-duration: 7s; }
        .bubble:nth-child(2) { bottom: 12%; right: 20%; animation-duration: 9s; }
        .bubble:nth-child(3) { top: 50%; right: 10%; animation-duration: 11s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-35px); }
        }

        .login-box {
            width: 360px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            text-align: center;
            animation: fadeUp 0.7s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-box h2 {
            color: white;
            margin-bottom: 25px;
            font-size: 26px;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.75);
            font-size: 15px;
        }
        .login-box input:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(0, 255, 255, 0.8);
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #007e7c;
            color: white;
            font-size: 17px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-box button:hover {
            background: #00a6a3;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.8);
            transform: scale(1.03);
        }

        .links {
            margin-top: 15px;
            font-size: 14px;
            color: #e7ffff;
        }
        .links a {
            color: #fff;
            text-decoration: underline;
        }

        .error-box {
            background: #ffdddd;
            padding: 10px;
            border-radius: 8px;
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
       
        .back-home {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 18px;
            background: rgba(255,255,255,0.25);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            backdrop-filter: blur(6px);
            transition: 0.3s;
        }
        .back-home:hover {
            background: rgba(255,255,255,0.45);
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-box">
    <h2>Enter Login Details</h2>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="links">
        Don’t have an account? <a href="register.php">Register here</a>
    </div>

    <a href="index.php" class="back-home">⬅ Back to Home</a>
</div>

</body>
</html>
