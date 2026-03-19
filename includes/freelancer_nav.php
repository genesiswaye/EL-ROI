<style>
    .top-nav {
    background-color: #FFFFFF;
    border-bottom: 1px solid #E5E7EB;
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
    border-bottom:2px solid #052a4dc9;
}
.menu-toggle{
    display:none;
    background:none;
    border:none;
    cursor:pointer;
}

@media (max-width:1024px){

    .menu-toggle{
        display:block;
    }

    .nav-links{
        position:absolute;
        top:64px;
        left:0;
        width:100%;
        background:white;
        flex-direction:column;
        padding:16px;
        gap:16px;
        border-bottom:1px solid #E5E7EB;
        display:none;
    }

    .nav-links.active{
        display:flex;
    }
    .nav-link{
    display:block;
    width:100%;
    padding:12px 16px;
}
    .nav-link:hover {
    background-color: #052a4dc9;
    color: white;
   
   
}
    .nav-link-active{
        border-bottom: none;
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

            <!-- Desktop Navigation -->
            <div class="nav-links" id="navLinks">
                <a href="overview.php"
class="nav-link <?= ($activePage == 'overview') ? 'nav-link-active' : '' ?>">
Overview
</a>

<a href="browse_jobs.php"
class="nav-link <?= ($activePage == 'browse_jobs') ? 'nav-link-active' : '' ?>">
Browse Jobs
</a>
<a href="post_job.php"
class="nav-link <?= ($activePage == 'post_job') ? 'nav-link-active' : '' ?>">
Post Job
</a>

<a href="my_jobs.php"
class="nav-link <?= ($activePage == 'my_jobs') ? 'nav-link-active' : '' ?>">
My Jobs
</a>

<a href="applications.php"
class="nav-link <?= ($activePage == 'applications') ? 'nav-link-active' : '' ?>">
Applications
</a>

<a href="messages.php"
class="nav-link <?= ($activePage == 'messages') ? 'nav-link-active' : '' ?>">
Messages
</a>

<a href="wallet.php"
class="nav-link <?= ($activePage == 'wallet') ? 'nav-link-active' : '' ?>">
Wallet
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
</script>
</body>
</html>