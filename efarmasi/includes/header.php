<?php
include_once 'auth.php';
$userInfo = getUserInfo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eFarmasi - Platform Konsultasi Obat Online</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <div class="logo">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <span class="brand-name">eFarmasi - RSUD Bukittinggi</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Beranda</a></li>
                <li><a href="about.html" class="nav-link">Tentang Kami</a></li>
                <li><a href="articles.html" class="nav-link">Artikel</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="consultation.php" class="nav-link">Konsultasi</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin.php" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <span class="user-name"><?php echo htmlspecialchars($userInfo['name']); ?></span>
                        <div class="dropdown">
                            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.html" class="btn-login">Masuk</a></li>
                    <li><a href="register.html" class="btn-register">Daftar</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>