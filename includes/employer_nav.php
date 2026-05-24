<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

$unreadCount = 0;

if (isset($_SESSION['user_id'])) {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM notifications
        WHERE user_id = ?
        AND is_read = 0
    ");

    $stmt->execute([$_SESSION['user_id']]);

    $unreadCount = (int)$stmt->fetchColumn();
}

$role = null;

if (isset($_SESSION['user_id'])) {

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $role = $user['role'];
    }
}
?>
<style>
    .top-nav {
        background-color: #FFFFFF;
        border-bottom: 1px solid #E5E7EB;
        position: relative;
        z-index: 1000;
    }

    .nav-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .nav-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 64px;
    }

    .logo {
        font-size: 20px;
        font-weight: 600;
        color: #1F2933;
    }

    .nav-links {
        display: none;
        align-items: center;
        gap: 32px;
    }

    @media (min-width: 768px) {
        .nav-links {
            display: flex;
        }
    }

    .nav-link {
        font-size: 15px;
        color: #6B7280;
        text-decoration: none;
        transition: color 0.2s ease;

    }

    .nav-link:hover {
        color: #052a4dc9;


    }

    .nav-link-active {
        color: #052a4dc9;
        font-weight: 500;
        border-bottom: 2px solid #052a4dc9;
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
    }

    @media (max-width:1024px) {

        .menu-toggle {
            display: block;
        }

        .nav-links {
            position: absolute;
            top: 64px;
            left: 0;
            width: 100%;
            background: white;
            flex-direction: column;
            padding: 16px;
            gap: 16px;
            border-bottom: 1px solid #E5E7EB;
            display: none;
        }

        .nav-links.active {
            display: flex;
        }

        .nav-link {
            display: block;
            width: 100%;
            padding: 12px 16px;
        }

        .nav-link:hover {
            background-color: #052a4dc9;
            color: white;


        }

        .nav-link-active {
            border-bottom: none;
        }
    }

    /* Dropdown */

    .dropdown {
        position: relative;

    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        min-width: 180px;
        display: none;
        flex-direction: column;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        z-index: 1100;
    }

    .dropdown-menu a {
        padding: 10px 16px;
        font-size: 14px;
        color: #6B7280;
        text-decoration: none;
    }

    .dropdown-menu a:hover {
        background: #F3F4F6;
        color: #052a4dc9;
    }

    /* Show dropdown on hover (desktop) */

    .dropdown:hover .dropdown-menu {
        display: flex;
    }

    .notification-btn {
    position: relative;

    width: 46px;
    height: 46px;

    border-radius: 14px;

    background: #f8fafc;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #334155;

    transition: all 0.2s ease;
}

.notification-btn:hover {
    background: #eef2ff;

    color: #2563eb;

    transform: translateY(-2px);
}

.notification-badge {
    position: absolute;

    top: -6px;
    right: -6px;

    min-width: 22px;
    height: 22px;

    padding: 0 6px;

    border-radius: 999px;

    background: linear-gradient(
        135deg,
        #ef4444,
        #dc2626
    );

    color: white;

    font-size: 11px;
    font-weight: 700;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 3px solid white;

    box-shadow:
        0 4px 12px rgba(239,68,68,0.35);
}

    @media (max-width:1024px) {
        .dropdown {
            width: 100%;
        }

        .dropdown-menu {
            position: static;
            width: 100%;
            border: none;
            box-shadow: none;
        }

        .dropdown:hover .dropdown-menu {
            display: none;
        }

        .dropdown.active .dropdown-menu {
            display: flex;
        }

    }
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
</head>

<body>
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-content">

                <!-- Logo -->
                <div class="logo">CampusLink</div>
                <?php
                $base = "/EL-ROI"; // or your project root
                ?>

                <!-- Desktop Navigation -->

                <div class="nav-links" id="navLinks">
                    <a href="<?= $base ?>/dashboard/overview.php"
                        class="nav-link <?= ($activePage == 'overview') ? 'nav-link-active' : '' ?>">
                        Overview
                    </a>

                    <?php if ($role === 'student'): ?>
                        <a href="<?= $base ?>/jobs/browse_jobs.php"
                            class="nav-link <?= ($activePage == 'browse_jobs') ? 'nav-link-active' : '' ?>">
                            Browse Jobs
                        </a>

                    <?php endif; ?>

                    <a href="<?= $base ?>/jobs/post_job.php"
                        class="nav-link <?= ($activePage == 'post_job') ? 'nav-link-active' : '' ?>">
                        Post Job
                    </a>

                    <div class="dropdown">
                        <a href="#"
                            class="nav-link <?= ($activePage == 'my_jobs') ? 'nav-link-active' : '' ?>" style="padding-bottom: 3px;">
                            My Jobs
                        </a>
                        <div class="dropdown-menu">

                            <a href="<?= $base ?>/jobs/my_jobs.php">Open Jobs</a>

                            <a href="<?= $base ?>/jobs/jobs_in_progress.php">Jobs In Progress</a>

                            <a href="<?= $base ?>/jobs/completed_jobs.php">Completed Jobs</a>

                        </div>
                    </div>
                    <?php if ($role === 'student'): ?>
                        <a href="<?= $base ?>/applications/my_applications.php"
                            class="nav-link <?= ($activePage == 'my_applications') ? 'nav-link-active' : '' ?>">
                            Applications
                        </a>
                    <?php endif; ?>

                    <a href="<?= $base ?>/messages/messages.php"
                        class="nav-link <?= ($activePage == 'messages') ? 'nav-link-active' : '' ?>">
                        Messages
                    </a>

                    <a href="../wallet/dashboard.php"
                        class="nav-link <?= ($activePage == 'wallet') ? 'nav-link-active' : '' ?>">
                        Wallet
                    </a>
                    <a
                        href="../notifications/index.php"
                        class="nav-link <?= ($activePage == 'notifications') ? 'nav-link-active' : '' ?>">

                        <div class="notification-btn">

                            <svg width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>

                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>

                            </svg>

                            <?php if ($unreadCount > 0): ?>

                                <span class="notification-badge">

                                    <?= $unreadCount > 99 ? '99+' : $unreadCount ?>

                                </span>

                            <?php endif; ?>

                        </div>

                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="menuToggle" class="menu-toggle">

                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>

                </button>

            </div>
        </div>
    </nav>
    <script>
        const menuToggle = document.getElementById("menuToggle");
        const navLinks = document.getElementById("navLinks");

        menuToggle.addEventListener("click", () => {
            navLinks.classList.toggle("active");
        });
        document.querySelectorAll(".dropdown > a").forEach(link => {

            link.addEventListener("click", function(e) {

                if (window.innerWidth <= 1024) {
                    e.preventDefault();
                    this.parentElement.classList.toggle("active");
                }

            });

        });
    </script>
</body>

</html>