<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="company-profile-setup.css"></link>
  <title>StudentLancer – Company Profile Setup</title>
</head>
<body>

<!-- ── Top Nav ──────────────────────────────────────────────── -->
<nav class="topnav">
  <a href="#" class="logo">
    <div class="logo-mark">SL</div>
    <span class="logo-text">Student<span>Lancer</span></span>
  </a>
  <div class="nav-right">
    <span class="nav-step">Step 2 of 3 — Company Profile</span>
    <div class="nav-avatar">HR</div>
  </div>
</nav>

<!-- ── Page ─────────────────────────────────────────────────── -->
<div class="page">

  <!-- ══ LEFT PANEL ══════════════════════════════════════════ -->
  <aside class="left-panel">

    <!-- Illustration -->
    <div class="illus-card">
      <svg viewBox="0 0 280 200" xmlns="http://www.w3.org/2000/svg" style="max-width:100%;position:relative;z-index:1">
        <!-- Building -->
        <rect x="60" y="70" width="160" height="120" rx="8" fill="rgba(255,255,255,.15)"/>
        <rect x="80" y="90" width="30" height="30" rx="4" fill="rgba(255,255,255,.3)"/>
        <rect x="125" y="90" width="30" height="30" rx="4" fill="rgba(255,255,255,.3)"/>
        <rect x="170" y="90" width="30" height="30" rx="4" fill="rgba(255,255,255,.3)"/>
        <rect x="80" y="135" width="30" height="30" rx="4" fill="rgba(255,255,255,.2)"/>
        <rect x="125" y="135" width="30" height="55" rx="4" fill="rgba(255,255,255,.35)"/>
        <rect x="170" y="135" width="30" height="30" rx="4" fill="rgba(255,255,255,.2)"/>
        <!-- Roof line -->
        <rect x="50" y="65" width="180" height="10" rx="5" fill="rgba(255,255,255,.25)"/>
        <!-- Flag -->
        <line x1="140" y1="35" x2="140" y2="65" stroke="rgba(255,255,255,.5)" stroke-width="2"/>
        <polygon points="140,35 165,45 140,55" fill="rgba(255,255,255,.7)"/>
        <!-- Stars -->
        <circle cx="40" cy="50" r="4" fill="rgba(255,255,255,.4)"/>
        <circle cx="240" cy="40" r="5" fill="rgba(255,255,255,.35)"/>
        <circle cx="260" cy="90" r="3" fill="rgba(255,255,255,.3)"/>
        <circle cx="20" cy="120" r="3.5" fill="rgba(255,255,255,.3)"/>
        <!-- Student figure -->
        <circle cx="95" cy="168" r="10" fill="rgba(255,255,255,.7)"/>
        <rect x="87" y="180" width="16" height="20" rx="5" fill="rgba(255,255,255,.5)"/>
        <!-- Employer figure -->
        <circle cx="185" cy="168" r="10" fill="rgba(255,255,255,.7)"/>
        <rect x="177" y="180" width="16" height="20" rx="5" fill="rgba(255,255,255,.5)"/>
        <!-- Handshake line -->
        <path d="M105 178 Q140 195 175 178" stroke="rgba(255,255,255,.6)" stroke-width="2" fill="none" stroke-dasharray="4 3"/>
      </svg>
      <h2 class="illus-title">Build Your Company Profile</h2>
      <p class="illus-sub">A complete profile helps you attract top student talent and build credibility across the StudentLancer network.</p>
    </div>

    <!-- Completion Progress -->
    <div class="progress-card">
      <div class="progress-card-header">
        <span class="progress-label">Profile Completion</span>
        <span class="progress-pct" id="progressPct">20%</span>
      </div>
      <div class="progress-bar-bg">
        <div class="progress-bar-fill" id="progressFill"></div>
      </div>
      <div class="progress-steps">
        <div class="progress-step done-step">
          <div class="step-dot done">
            <svg viewBox="0 0 10 10" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2 5 4 7 8 3"/>
            </svg>
          </div>
          Account Registration
        </div>
        <div class="progress-step active-step">
          <div class="step-dot active"></div>
          Company Information
        </div>
        <div class="progress-step">
          <div class="step-dot"></div>
          Logo &amp; Branding
        </div>
        <div class="progress-step">
          <div class="step-dot"></div>
          Verification &amp; CAC
        </div>
        <div class="progress-step">
          <div class="step-dot"></div>
          Post First Job
        </div>
      </div>
    </div>

    <!-- Trust Stats -->
    <div class="trust-card">
      <div class="trust-card-title">Platform Stats</div>
      <div class="badges-grid">
        <div class="badge-item">
          <div class="badge-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="badge-num">12K+</div>
          <div class="badge-lbl">Student Talents</div>
        </div>
        <div class="badge-item">
          <div class="badge-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
          </div>
          <div class="badge-num">3.4K</div>
          <div class="badge-lbl">Jobs Posted</div>
        </div>
        <div class="badge-item">
          <div class="badge-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
          </div>
          <div class="badge-num">98%</div>
          <div class="badge-lbl">Satisfaction</div>
        </div>
        <div class="badge-item">
          <div class="badge-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="badge-num">4.9★</div>
          <div class="badge-lbl">Avg Rating</div>
        </div>
      </div>
    </div>

    <!-- Verification Status -->
    <div class="verify-card">
      <div class="verify-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <div class="verify-text">
        <h4>Verification Status</h4>
        <p>Submit your CAC number to get a verified badge. Verified companies get 3× more applicants.</p>
        <div class="verify-status">
          <span class="status-dot"></span>
          Pending Verification
        </div>
      </div>
    </div>

  </aside>

  <!-- ══ RIGHT PANEL ══════════════════════════════════════════ -->
  <main class="right-panel">

    <!-- Page Header -->
    <div class="section-head">
      <div class="section-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
          <rect x="2" y="7" width="20" height="14" rx="2"/>
          <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
        </svg>
      </div>
      <div>
        <h1>Company Profile Setup</h1>
        <p>Tell students who you are and why they should work with you.</p>
      </div>
    </div>

    <!-- ── Card 1: Branding ─────────────────────────────────── -->
    <div class="form-card">
      <div class="card-head">
        <div class="card-head-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
        <h2>Branding &amp; Visuals</h2>
        <span class="card-badge optional">Optional</span>
      </div>
      <div class="card-body">

        <!-- Banner -->
        <div class="banner-upload" id="bannerUpload" title="Upload company banner">
          <input type="file" accept="image/*" id="bannerInput" onchange="previewImage(this,'bannerPreview','bannerUpload')" />
          <img id="bannerPreview" class="banner-preview" alt="Banner preview" />
          <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
          <span class="banner-upload-label">Upload Company Banner</span>
          <span class="banner-upload-sub">Recommended 1200 × 300px · PNG, JPG · Max 5MB</span>
        </div>

        <!-- Logo + Name -->
        <div class="logo-row">
          <div class="logo-upload-wrap">
            <div class="logo-upload" id="logoUpload" title="Upload company logo">
              <input type="file" accept="image/*" id="logoInput" onchange="previewImage(this,'logoPreview','logoUpload')" />
              <img id="logoPreview" class="logo-preview" alt="Logo preview" />
              <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
              </svg>
              <span class="logo-upload-hint">Logo<br/>200×200px</span>
            </div>
          </div>
          <div class="name-field">
            <div class="field-group">
              <label class="field-label" for="companyName">
                Company Name <span class="required-star">*</span>
              </label>
              <input type="text" id="companyName" class="field-input" placeholder="e.g. TechNova Solutions Ltd."
                oninput="updateProgress()" />
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ── Card 2: Company Info ─────────────────────────────── -->
    <div class="form-card">
      <div class="card-head">
        <div class="card-head-icon purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <h2>Company Information</h2>
        <span class="card-badge required">Required</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">

        <div class="field-row cols-2">
          <!-- Industry -->
          <div class="field-group">
            <label class="field-label" for="industry">
              Industry <span class="required-star">*</span>
            </label>
            <select id="industry" class="field-input" onchange="updateProgress()">
              <option value="" disabled selected>Select industry…</option>
              <option>Technology &amp; Software</option>
              <option>FinTech &amp; Banking</option>
              <option>E-Commerce &amp; Retail</option>
              <option>Healthcare &amp; MedTech</option>
              <option>Education &amp; EdTech</option>
              <option>Media &amp; Entertainment</option>
              <option>Logistics &amp; Supply Chain</option>
              <option>Agriculture &amp; AgriTech</option>
              <option>Real Estate &amp; PropTech</option>
              <option>Marketing &amp; Advertising</option>
              <option>Consulting &amp; Professional Services</option>
              <option>Non-Profit &amp; NGO</option>
              <option>Manufacturing &amp; Engineering</option>
              <option>Other</option>
            </select>
          </div>

          <!-- Company Size -->
          <div class="field-group">
            <label class="field-label" for="companySize">Company Size</label>
            <select id="companySize" class="field-input">
              <option value="" disabled selected>No. of employees…</option>
              <option>1 – 10 (Micro)</option>
              <option>11 – 50 (Small)</option>
              <option>51 – 200 (Medium)</option>
              <option>201 – 1,000 (Large)</option>
              <option>1,000+ (Enterprise)</option>
            </select>
          </div>
        </div>

        <!-- Website -->
        <div class="field-group">
          <label class="field-label" for="website">
            Company Website <span class="required-star">*</span>
          </label>
          <div class="input-wrap">
            <span class="input-prefix">
              <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
            </span>
            <input type="url" id="website" class="field-input has-prefix" placeholder="https://yourcompany.com"
              oninput="updateProgress()" />
          </div>
        </div>

        <div class="field-row cols-2">
          <!-- Founded Year -->
          <div class="field-group">
            <label class="field-label" for="foundedYear">Founded Year</label>
            <input type="number" id="foundedYear" class="field-input" placeholder="e.g. 2019" min="1900" max="2026" />
          </div>

          <!-- Location -->
          <div class="field-group">
            <label class="field-label" for="location">Headquarters City</label>
            <div class="input-wrap">
              <span class="input-prefix">
                <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
              </span>
              <input type="text" id="location" class="field-input has-prefix" placeholder="e.g. Lagos, Nigeria" />
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ── Card 3: CAC Verification ────────────────────────── -->
    <div class="form-card">
      <div class="card-head">
        <div class="card-head-icon green">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <h2>Legal &amp; CAC Registration</h2>
        <span class="card-badge required">Required</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">

        <div class="cac-info">
          <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          Your CAC Registration Number is used to verify your business legitimacy. This information is kept confidential and used solely for platform verification. Verified companies receive a trusted badge visible to all students.
        </div>

        <div class="field-row cols-2">
          <div class="field-group">
            <label class="field-label" for="cacNumber">
              CAC Number <span class="required-star">*</span>
            </label>
            <div class="input-wrap">
              <input type="text" id="cacNumber" class="field-input" placeholder="e.g. RC-1234567"
                oninput="updateProgress()" maxlength="15" />
              <span class="input-suffix" id="cacCheck" style="display:none">
                <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="field-group">
            <label class="field-label" for="cacType">Registration Type</label>
            <select id="cacType" class="field-input">
              <option value="" disabled selected>Select type…</option>
              <option>Business Name</option>
              <option>Private Limited Company (Ltd)</option>
              <option>Public Limited Company (PLC)</option>
              <option>Incorporated Trustee (NGO)</option>
              <option>Limited Liability Partnership</option>
            </select>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label" for="cacDoc">Upload CAC Certificate (Optional)</label>
          <div class="banner-upload" style="height:90px" title="Upload CAC certificate">
            <input type="file" accept=".pdf,.jpg,.jpeg,.png" />
            <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <span class="banner-upload-label" style="font-size:12.5px">Click to upload CAC Certificate</span>
            <span class="banner-upload-sub">PDF, JPG or PNG · Max 10MB</span>
          </div>
        </div>

      </div>
    </div>

    <!-- ── Card 4: Description ──────────────────────────────── -->
    <div class="form-card">
      <div class="card-head">
        <div class="card-head-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/>
          </svg>
        </div>
        <h2>Company Description</h2>
        <span class="card-badge required">Required</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">

        <div class="field-group">
          <label class="field-label" for="companyDesc">
            About Your Company <span class="required-star">*</span>
          </label>
          <textarea id="companyDesc" class="field-input" maxlength="800"
            placeholder="Describe what your company does, your mission, and why students would love working with you…"
            oninput="updateProgress(); updateDescCount()"></textarea>
          <div class="field-hint">
            <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Minimum 80 characters recommended
            <span class="char-count" id="descCount">0 / 800</span>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label" for="companyTagline">Company Tagline</label>
          <input type="text" id="companyTagline" class="field-input"
            placeholder="e.g. Empowering Africa's digital future, one solution at a time." maxlength="100" />
        </div>

      </div>
    </div>

    <!-- ── Card 5: Social Links ─────────────────────────────── -->
    <div class="form-card">
      <div class="card-head">
        <div class="card-head-icon purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
          </svg>
        </div>
        <h2>Social &amp; Online Presence</h2>
        <span class="card-badge optional">Optional</span>
      </div>
      <div class="card-body">
        <div class="social-row">

          <div class="social-item">
            <div class="social-logo li">
              <svg viewBox="0 0 24 24" fill="white" width="16" height="16">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                <rect x="2" y="9" width="4" height="12"/>
                <circle cx="4" cy="4" r="2"/>
              </svg>
            </div>
            <input type="url" class="field-input" placeholder="linkedin.com/company/yourcompany" />
          </div>

          <div class="social-item">
            <div class="social-logo tw">
              <svg viewBox="0 0 24 24" fill="white" width="16" height="16">
                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
              </svg>
            </div>
            <input type="url" class="field-input" placeholder="twitter.com/yourcompany" />
          </div>

          <div class="social-item">
            <div class="social-logo gh">
              <svg viewBox="0 0 24 24" fill="white" width="16" height="16">
                <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
              </svg>
            </div>
            <input type="url" class="field-input" placeholder="github.com/yourcompany" />
          </div>

        </div>
      </div>
    </div>

    <!-- ── Action Row ───────────────────────────────────────── -->
    <div class="action-row">
      <button class="btn-preview" onclick="handlePreview()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        Preview
      </button>
      <button class="btn-save" onclick="handleSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/>
          <polyline points="7 3 7 8 15 8"/>
        </svg>
        Save Company Profile
      </button>
    </div>

  </main>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <div class="toast-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>
  <span id="toastMsg">Company profile saved successfully!</span>
</div>

<script>
  /* ── Image Preview ─────────────────────────────────────────── */
  function previewImage(input, previewId, wrapId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById(previewId);
      img.src = e.target.result;
      img.style.display = 'block';
      const wrap = document.getElementById(wrapId);
      wrap.querySelectorAll('svg, .banner-upload-label, .banner-upload-sub, .logo-upload-hint')
        .forEach(el => el.style.display = 'none');
    };
    reader.readAsDataURL(file);
  }

  /* ── Progress Calculation ──────────────────────────────────── */
  const FIELDS = ['companyName','industry','website','cacNumber','companyDesc'];

  function updateProgress() {
    const cac = document.getElementById('cacNumber').value.trim();
    if (cac.length >= 5) document.getElementById('cacCheck').style.display = 'flex';
    else document.getElementById('cacCheck').style.display = 'none';

    const filled = FIELDS.filter(id => {
      const el = document.getElementById(id);
      return el && el.value.trim().length > 0;
    }).length;

    // base: 20% for account, rest distributed
    const pct = 20 + Math.round((filled / FIELDS.length) * 80);
    document.getElementById('progressPct').textContent = pct + '%';
    document.getElementById('progressFill').style.width = pct + '%';

    // Update steps dynamically
    const steps = document.querySelectorAll('.progress-step');
    if (filled >= 1) {
      steps[2].classList.add('active-step');
      steps[2].querySelector('.step-dot').classList.add('active');
    }
    if (filled >= 3) {
      steps[3].classList.add('active-step');
      steps[3].querySelector('.step-dot').classList.add('active');
    }
  }

  /* ── Description Character Count ──────────────────────────── */
  function updateDescCount() {
    const len = document.getElementById('companyDesc').value.length;
    const el  = document.getElementById('descCount');
    el.textContent = `${len} / 800`;
    el.classList.toggle('warn', len > 720);
  }

  /* ── Toast Helper ──────────────────────────────────────────── */
  function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
  }

  /* ── Validate ──────────────────────────────────────────────── */
  function validate() {
    const required = [
      { id: 'companyName', label: 'Company Name' },
      { id: 'industry',    label: 'Industry' },
      { id: 'website',     label: 'Company Website' },
      { id: 'cacNumber',   label: 'CAC Registration Number' },
      { id: 'companyDesc', label: 'Company Description' },
    ];

    for (const { id, label } of required) {
      const el = document.getElementById(id);
      if (!el.value.trim()) {
        el.focus();
        el.style.borderColor = '#ef4444';
        el.style.boxShadow = '0 0 0 3px rgba(239,68,68,.12)';
        setTimeout(() => { el.style.borderColor = ''; el.style.boxShadow = ''; }, 2500);
        showToast(`⚠️ Please fill in: ${label}`);
        return false;
      }
    }
    return true;
  }

  /* ── Save ──────────────────────────────────────────────────── */
  function handleSave() {
    if (!validate()) return;

    const data = {
      companyName:  document.getElementById('companyName').value,
      industry:     document.getElementById('industry').value,
      website:      document.getElementById('website').value,
      cacNumber:    document.getElementById('cacNumber').value,
      description:  document.getElementById('companyDesc').value,
      size:         document.getElementById('companySize').value,
      founded:      document.getElementById('foundedYear').value,
      location:     document.getElementById('location').value,
      tagline:      document.getElementById('companyTagline').value,
    };

    console.log('Company Profile Saved:', data);
    showToast('✅ Company profile saved successfully!');

    // Update progress to 100%
    document.getElementById('progressPct').textContent = '100%';
    document.getElementById('progressFill').style.width = '100%';
    document.querySelectorAll('.step-dot').forEach(d => {
      d.classList.add('done');
      d.innerHTML = `<svg viewBox="0 0 10 10" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="2 5 4 7 8 3"/></svg>`;
    });
    document.querySelectorAll('.progress-step').forEach(s => {
      s.classList.add('done-step');
      s.classList.remove('active-step');
    });
  }

  /* ── Preview ───────────────────────────────────────────────── */
  function handlePreview() {
    const name = document.getElementById('companyName').value || 'Your Company';
    showToast(`👁 Previewing: ${name}`);
  }
</script>
</body>
</html>