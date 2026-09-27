<?php
// index.php
require_once __DIR__ . '/includes/helpers.php'; // starts session
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>College Alumni Engagement Portal</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* ===== GLOBAL ===== */
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #14b8a6, #ffffff 50%, #ccfbf1);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ===== HEADER ===== */
header h1 {
  text-align: center;
  font-size: 2.6rem;
  padding: 25px 15px 10px;
  font-weight: 700;
  color: #0f766e;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ===== HERO CARD ===== */
.hero {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 10px;
}

.hero-card {
  background: rgba(255, 255, 255, 0.36);
  backdrop-filter: blur(12px);
  border-radius: 22px;
  padding: 35px 30px 40px;
  max-width: 620px;
  width: 90%;
  text-align: center;
  box-shadow: 0 20px 45px rgba(20,184,166,0.25);
  animation: fadeUp 0.9s ease;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(25px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ===== ICON ===== */
.hero img {
  width: 135px;
  margin-bottom: 18px;
  filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));
}

/* ===== TAGLINE ===== */
.tagline {
  background: linear-gradient(90deg, #14b8a6, #0d9488);
  color: #ffffff;
  padding: 8px 28px;
  border-radius: 25px;
  font-weight: 600;
  font-size: 1rem;
  display: inline-block;
  margin-bottom: 18px;
}

/* ===== TEXT ===== */
.hero h3 {
  font-size: 1.2rem;
  color: #134e4a;
  margin-bottom: 26px;
  line-height: 1.6;
}

/* ===== BUTTONS ===== */
.btn-custom {
  width: 210px;
  padding: 14px;
  border-radius: 12px;
  font-size: 1.45rem;
  font-weight: 700;
  border: none;
  margin: 8px;
  transition: all 0.3s ease;
}

.btn-login {
  background: linear-gradient(135deg, #14b8a6, #0d9488);
  color: #ffffff;
}

.btn-login:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 12px 25px rgba(20,184,166,0.4);
}

.btn-register {
  background: linear-gradient(135deg, #f0fdfa, #99f6e4);
  color: #065f46;
  border: 2px solid #14b8a6;
}

.btn-register:hover {
  background: #ccfbf1;
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 12px 25px rgba(13,148,136,0.25);
}

/* ===== DEMO ACCOUNTS ===== */
.demo-accounts {
  margin-top: 22px;
  padding: 18px;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid rgba(13, 148, 136, 0.25);
  border-radius: 15px;
  text-align: left;
}

.demo-accounts h4 {
  text-align: center;
  color: #0f766e;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 5px;
}

.demo-note {
  text-align: center;
  color: #4b5563;
  font-size: 0.78rem;
  margin-bottom: 12px;
}

.demo-account {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 9px;
  padding: 9px 12px;
  margin-bottom: 8px;
  border-left: 4px solid #14b8a6;
}

.demo-account strong {
  display: block;
  color: #0f766e;
  font-size: 0.88rem;
  margin-bottom: 3px;
}

.demo-account span {
  display: block;
  color: #374151;
  font-size: 0.75rem;
  line-height: 1.5;
  word-break: break-word;
}

.demo-register-note {
  text-align: center;
  color: #4b5563;
  font-size: 0.75rem;
  margin: 10px 0 0;
}

/* ===== ABOUT LINK ===== */
.about-link {
  margin-top: 22px;
}

.about-link a {
  background: #97f1e2c2;
  color: #0f766e;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.6rem;
  display: inline-block;
  transition: all 0.25s ease;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.about-link a:hover {
  background: #89f3f3ff;
  transform: scale(1.1);
}

/* ===== FOOTER ===== */
footer {
  text-align: center;
  padding: 14px 10px;
  font-size: 0.9rem;
  color: #134e4a;
  background: rgba(88, 87, 87, 0.11);
  backdrop-filter: blur(8px);
}
</style>
</head>

<body>

<header>
  <h1>WELCOME to our <br> College Alumni Engagement Portal</h1>
</header>

<div class="hero">
  <div class="hero-card">

    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135810.png" alt="Alumni Icon">

    <div class="tagline">Connect • Share • Grow</div>

    <h3>
      Where alumni experience meets student ambition to create shared opportunities.
    </h3>

      <div>
  <a href="login.php" class="btn btn-custom btn-login">Login</a>
  <a href="register.php" class="btn btn-custom btn-register">Register</a>
</div>

<!-- ===== DEMO ACCOUNTS ===== -->
<div class="demo-accounts">

  <h4>🔑 Demo Accounts to Login</h4>

  <p class="demo-note">
    Use these credentials to explore different user roles.
  </p>

  <div class="demo-account">
    <strong>👨‍💼 Admin</strong>
    <span>Email: admin1@gmail.com</span>
    <span>Password: admin@123</span>
  </div>

  <div class="demo-account">
    <strong>🎓 Alumni</strong>
    <span>Email: raj@gmail.com</span>
    <span>Password: raj@123</span>
  </div>

  <div class="demo-account">
    <strong>👨‍🎓 Student</strong>
    <span>Email: rahul@gmail.com</span>
    <span>Password: rahul@123</span>
  </div>

  <p class="demo-register-note">
    All preloaded accounts contain demo data. You can also register a new account.
  </p>

</div>

      <div class="about-link">
      <a href="about.html">About Us</a>
      </div>

  </div>
</div>

<footer>
  © 2025 All Rights Reserved | Designed by Khushboo Sahu
</footer>

</body>
</html>
