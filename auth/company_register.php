<?php

session_start();
require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $company_name = trim($_POST['company_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone_number']);
  $industry = trim($_POST['industry']);
  $company_size = trim($_POST['company_size']);
  $website = trim($_POST['website']);
  $address = trim($_POST['address']);
  $cac = trim($_POST['cac_number']);
  $contact_name = trim($_POST['contact_name']);
  $contact_position = trim($_POST['contact_position']);
  $description = trim($_POST['description']);

  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password !== $confirm_password) {

    $error = "Passwords do not match.";
  } else {

    $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
        ");

    $stmt->execute([$email]);

    if ($stmt->fetch()) {

      $error = "Email already exists.";
    } else {

      try {

        $pdo->beginTransaction();

        $hashedPassword =
          password_hash(
            $password,
            PASSWORD_DEFAULT
          );

        /*
                CREATE USER
                */

        $stmt = $pdo->prepare("
                    INSERT INTO users (
                        full_name,
                        email,
                        password,
                        role,
                        account_status
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        'company',
                        'pending'
                    )
                ");

        $stmt->execute([
          $company_name,
          $email,
          $hashedPassword
        ]);

        $user_id =
          $pdo->lastInsertId();

        /*
                CREATE WALLET
                */

        $stmt = $pdo->prepare("
                    INSERT INTO wallets (
                        user_id,
                        balance,
                        held_balance,
                        currency
                    )
                    VALUES (
                        ?,
                        0.00,
                        0.00,
                        'NGN'
                    )
                ");

        $stmt->execute([
          $user_id
        ]);

        /*
                CREATE COMPANY PROFILE
                */

        $stmt = $pdo->prepare("
                    INSERT INTO company_profiles (
                        user_id,
                        company_name,
                        industry,
                        website,
                        cac_number,
                        description,
                        verification_status
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        'pending'
                    )
                ");
        $stmt = $pdo->prepare("
                    INSERT INTO company_profiles (
                        user_id,
                        company_name,
                        industry,
                        website,
                        cac_number,
                        description,
                        verification_status,
                        phone_number,
                        company_size,
                        address,
                        contact_name,
                        contact_position
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        'pending',
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ");

        $stmt->execute([
          $user_id,
          $company_name,
          $industry,
          $website,
          $cac,
          $description,
          $phone,
          $company_size,
          $address,
          $contact_name,
          $contact_position
        ]);

        $pdo->commit();

        header(
          "Location: registration_pending.php"
        );

        exit();
      } catch (Exception $e) {

        $pdo->rollBack();

        $error =
          "Registration failed.";
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
  <link rel="stylesheet" href="company-register.css">
  <link rel="stylesheet" href="../dist/output.css">
  </link>
  <title>StudentLancer – Company Registration</title>

</head>

<body>

  <!-- Background blobs -->
  <div class="blob-wrap">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>

  <div class="page-wrap">

    <!-- ══ LEFT PANEL ════════════════════════════════════════════ -->
    <div class="left-panel">

      <!-- Logo -->
      <div class="brand">
        <div class="brand-mark">SL</div>
        <span class="brand-name">StudentLancer</span>
      </div>

      <!-- Illustration -->
      <div class="illus-wrap">
        <svg viewBox="0 0 380 220" xmlns="http://www.w3.org/2000/svg" style="max-width:100%;height:auto">
          <!-- Office building bg -->
          <rect x="60" y="60" width="260" height="150" rx="10" fill="rgba(255,255,255,.08)" />
          <!-- Windows -->
          <rect x="82" y="80" width="38" height="28" rx="4" fill="rgba(255,255,255,.22)" />
          <rect x="132" y="80" width="38" height="28" rx="4" fill="rgba(255,255,255,.3)" />
          <rect x="182" y="80" width="38" height="28" rx="4" fill="rgba(255,255,255,.18)" />
          <rect x="232" y="80" width="38" height="28" rx="4" fill="rgba(255,255,255,.25)" />
          <rect x="82" y="120" width="38" height="28" rx="4" fill="rgba(255,255,255,.15)" />
          <rect x="132" y="120" width="38" height="60" rx="4" fill="rgba(255,255,255,.32)" />
          <rect x="182" y="120" width="38" height="28" rx="4" fill="rgba(255,255,255,.2)" />
          <rect x="232" y="120" width="38" height="28" rx="4" fill="rgba(255,255,255,.15)" />
          <!-- Roof stripe -->
          <rect x="50" y="55" width="280" height="9" rx="4" fill="rgba(255,255,255,.18)" />
          <!-- Flagpole -->
          <line x1="190" y1="20" x2="190" y2="55" stroke="rgba(255,255,255,.45)" stroke-width="2" />
          <polygon points="190,20 218,30 190,40" fill="rgba(255,255,255,.65)" />
          <!-- Left student -->
          <circle cx="55" cy="168" r="13" fill="rgba(255,255,255,.72)" />
          <rect x="44" y="183" width="22" height="26" rx="7" fill="rgba(255,255,255,.5)" />
          <!-- Grad cap left -->
          <rect x="44" y="158" width="22" height="5" rx="2" fill="rgba(255,255,255,.5)" />
          <polygon points="55,154 66,161 55,165 44,161" fill="rgba(255,255,255,.55)" />
          <!-- Right professional -->
          <circle cx="325" cy="168" r="13" fill="rgba(255,255,255,.72)" />
          <rect x="314" y="183" width="22" height="26" rx="7" fill="rgba(255,255,255,.5)" />
          <!-- Briefcase -->
          <rect x="316" y="155" width="18" height="12" rx="3" fill="rgba(255,255,255,.42)" />
          <rect x="320" y="152" width="10" height="5" rx="2" fill="rgba(255,255,255,.3)" />
          <!-- Middle student -->
          <circle cx="190" cy="175" r="14" fill="rgba(255,255,255,.75)" />
          <rect x="178" y="191" width="24" height="18" rx="6" fill="rgba(255,255,255,.55)" />
          <!-- Connection lines -->
          <path d="M68 185 Q130 165 176 185" stroke="rgba(255,255,255,.45)" stroke-width="1.8" fill="none" stroke-dasharray="5 3" />
          <path d="M204 185 Q255 165 312 185" stroke="rgba(255,255,255,.45)" stroke-width="1.8" fill="none" stroke-dasharray="5 3" />
          <!-- Floating dots -->
          <circle cx="30" cy="50" r="5" fill="rgba(255,255,255,.35)" />
          <circle cx="350" cy="40" r="4" fill="rgba(255,255,255,.28)" />
          <circle cx="360" cy="120" r="3" fill="rgba(255,255,255,.22)" />
          <circle cx="20" cy="140" r="4" fill="rgba(255,255,255,.28)" />
          <!-- Stars -->
          <text x="100" y="45" font-size="14" fill="rgba(255,255,255,.55)">★</text>
          <text x="270" y="40" font-size="12" fill="rgba(255,255,255,.45)">★</text>
          <text x="310" y="75" font-size="10" fill="rgba(255,255,255,.35)">★</text>
        </svg>
      </div>

      <!-- Headline -->
      <div class="left-headline">
        <h1>Hire Verified<br />Campus Talent</h1>
        <p>Connect with skilled students, lecturers, and university communities through a secure AI-powered freelancing platform.</p>
      </div>

      <!-- Features -->
      <div class="features">
        <div class="feature-item">
          <div class="feature-check">
            <svg viewBox="0 0 14 14" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2 7 5 10 12 3" />
            </svg>
          </div>
          <div>
            <div class="feature-text">Verified University Users</div>
            <div class="feature-sub">All talent verified via institutional email</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-check">
            <svg viewBox="0 0 14 14" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2 7 5 10 12 3" />
            </svg>
          </div>
          <div>
            <div class="feature-text">Secure Escrow Payments</div>
            <div class="feature-sub">Funds held safely until delivery confirmed</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-check">
            <svg viewBox="0 0 14 14" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2 7 5 10 12 3" />
            </svg>
          </div>
          <div>
            <div class="feature-text">AI-Powered Matching</div>
            <div class="feature-sub">Smart candidate ranking and recommendations</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-check">
            <svg viewBox="0 0 14 14" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2 7 5 10 12 3" />
            </svg>
          </div>
          <div>
            <div class="feature-text">Transparent Hiring Process</div>
            <div class="feature-sub">Full audit trail and dispute resolution</div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="stat-row">
        <div class="stat-pill">
          <div class="stat-num">12K+</div>
          <div class="stat-lbl">Student Talents</div>
        </div>
        <div class="stat-pill">
          <div class="stat-num">480+</div>
          <div class="stat-lbl">Companies</div>
        </div>
        <div class="stat-pill">
          <div class="stat-num">98%</div>
          <div class="stat-lbl">Satisfaction</div>
        </div>
      </div>

    </div><!-- /left-panel -->

    <!-- ══ RIGHT PANEL ═══════════════════════════════════════════ -->
    <div class="right-panel">

      <!-- Header -->
      <div class="form-head">
        <div class="form-head-top">
          <h2 class="form-title">Create Company Account</h2>
          <div class="completion-badge" id="completionBadge">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
              <circle cx="8" cy="8" r="6" />
              <polyline points="8 5 8 8 10 9" />
            </svg>
            <span id="completionPct">0% complete</span>
          </div>
        </div>
        <p class="form-sub">Register your organization and start hiring campus talent.</p>
        <div class="completion-bar-bg">
          <div class="completion-bar-fill" id="completionFill"></div>
        </div>
      </div>

      <form id="regForm" method="POST">

        <!-- ─ Company Info ──────────────────────────────────── -->
        <div class="section-title">Company Information</div>
        <div class="form-grid">

          <!-- Company Name -->
          <div class="field">
            <label class="field-label" for="companyName">
              Company Name <span class="req">*</span>
            </label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="7" width="20" height="14" rx="2" />
                  <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                </svg>
              </span>
              <input type="text" id="companyName" name="company_name" class="field-input" placeholder="ABC Technologies Ltd"
                required oninput="trackField(this); validate(this)" />
              <span class="input-suffix" id="suf-companyName">
                <svg viewBox="0 0 16 16" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                  <polyline points="3 8 6 11 13 4" />
                </svg>
              </span>
            </div>
            <div class="field-error" id="err-companyName">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
                <circle cx="8" cy="8" r="6" />
                <line x1="8" y1="5" x2="8" y2="8" />
                <circle cx="8" cy="11" r=".5" fill="currentColor" />
              </svg>
              Please enter your company name.
            </div>
          </div>

          <div class="field-row-2">
            <!-- Company Email -->
            <div class="field">
              <label class="field-label" for="companyEmail">
                Company Email <span class="req">*</span>
              </label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                  </svg>
                </span>
                <input type="email" id="companyEmail" name="email" class="field-input" placeholder="contact@company.com"
                  required oninput="trackField(this); validate(this)" />
                <span class="input-suffix" id="suf-companyEmail"></span>
              </div>
              <div class="field-error" id="err-companyEmail">Enter a valid email address.</div>
            </div>

            <!-- Phone -->
            <div class="field">
              <label class="field-label" for="phone">
                Phone Number <span class="req">*</span>
              </label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 5.55 5.55l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                  </svg>
                </span>
                <input type="tel" id="phone" name="phone_number" class="field-input" placeholder="+234 xxx xxx xxxx"
                  required oninput="trackField(this); validate(this)" />
                <span class="input-suffix" id="suf-phone"></span>
              </div>
              <div class="field-error" id="err-phone">Enter a valid phone number.</div>
            </div>
          </div>

          <div class="field-row-2">
            <!-- Industry -->
            <div class="field">
              <label class="field-label" for="industry">
                Industry <span class="req">*</span>
              </label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                  </svg>
                </span>
                <select id="industry" name="industry" class="field-input" required onchange="trackField(this); validate(this)">
                  <option value="" disabled selected>Select industry…</option>
                  <option>Information Technology</option>
                  <option>Finance</option>
                  <option>Education</option>
                  <option>Engineering</option>
                  <option>Healthcare</option>
                  <option>Manufacturing</option>
                  <option>Consulting</option>
                  <option>Media &amp; Communications</option>
                  <option>Retail &amp; E-Commerce</option>
                  <option>Government Agency</option>
                  <option>NGO / Non-Profit</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="field-error" id="err-industry">Please select your industry.</div>
            </div>

            <!-- Company Size -->
            <div class="field">
              <label class="field-label" for="companySize">Company Size</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                  </svg>
                </span>
                <select id="companySize" name="company_size" class="field-input" onchange="trackField(this)">
                  <option value="" disabled selected>No. of employees…</option>
                  <option>1–10 Employees</option>
                  <option>11–50 Employees</option>
                  <option>51–200 Employees</option>
                  <option>201–500 Employees</option>
                  <option>500+ Employees</option>
                </select>
              </div>
            </div>
          </div>

          <div class="field-row-2">
            <!-- Website -->
            <div class="field">
              <label class="field-label" for="website">Company Website</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                  </svg>
                </span>
                <input type="text" id="website" name="website" class="field-input" placeholder="https://www.company.com"
                  oninput="trackField(this)" />
              </div>
            </div>

            <!-- Address -->
            <div class="field">
              <label class="field-label" for="address">Company Address</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                  </svg>
                </span>
                <input type="text" name="address" id="address" class="field-input" placeholder="Lagos, Nigeria"
                  oninput="trackField(this)" />
              </div>
            </div>
          </div>

        </div>

        <!-- ─ Legal ───────────────────────────────────────────── -->
        <div class="section-title">Legal &amp; Verification</div>
        <div class="form-grid">

          <!-- CAC -->
          <div class="field">
            <label class="field-label" for="cacNumber">
              CAC Registration Number <span class="req">*</span>
            </label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
              </span>
              <input type="text" name="cac_number" id="cacNumber" class="field-input" placeholder="RC1234567"
                required oninput="trackField(this); validate(this); updateCacBadge(this)" maxlength="14" />
              <span class="input-suffix" id="suf-cacNumber"></span>
            </div>
            <div class="field-error" id="err-cacNumber">Enter a valid CAC number (e.g. RC1234567).</div>
          </div>

          <!-- CAC badge -->
          <div class="cac-badge" id="cacBadge">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <circle cx="10" cy="10" r="8" />
              <line x1="10" y1="7" x2="10" y2="10" />
              <circle cx="10" cy="13" r=".5" fill="currentColor" />
            </svg>
            CAC verification is processed within 24–48 hrs after submission. Verified companies receive a trusted badge visible to all students.
          </div>

        </div>

        <!-- ─ Contact Person ──────────────────────────────────── -->
        <div class="section-title">Contact Person</div>
        <div class="form-grid">

          <div class="field-row-2">
            <!-- Contact Name -->
            <div class="field">
              <label class="field-label" for="contactName">
                Full Name <span class="req">*</span>
              </label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                </span>
                <input type="text" name="contact_name" id="contactName" class="field-input" placeholder="John Doe"
                  required oninput="trackField(this); validate(this)" />
                <span class="input-suffix" id="suf-contactName"></span>
              </div>
              <div class="field-error" id="err-contactName">Please enter the contact person's name.</div>
            </div>

            <!-- Contact Position -->
            <div class="field">
              <label class="field-label" for="contactPosition">
                Position / Title <span class="req">*</span>
              </label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                  </svg>
                </span>
                <input type="text" name="contact_position" id="contactPosition" class="field-input" placeholder="HR Manager"
                  required oninput="trackField(this); validate(this)" />
                <span class="input-suffix" id="suf-contactPosition"></span>
              </div>
              <div class="field-error" id="err-contactPosition">Please enter the contact's position.</div>
            </div>
          </div>

        </div>

        <!-- ─ About ───────────────────────────────────────────── -->
        <div class="section-title">Company Description</div>
        <div class="form-grid">

          <div class="field">
            <label class="field-label" for="description">
              About Your Company <span class="req">*</span>
            </label>
            <div class="input-wrap">
              <textarea id="description" name="description" class="field-input no-icon" rows="4"
                maxlength="500"
                placeholder="Tell students about your company, services, mission, and the types of opportunities you offer."
                required oninput="trackField(this); validate(this); updateDescCount()"></textarea>
            </div>
            <div class="field-hint">
              <span>Minimum 50 characters recommended</span>
              <span id="descCount" class="char-count">0 / 500</span>
            </div>
            <div class="field-error" id="err-description">Please add a company description.</div>
          </div>

        </div>

        <!-- ─ Security ────────────────────────────────────────── -->
        <div class="section-title">Account Security</div>
        <div class="form-grid">

          <!-- Password -->
          <div class="field">
            <label class="field-label" for="password">
              Password <span class="req">*</span>
            </label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
              <input type="password" name="password" id="password" class="field-input" placeholder="Create a strong password"
                required oninput="trackField(this); checkPasswordStrength(this.value); validate(this)" />
              <button type="button" class="pw-toggle" id="pwToggle1" onclick="togglePw('password','pwToggle1')" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
            <div class="pw-strength-wrap" id="strengthWrap" style="display:none">
              <div class="strength-bars">
                <div class="strength-bar" id="sb1"></div>
                <div class="strength-bar" id="sb2"></div>
                <div class="strength-bar" id="sb3"></div>
                <div class="strength-bar" id="sb4"></div>
              </div>
              <span class="strength-label" id="strengthLabel"></span>
            </div>
            <div class="field-error" id="err-password">Password must be at least 8 characters.</div>
          </div>

          <!-- Confirm Password -->
          <div class="field">
            <label class="field-label" for="confirmPw">
              Confirm Password <span class="req">*</span>
            </label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
              <input type="password" name="confirm_password" id="confirmPw" class="field-input" placeholder="Re-enter your password"
                required oninput="trackField(this); validate(this)" />
              <button type="button" class="pw-toggle" id="pwToggle2" onclick="togglePw('confirmPw','pwToggle2')" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
            <div class="field-error" id="err-confirmPw">Passwords do not match.</div>
          </div>

        </div>

        <!-- ─ Terms ───────────────────────────────────────────── -->
        <div style="margin-top:18px">
          <div class="terms-row">
            <div class="custom-checkbox" id="termsBox" onclick="toggleTerms()">
              <input type="checkbox" id="termsCheck" tabindex="-1" />
              <svg viewBox="0 0 12 12" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="2 6 5 9 10 3" />
              </svg>
            </div>
            <p class="terms-text">
              I agree to the StudentLancer
              <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.
              I confirm that the information provided is accurate and that I am authorised to register on behalf of this organisation.
            </p>
          </div>
          <div class="field-error" id="err-terms" style="margin-top:6px">You must agree to the terms to continue.</div>
        </div>

        <!-- ─ Submit ──────────────────────────────────────────── -->
        <button type="submit" class="btn-primary" id="submitBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
          </svg>
          Create Company Account
        </button>

        <!-- Divider -->
        <div class="divider">or</div>

        <!-- Google -->
        <!-- <button type="button" class="btn-google" onclick="showToast('Google sign-in coming soon!','info')">
        <svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
      </button> -->

        <!-- Security Notice -->
        <div class="security-notice">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
          <span>Your organization information will be reviewed by our trust &amp; safety team before full platform access is granted. This typically takes 24–48 hours.</span>
        </div>

        <!-- Footer -->
        <p class="form-footer">
          Already have an account? <a href="#">Sign In</a>
        </p>

      </form>

    </div><!-- /right-panel -->
  </div><!-- /page-wrap -->

  <!-- Toast -->
  <div class="toast" id="toast">
    <span class="toast-dot" id="toastDot"></span>
    <span id="toastMsg"></span>
  </div>

  <script>
    /* ── Tracked required fields ───────────────────────────────── */
    const REQUIRED = ['companyName', 'companyEmail', 'cacNumber', 'industry',
      'contactName', 'contactPosition', 'phone', 'description',
      'password', 'confirmPw'
    ];
    const filledFields = new Set();

    /* ── Progress ──────────────────────────────────────────────── */
    function updateProgress() {
      const total = REQUIRED.length; // +1 for terms
      const terms = document.getElementById('termsCheck').checked ? 1 : 0;
      const filled = filledFields.size + terms;
      const pct = Math.round((filled / total) * 100);
      document.getElementById('completionFill').style.width = pct + '%';
      document.getElementById('completionPct').textContent = pct + '% complete';
    }

    function trackField(el) {
      if (el.value.trim()) filledFields.add(el.id);
      else filledFields.delete(el.id);
      updateProgress();
    }

    /* ── Validation ────────────────────────────────────────────── */
    function showError(id, show) {
      const err = document.getElementById('err-' + id);
      if (err) err.classList.toggle('visible', show);
    }

    function setInputState(el, state) {
      el.classList.remove('valid', 'invalid');
      if (state) el.classList.add(state);
      const suf = document.getElementById('suf-' + el.id);
      if (suf) {
        if (state === 'valid') {
          suf.innerHTML = `<svg viewBox="0 0 16 16" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><polyline points="3 8 6 11 13 4"/></svg>`;
          suf.classList.add('visible');
        } else {
          suf.innerHTML = '';
          suf.classList.remove('visible');
        }
      }
    }

    function validate(el) {
      const id = el.id;
      const val = el.value.trim();
      let ok = true;

      if (id === 'website') {
    ok =
        val === '' ||
        /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/.*)?$/i.test(val);
}
    else if (id === 'cacNumber') {
      ok = /^[A-Za-z]{2}\d{5,10}$/.test(val.replace(/\s/g, ''));
    } else if (id === 'phone') {
      ok = /^[\+\d\s\-\(\)]{7,}$/.test(val);
    } else if (id === 'password') {
      ok = val.length >= 8;
    } else if (id === 'confirmPw') {
      ok = val === document.getElementById('password').value && val !== '';
    } else if (id === 'industry') {
      ok = val !== '';
    } else {
      ok = val.length > 0;
    }

    if (val === '') {
      setInputState(el, null);
      showError(id, false);
      return false;
    }

    setInputState(el, ok ? 'valid' : 'invalid');
    showError(id, !ok);
    return ok;
    }

    /* ── Password strength ─────────────────────────────────────── */
    function checkPasswordStrength(pw) {
      const wrap = document.getElementById('strengthWrap');
      if (!pw) {
        wrap.style.display = 'none';
        return;
      }
      wrap.style.display = 'flex';

      let score = 0;
      if (pw.length >= 8) score++;
      if (/[A-Z]/.test(pw)) score++;
      if (/[0-9]/.test(pw)) score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;

      const levels = [{
          label: 'Weak',
          cls: 'weak'
        },
        {
          label: 'Fair',
          cls: 'fair'
        },
        {
          label: 'Good',
          cls: 'good'
        },
        {
          label: 'Strong',
          cls: 'strong'
        },
      ];
      const clsMap = ['', 'weak', 'fair', 'good', 'strong'];

      for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('sb' + i);
        bar.className = 'strength-bar ' + (i <= score ? clsMap[score] : '');
      }

      document.getElementById('strengthLabel').textContent =
        score > 0 ? levels[score - 1].label : '';
      document.getElementById('strengthLabel').style.color = ['', '#ef4444', '#f59e0b', '#3b82f6', '#22c55e'][score];
    }

    /* ── Password toggle ───────────────────────────────────────── */
    function togglePw(fieldId, toggleId) {
      const input = document.getElementById(fieldId);
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    /* ── Description char count ────────────────────────────────── */
    function updateDescCount() {
      const len = document.getElementById('description').value.length;
      const el = document.getElementById('descCount');
      el.textContent = len + ' / 500';
      el.style.color = len > 450 ? '#ef4444' : '';
    }

    /* ── Terms checkbox ────────────────────────────────────────── */
    function toggleTerms() {
      const box = document.getElementById('termsBox');
      const check = document.getElementById('termsCheck');
      check.checked = !check.checked;
      box.classList.toggle('checked', check.checked);
      showError('terms', false);
      updateProgress();
    }

    /* ── CAC badge ─────────────────────────────────────────────── */
    function updateCacBadge(el) {
      const badge = document.getElementById('cacBadge');
      const val = el.value.trim();
      const ok = /^[A-Za-z]{2}\d{5,10}$/.test(val.replace(/\s/g, ''));
      if (ok) {
        badge.style.background = '#f0fdf4';
        badge.style.borderColor = '#86efac';
        badge.style.color = '#166534';
        badge.innerHTML = `
        <svg viewBox="0 0 20 20" fill="none" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <path d="M10 18s6-3 6-7.5V4l-6-2-6 2v6.5c0 4.5 6 7.5 6 7.5z"/>
          <polyline points="7 10 9 12 13 8"/>
        </svg>
        CAC number format recognised. Your registration will be verified within 24–48 hrs.`;
      } else {
        badge.style.background = '#fffbeb';
        badge.style.borderColor = '#fde68a';
        badge.style.color = '#92400e';
        badge.innerHTML = `
        <svg viewBox="0 0 20 20" fill="none" stroke="#d97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <circle cx="10" cy="10" r="8"/>
          <line x1="10" y1="7" x2="10" y2="10"/>
          <circle cx="10" cy="13" r=".5" fill="currentColor"/>
        </svg>
        CAC verification is processed within 24–48 hrs. Verified companies receive a trusted badge visible to all students.`;
      }
    }

    /* ── Submit ────────────────────────────────────────────────── */
    function handleSubmit(e) {
      e.preventDefault();
      let allOk = true;

      REQUIRED.forEach(id => {
        const el = document.getElementById(id);
        if (!validate(el)) allOk = false;
      });

      if (!document.getElementById('termsCheck').checked) {
        showError('terms', true);
        allOk = false;
      }

      if (!allOk) {
        const firstErr = document.querySelector('.field-input.invalid, .field-error.visible');
        if (firstErr) firstErr.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
        showToast('Please fix the highlighted errors before continuing.', 'error');
        return;
      }

      /* Simulate success */
      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.innerHTML = `
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      Creating account…`;

      setTimeout(() => {
        btn.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        Account Created Successfully!`;
        btn.style.background = 'linear-gradient(135deg,#22c55e,#16a34a)';
        document.getElementById('completionFill').style.width = '100%';
        document.getElementById('completionPct').textContent = '100% complete';
        showToast('🎉 Company account created! Redirecting to dashboard…', 'success');
      }, 2000);
    }

    /* ── Toast ─────────────────────────────────────────────────── */
    function showToast(msg, type = 'success') {
      const toast = document.getElementById('toast');
      const dot = document.getElementById('toastDot');
      document.getElementById('toastMsg').textContent = msg;
      dot.className = 'toast-dot ' + (type === 'error' ? 'error' : type === 'info' ? '' : 'success');
      dot.style.background = type === 'error' ? '#f87171' : type === 'info' ? '#60a5fa' : '#22c55e';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 4000);
    }
  </script>
</body>

</html>