<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="register.css">
    
    <title>Document</title>
    <style>
        *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #1F2933;
    margin-bottom: 0.5rem;
    font-size: 0.938rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.375rem;
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
    color: #1F2933;
    background-color: #FFFFFF;
    transition: all 0.2s;
}

.form-input::placeholder {
    color: #9CA3AF;
}

.form-input:focus {
    outline: none;
    border-color: #4B2E83;
    box-shadow: 0 0 0 3px rgba(75, 46, 131, 0.1);
}

.field-hint {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #6B7280;
    line-height: 1.5;
}
.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #6B7280;
}

.checkbox-label input[type="checkbox"] {
    margin-top: 0.25rem;
    width: 1rem;
    height: 1rem;
    cursor: pointer;
    accent-color: #4B2E83;
}

.link-text {
    color: #4B2E83;
    text-decoration: none;
}

.link-text:hover {
    text-decoration: underline;
}
.btn-primary {
    padding: 0.75rem 1.25rem;
    background-color: #4B2E83;
    color: #FFFFFF;
    border: none;
    border-radius: 0.375rem;
    font-weight: 500;
    cursor: pointer;
    transition: opacity 0.2s;
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
}

.btn-primary:hover {
    opacity: 0.9;
}

.btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(75, 46, 131, 0.2);
}

.btn-full {
    width: 100%;
}

    </style>
</head>

<body class="flex items-center justify-center h-screen">
    <div style="padding: 1rem;" class="bg-white rounded-lg shadow-md w-full max-w-md">
        <div style="margin-bottom: 1rem;" class="flex items-center justify-center">
            <img src="../assets/campuslink logo.jpg" alt="Logo for website" class="w-20 mb-4">
        </div>
       <div class="flex flex-col justify-center mt-6">
        <form action="">
            <div class="form-group">
                <label for="register-name" class="form-label">Company name</label>
                <input type="text" id="register-name" class="form-input" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label for="register-email" class="form-label">Email address</label>
                <input type="email" id="register-email" class="form-input" placeholder="you@university.edu" required>
            </div>


            <div class="form-group">
                <label for="register-password" class="form-label">Password</label>
                <input type="password" id="register-password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="confirm-password" class="form-label">Confirm password</label>
                <input type="password" id="confirm-password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" required>
                    <span>I agree to the <a href="#" class="link-text">Terms of Service</a> and <a href="#" class="link-text">Privacy Policy</a></span>
                </label>
            </div>

            <button type="submit" class="btn-primary btn-full">Create account</button>
        </form>
    </div>

</div>
    </div>
</body>
</html>