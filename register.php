<?php
require_once "includes/db.php";
require_once "includes/helpers.php";

$error = "";
$success = "";

// FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF CHECK
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF Token");
    }

    // Common Fields
    $role     = $_POST['role'];
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate role
    if (!in_array($role, ['student', 'alumni'])) {
        $error = "Please select a valid role.";
    } else {
        // CHECK IF EMAIL ALREADY EXISTS
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered!";
        } else {

            // INSERT INTO USERS TABLE
            $stmt = $conn->prepare("INSERT INTO users (name, email, user_password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                // INSERT INTO STUDENT PROFILE
                if ($role === "student") {
                    $course = $_POST['course'];
                    $study_year   = $_POST['study_year'];

                    $s = $conn->prepare("INSERT INTO student_profile (user_id, course, study_year) VALUES (?, ?, ?)");
                    $s->bind_param("iss", $user_id, $course, $study_year);
                    $s->execute();
                }

                // INSERT INTO ALUMNI PROFILE
                if ($role === "alumni") {
                    $batch    = $_POST['batch'];
                    $company  = $_POST['company'];
                    $position = $_POST['position'];
                    $bio      = $_POST['bio'];

                    $a = $conn->prepare("INSERT INTO alumni_profile (user_id, batch_year, company, position, bio) 
                                         VALUES (?, ?, ?, ?, ?)");
                    $a->bind_param("issss", $user_id, $batch, $company, $position, $bio);
                    $a->execute();
                }

                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Try again!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>

    <style>
        /* BACKGROUND */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background: linear-gradient(135deg, #009e9a, #005f60);
            font-family: "Poppins", sans-serif;
            overflow-y: auto;
        }

        /* Floating circles */
        .bubble {
            position: fixed;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
            z-index: -1;
        }

        .bubble:nth-child(1) { top: 10%; left: 15%; animation-duration: 7s; }
        .bubble:nth-child(2) { bottom: 12%; right: 20%; animation-duration: 9s; }
        .bubble:nth-child(3) { top: 50%; right: 10%; animation-duration: 11s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-35px); }
        }

        /* GLASS CARD */
        .register-box {
            width: 400px;
            padding: 30px;
            margin: 50px 0;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            animation: fadeUp 0.7s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-box h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
        }

        /* INPUTS */
        .register-box input,
        .register-box select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.75);
            font-size: 15px;
        }

        .register-box input:focus,
        .register-box select:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(0,255,255,0.8);
        }

        /* BUTTON */
        .register-box button {
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

        .register-box button:hover {
            background: #00a6a3;
            box-shadow: 0 0 12px rgba(0,255,255,0.8);
            transform: scale(1.03);
        }

        /* LOGIN LINK */
        .links {
            margin-top: 15px;
            text-align: center;
            color: #e7ffff;
            font-size: 14px;
        }

        .links a {
            color: #ffffff;
            text-decoration: underline;
            transition: 0.3s;
        }

        .links a:hover {
            color: #d0ffff;
        }

        /* BACK HOME */
        .back-home {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: white;
            text-decoration: none;
            padding: 10px;
            background: rgba(255,255,255,0.25);
            border-radius: 8px;
            backdrop-filter: blur(6px);
            transition: 0.3s;
        }

        .back-home:hover {
            background: rgba(255,255,255,0.45);
            transform: scale(1.05);
        }

        /* ERROR & SUCCESS */
        .msg {
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .error { background: #ff4b4b; color: white; }
        .success { background: #4caf50; color: white; }
    </style>

</head>

<body>

    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="register-box">

        <h2>Create Your Account</h2>

        <?php if ($error): ?>
            <div class="msg error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="msg success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <select name="role" required onchange="showFields()">
                <option value="">Select: Are you Alumni or Student?</option>
                <option value="student">Student</option>
                <option value="alumni">Alumni</option>
            </select>

            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Create Password" required>

            <!-- STUDENT -->
            <div id="studentFields" style="display:none;">
                <input type="text" name="course" placeholder="Course (BCA, BBA, etc)">
                <input type="text" name="study_year" placeholder="Year of Study">
            </div>

            <!-- ALUMNI -->
            <div id="alumniFields" style="display:none;">
                <input type="text" name="batch" placeholder="Batch (2018–2021)">
                <input type="text" name="company" placeholder="Company Name">
                <input type="text" name="position" placeholder="Position">
                <input type="text" name="bio" placeholder="Bio">
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="links">
            Already have an account? <a href="login.php">Login here</a>
        </div>

        <a href="index.php" class="back-home">⬅ Back to Home</a>
    </div>

    <script>
        function showFields() {
            let role = document.querySelector("select[name='role']").value;

            document.getElementById("studentFields").style.display =
                role === "student" ? "block" : "none";

            document.getElementById("alumniFields").style.display =
                role === "alumni" ? "block" : "none";
        }
    </script>

</body>
</html>
