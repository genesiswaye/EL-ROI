<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="register.css">
    
    <title>Document</title>
    
</head>

<body class="flex items-center justify-center h-screen">
    <div style="padding: 2rem;" class="bg-white rounded-lg shadow-md w-full max-w-md">
        <div class="flex items-center justify-center">
            <img src="../assets/studentLancerlogo.jpg" alt="Logo for website" class="w-20">
        </div>
        <h2 class="text-left text-[#1F2933] text-[2rem] mb-2 font-semibold">Create an account</h2>
        <p style="margin-bottom: 2rem; margin-top:0.5rem;" class="text-[#6B7280] text-[1rem] mb-3">Join CampusLink to get started</p>

        <form action="">
            <div>
                <label for="role" class="mt-4 font-semibold">I am a</label>
                <div class="role-selector">
                    <label class="role-option">
                        <input type="radio" name="role" value="student" checked onchange="toggleStudentFields()">
                        <a href="login.php" class="role-card">
                            <svg class="role-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <span class="role-label">Student</span>
                        </a>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="lecturer" onchange="toggleStudentFields()">
                        <a href="login.php" class="role-card">
                            <svg class="role-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span class="role-label">Lecturer</span>
                        </a>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="company" onchange="toggleStudentFields()">
                        <a href="company_register.php" class="role-card">
                            <svg class="role-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
                                <path d="M9 22v-4h6v4"></path>
                                <path d="M8 6h.01"></path>
                                <path d="M16 6h.01"></path>
                                <path d="M12 6h.01"></path>
                                <path d="M12 10h.01"></path>
                                <path d="M12 14h.01"></path>
                                <path d="M16 10h.01"></path>
                                <path d="M16 14h.01"></path>
                                <path d="M8 10h.01"></path>
                                <path d="M8 14h.01"></path>
                            </svg>
                            <span class="role-label">Company</span>
                        </a>
                    </label>
                </div>
            </div>
        </form>
    </div>
</body>
</html>