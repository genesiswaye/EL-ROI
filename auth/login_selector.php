<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <title>StudentLancer Login</title>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">

    <div class="text-center mb-8">
        <img
            src="../assets/studentLancerlogo.jpg"
            alt="StudentLancer"
            class="w-20 mx-auto mb-4">

        <h1 class="text-3xl font-bold text-gray-800">
            Welcome Back
        </h1>

        <p class="text-gray-500 mt-2">
            Choose how you want to sign in
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        <!-- Student & Lecturer -->

        <a
            href="login.php"
            class="border rounded-xl p-6 hover:shadow-lg transition hover:border-blue-500">

            <div class="flex items-center gap-4 mb-4">

                <div class="bg-blue-100 p-3 rounded-lg">
                    🎓
                </div>

                <div>
                    <h3 class="font-semibold text-lg">
                        Students & Lecturers
                    </h3>

                    <p class="text-sm text-gray-500">
                        Sign in with your university account
                    </p>
                </div>

            </div>

            <ul class="text-sm text-gray-600 space-y-2">

                <li>✓ Covenant University Students</li>
                <li>✓ Academic Staff</li>
                <li>✓ Google Authentication</li>

            </ul>

        </a>

        <!-- Company -->

        <a
            href="company_login.php"
            class="border rounded-xl p-6 hover:shadow-lg transition hover:border-purple-500">

            <div class="flex items-center gap-4 mb-4">

                <div class="bg-purple-100 p-3 rounded-lg">
                    🏢
                </div>

                <div>
                    <h3 class="font-semibold text-lg">
                        Companies
                    </h3>

                    <p class="text-sm text-gray-500">
                        Sign in to your verified company account
                    </p>
                </div>

            </div>

            <ul class="text-sm text-gray-600 space-y-2">

                <li>✓ Verified Organizations</li>
                <li>✓ Post Jobs & Projects</li>
                <li>✓ Hire Student Talent</li>

            </ul>

        </a>

    </div>

    <div class="mt-8 text-center text-sm text-gray-500">

        Don't have an account?

        <a
            href="register.php"
            class="text-purple-700 font-medium">
            Register Here
        </a>

    </div>

</div>

</body>
</html>