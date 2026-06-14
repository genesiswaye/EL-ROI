<?php

require "middleware.php";
require "helper.php";
requireRole(['super_admin']);

include "../config/database.php";

try {

    $stmt = $pdo->query("
        SELECT
            a.admin_id,
            a.full_name,
            a.email,
            a.role,
            a.status,
            creator.full_name AS creator_name,
            a.created_at

        FROM admins a

        LEFT JOIN admins creator
        ON a.created_by = creator.admin_id

        ORDER BY a.created_at DESC
    ");

    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {

    die("Error loading admins");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    </style>
</head>

<body>
    <nav class="top-nav">


        <div class="nav-container">

            <div class="nav-content">

                <div class="logo">
                    StudentLancer Admin
                </div>

                <div class="nav-links" id="navLinks">

                    <a href="admin-dashboard.php"
                        class="nav-link <?= ($activePage == 'admin-dashboard') ? 'nav-link-active' : '' ?>">
                        Dashboard
                    </a>

                    <a href="withdrawal.php"
                        class="nav-link <?= ($activePage == 'withdrawal') ? 'nav-link-active' : '' ?>">
                        Withdrawals


                    </a>

                    <a href="dispute.php"
                        class="nav-link <?= ($activePage == 'dispute') ? 'nav-link-active' : '' ?>">
                        Disputes

                    </a>
                    <a href="companies.php"
                        class="nav-link <?= ($activePage == 'companies') ? 'nav-link-active' : '' ?>">
                        Company verification

                    </a>

                    <!-- <a href="analytics.php"
                        class="nav-link <?= ($activePage == 'analytics') ? 'nav-link-active' : '' ?>">
                        Analytics
                    </a> -->

                    <a href="audit-logs.php"
                        class="nav-link <?= ($activePage == 'audit_logs') ? 'nav-link-active' : '' ?>">
                        Audit Logs
                    </a>

                    <a href="fraud_flags.php"
                        class="nav-link <?= ($activePage == 'fraud_flags') ? 'nav-link-active' : '' ?>">
                        Fraud Flags



                    </a>

                    <a href="create-admin.php"
                        class="nav-link <?= ($activePage == 'create-admin') ? 'nav-link-active' : '' ?>">
                        Admins
                    </a>

                </div>

                <div class="admin-profile" style="display:none;">

                    <div class="avatar">

                        <?= strtoupper(substr($_SESSION['admin_name'], 0, 1)) ?>

                    </div>

                    <div class="admin-info">

                        <div class="admin-name">

                            <?= htmlspecialchars($_SESSION['admin_name']) ?>

                        </div>

                        <div class="admin-role">

                            <?= htmlspecialchars($_SESSION['role']) ?>

                        </div>

                    </div>

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