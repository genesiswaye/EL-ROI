<?php

session_start();

require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id'])) {
  die("Unauthorized");
}

$employer_id = $_SESSION['user_id'];

if (!isset($_GET['application_id'])) {
  die("Application missing");
}

$application_id = (int) $_GET['application_id'];

$stmt = $pdo->prepare("
SELECT
    applications.id,
    applications.student_id,

    jobs.id AS job_id,
    jobs.title,

    users.full_name,
    users.rating,
    users.completed_jobs,

    student_profiles.department

FROM applications

JOIN jobs
ON applications.job_id = jobs.id

JOIN users
ON applications.student_id = users.id

LEFT JOIN student_profiles
ON student_profiles.user_id = users.id

WHERE applications.id = ?
");

$stmt->execute([$application_id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
  die("Invalid application");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $rating = (int) ($_POST['rating'] ?? 0);
  $comment = trim($_POST['comment'] ?? '');

  if ($rating < 1 || $rating > 5) {
    die("Invalid rating");
  }

  $pdo->beginTransaction();

  try {

    /*
        Prevent duplicate review
        */

    $stmt = $pdo->prepare("
        SELECT id
        FROM reviews
        WHERE job_id = ?
        AND reviewer_id = ?
        ");

    $stmt->execute([
      $student['job_id'],
      $employer_id
    ]);

    if ($stmt->fetch()) {
      throw new Exception("Review already submitted");
    }

    /*
        Insert review
        */

    $stmt = $pdo->prepare("
        INSERT INTO reviews
        (
            job_id,
            reviewer_id,
            reviewee_id,
            rating,
            comment
        )
        VALUES (?, ?, ?, ?, ?)
        ");

    $stmt->execute([
      $student['job_id'],
      $employer_id,
      $student['student_id'],
      $rating,
      $comment
    ]);

    /*
        Update student's rating
        */

    $stmt = $pdo->prepare("
        SELECT
            AVG(rating) AS avg_rating,
            COUNT(*) AS total_reviews
        FROM reviews
        WHERE reviewee_id = ?
        ");

    $stmt->execute([
      $student['student_id']
    ]);

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $avg_rating = round($stats['avg_rating'], 2);
    $total_reviews = (int)$stats['total_reviews'];

    /*
        completed jobs +1
        */

    $stmt = $pdo->prepare("
        UPDATE users
        SET
        rating = ?,
        total_reviews = ?
        WHERE id = ?
        ");

    $stmt->execute([
      $avg_rating,
      $total_reviews,
      $student['student_id']
    ]);

    // require_once "../AI/run_recommendations.php";

    // runRecommendations();

    $pdo->commit();

    header(
      "Location: ../messages/messages.php?application_id=" .
        $application_id .
        "&review=success"
    );
    exit();
  } catch (Exception $e) {

    $pdo->rollBack();

    die($e->getMessage());
  }
}
$name = $student['full_name'] ?? 'Student';

$words = preg_split('/\s+/', trim($name));

$initials = '';

foreach ($words as $word) {

  $initials .= mb_strtoupper(
    mb_substr($word, 0, 1)
  );

  if (mb_strlen($initials) >= 2) {
    break;
  }
}
?>
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>StudentLancer – Rate Freelancer Modal</title>
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --blue-50: #eff6ff;
      --blue-100: #dbeafe;
      --blue-500: #3b82f6;
      --blue-600: #2563eb;
      --blue-700: #1d4ed8;
      --purple-500: #8b5cf6;
      --purple-600: #7c3aed;
      --purple-100: #ede9fe;
      --navy: #0f172a;
      --slate-50: #f8fafc;
      --slate-100: #f1f5f9;
      --slate-200: #e2e8f0;
      --slate-300: #cbd5e1;
      --slate-400: #94a3b8;
      --slate-500: #64748b;
      --slate-600: #475569;
      --slate-700: #334155;
      --slate-800: #1e293b;
      --white: #ffffff;
      --grad: linear-gradient(135deg, var(--blue-600), var(--purple-600));
      --grad-soft: linear-gradient(135deg, #dbeafe 0%, #ede9fe 100%);
      --shadow-xl: 0 20px 60px rgba(15, 23, 42, .18), 0 4px 16px rgba(37, 99, 235, .10);
      --radius-lg: 20px;
      --radius-md: 12px;
      --radius-sm: 8px;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* Demo Trigger */
    .demo-bg {
      text-align: center;
    }

    .demo-label {
      color: rgba(255, 255, 255, .5);
      font-size: 13px;
      margin-bottom: 12px;
      letter-spacing: .05em;
      text-transform: uppercase;
    }

    .open-btn {
      background: var(--grad);
      color: var(--white);
      border: none;
      padding: 14px 32px;
      border-radius: 50px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(37, 99, 235, .4);
      transition: transform .2s, box-shadow .2s;
    }

    .open-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(37, 99, 235, .55);
    }

    /* Overlay */
    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .72);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 16px;
      opacity: 0;
      pointer-events: none;
      transition: opacity .3s ease;
    }

    .overlay.active {
      opacity: 1;
      pointer-events: all;
    }

    /* Modal */
    .modal {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl);
      width: 100%;
      max-width: 580px;
      max-height: 92vh;
      overflow-y: auto;
      transform: translateY(24px) scale(.97);
      transition: transform .35s cubic-bezier(.34, 1.56, .64, 1), opacity .3s ease;
      opacity: 0;
      scrollbar-width: thin;
      scrollbar-color: var(--slate-200) transparent;
    }

    .modal::-webkit-scrollbar {
      width: 6px;
    }

    .modal::-webkit-scrollbar-thumb {
      background: var(--slate-200);
      border-radius: 99px;
    }

    .overlay.active .modal {
      transform: translateY(0) scale(1);
      opacity: 1;
    }

    /* Header */
    .modal-header {
      background: var(--grad);
      border-radius: var(--radius-lg) var(--radius-lg) 0 0;
      padding: 36px 32px 28px;
      text-align: center;
      position: relative;
    }

    .close-btn {
      position: absolute;
      top: 16px;
      right: 16px;
      background: rgba(255, 255, 255, .18);
      border: none;
      color: var(--white);
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 18px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .2s;
    }

    .close-btn:hover {
      background: rgba(255, 255, 255, .32);
    }

    .check-icon {
      width: 64px;
      height: 64px;
      background: rgba(255, 255, 255, .2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      border: 3px solid rgba(255, 255, 255, .5);
    }

    .modal-header h1 {
      color: var(--white);
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .modal-header p {
      color: rgba(255, 255, 255, .82);
      font-size: 13.5px;
      line-height: 1.6;
      max-width: 400px;
      margin: 0 auto;
    }

    /* Body */
    .modal-body {
      padding: 24px 28px 28px;
      display: flex;
      flex-direction: column;
      gap: 22px;
    }

    /* Illustration */
    .illustration-wrap {
      background: var(--grad-soft);
      border-radius: var(--radius-md);
      padding: 24px;
      text-align: center;
    }

    /* Student Card */
    .student-card {
      border: 1.5px solid var(--slate-200);
      border-radius: var(--radius-md);
      padding: 18px;
      display: flex;
      align-items: center;
      gap: 16px;
      background: var(--slate-50);
    }

    .avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--grad);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      font-weight: 700;
      color: var(--white);
      flex-shrink: 0;
      border: 3px solid var(--white);
      box-shadow: 0 2px 10px rgba(37, 99, 235, .25);
    }

    .student-info {
      flex: 1;
      min-width: 0;
    }

    .student-name {
      font-size: 16px;
      font-weight: 700;
      color: var(--slate-800);
    }

    .student-dept {
      font-size: 12.5px;
      color: var(--slate-500);
      margin: 2px 0 6px;
    }

    .job-title {
      font-size: 13px;
      color: var(--blue-600);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .student-meta {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 6px;
      flex-shrink: 0;
    }

    .badge-complete {
      background: #dcfce7;
      color: #166534;
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 50px;
    }

    .stat-pill {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: var(--slate-600);
    }

    /* Stars */
    .rating-section {
      text-align: center;
    }

    .rating-label {
      font-size: 14px;
      font-weight: 600;
      color: var(--slate-700);
      margin-bottom: 14px;
    }

    .stars-row {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 10px;
    }

    .star {
      cursor: pointer;
      transition: transform .15s;
      line-height: 1;
      background: none;
      border: none;
      padding: 0;
    }

    .star svg {
      width: 42px;
      height: 42px;
      transition: fill .15s, stroke .15s;
      fill: var(--slate-200);
      stroke: var(--slate-300);
      stroke-width: 1.5;
    }

    .star.active svg,
    .star.hovered svg {
      fill: #f59e0b;
      stroke: #f59e0b;
    }

    .star:hover {
      transform: scale(1.18);
    }

    .rating-text {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--blue-600);
      min-height: 20px;
    }

    /* Textarea */
    .review-wrap label {
      display: block;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--slate-700);
      margin-bottom: 8px;
    }

    .review-textarea {
      width: 100%;
      min-height: 110px;
      border: 1.5px solid var(--slate-200);
      border-radius: var(--radius-sm);
      padding: 12px 14px;
      font-size: 14px;
      font-family: inherit;
      color: var(--slate-700);
      resize: vertical;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      line-height: 1.6;
    }

    .review-textarea:focus {
      border-color: var(--blue-500);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
    }

    .review-textarea::placeholder {
      color: var(--slate-400);
    }

    .char-counter {
      text-align: right;
      font-size: 11.5px;
      color: var(--slate-400);
      margin-top: 5px;
    }

    .char-counter.warn {
      color: #ef4444;
    }

    /* Tags */
    .tags-label {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--slate-700);
      margin-bottom: 10px;
    }

    .tags-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .tag {
      background: var(--slate-100);
      color: var(--slate-600);
      border: 1.5px solid var(--slate-200);
      border-radius: 50px;
      padding: 6px 14px;
      font-size: 12.5px;
      font-weight: 500;
      cursor: pointer;
      transition: all .18s;
      user-select: none;
    }

    .tag:hover {
      border-color: var(--blue-500);
      color: var(--blue-600);
      background: var(--blue-50);
    }

    .tag.selected {
      background: var(--grad);
      color: var(--white);
      border-color: transparent;
      box-shadow: 0 2px 8px rgba(37, 99, 235, .22);
    }

    /* Notice */
    .notice-card {
      background: var(--grad-soft);
      border-radius: var(--radius-md);
      padding: 16px 18px;
      display: flex;
      gap: 14px;
      align-items: flex-start;
    }

    .notice-icons {
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex-shrink: 0;
    }

    .notice-icon {
      width: 34px;
      height: 34px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .notice-icon.ai {
      background: rgba(37, 99, 235, .15);
    }

    .notice-icon.trust {
      background: rgba(124, 58, 237, .15);
    }

    .notice-text h4 {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--slate-800);
      margin-bottom: 5px;
    }

    .notice-text p {
      font-size: 12.5px;
      color: var(--slate-600);
      line-height: 1.6;
    }

    /* Buttons */
    .modal-actions {
      display: flex;
      gap: 10px;
      flex-direction: column;
    }

    .btn-primary {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: var(--radius-sm);
      background: var(--grad);
      color: var(--white);
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(37, 99, 235, .35);
      transition: transform .2s, box-shadow .2s, opacity .2s;
    }

    .btn-primary:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(37, 99, 235, .45);
    }

    .btn-primary:disabled {
      opacity: .55;
      cursor: not-allowed;
    }

    .btn-secondary {
      width: 100%;
      padding: 12px;
      border: 1.5px solid var(--slate-300);
      border-radius: var(--radius-sm);
      background: var(--white);
      color: var(--slate-600);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: border-color .2s, color .2s, background .2s;
    }

    .btn-secondary:hover {
      border-color: var(--blue-500);
      color: var(--blue-600);
      background: var(--blue-50);
    }

    /* Footer */
    .modal-footer {
      padding: 0 28px 20px;
      text-align: center;
      font-size: 12px;
      color: var(--slate-400);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    /* Success */
    .success-state {
      display: none;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 48px 32px;
      gap: 16px;
    }

    .success-state.visible {
      display: flex;
    }

    .success-ring {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: pop .4s cubic-bezier(.34, 1.56, .64, 1);
    }

    @keyframes pop {
      from {
        transform: scale(.6);
        opacity: 0
      }

      to {
        transform: scale(1);
        opacity: 1
      }
    }

    .success-state h2 {
      font-size: 20px;
      font-weight: 700;
      color: var(--slate-800);
    }

    .success-state p {
      font-size: 14px;
      color: var(--slate-500);
      max-width: 340px;
      line-height: 1.65;
    }

    .updated-rating {
      background: var(--grad-soft);
      border-radius: var(--radius-md);
      padding: 14px 24px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      font-weight: 600;
      color: var(--slate-700);
    }

    .close-success {
      width: 100%;
      max-width: 320px;
      padding: 13px;
      border: none;
      border-radius: var(--radius-sm);
      background: var(--grad);
      color: var(--white);
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(37, 99, 235, .3);
      transition: transform .2s, box-shadow .2s;
    }

    .close-success:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(37, 99, 235, .4);
    }

    /* Responsive */
    @media (max-width: 520px) {
      .modal-body {
        padding: 18px 18px 22px;
        gap: 18px;
      }

      .modal-header {
        padding: 28px 20px 22px;
      }

      .modal-header h1 {
        font-size: 19px;
      }

      .student-card {
        flex-wrap: wrap;
      }

      .student-meta {
        flex-direction: row;
        align-items: center;
      }

      .modal-footer {
        padding: 0 18px 18px;
      }

      .stars-row {
        gap: 5px;
      }

      .star svg {
        width: 36px;
        height: 36px;
      }
    }
  </style>
</head>

<body>

  <div class="demo-bg">
    <p class="demo-label">StudentLancer Platform Preview</p>
    <button class="open-btn" onclick="openModal()">Mark Job as Completed →</button>
  </div>

  <div class="overlay" id="overlay" onclick="handleOverlayClick(event)">
    <div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">

      <!-- SUCCESS STATE -->
      <div class="success-state" id="successState">
        <div class="success-ring">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="38" height="38">
            <polyline points="20 6 9 17 4 12" />
          </svg>
        </div>
        <h2>Review Submitted!</h2>
        <p>Thank you for your feedback. Your review has been recorded and will help improve future applicant recommendations.</p>
        <div class="updated-rating" id="updatedRating"></div>
        <button class="close-success" onclick="closeModal()">Done — Close</button>
      </div>

      <!-- MAIN CONTENT -->
      <div id="mainContent">

        <div class="modal-header">
          <button class="close-btn" onclick="closeModal()" aria-label="Close">&#x2715;</button>
          <div class="check-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <h1 id="modal-title">Job Completed Successfully</h1>
          <p>Please rate your experience with this student. Your feedback helps maintain quality across the StudentLancer community.</p>
        </div>

        <form method="POST">
          <div class="modal-body">

            <!-- ILLUSTRATION -->
            <div class="illustration-wrap">
              <svg viewBox="0 0 360 160" xmlns="http://www.w3.org/2000/svg" style="max-width:220px;height:auto">
                <rect x="30" y="120" width="300" height="8" rx="4" fill="#c7d2fe" />
                <rect x="140" y="50" width="80" height="55" rx="6" fill="#4f46e5" />
                <rect x="148" y="58" width="64" height="40" rx="3" fill="#a5b4fc" />
                <rect x="175" y="105" width="10" height="15" rx="2" fill="#6366f1" />
                <rect x="165" y="118" width="30" height="4" rx="2" fill="#818cf8" />
                <rect x="154" y="65" width="30" height="3" rx="1.5" fill="#4f46e5" opacity=".6" />
                <rect x="154" y="72" width="44" height="3" rx="1.5" fill="#4f46e5" opacity=".6" />
                <rect x="154" y="79" width="22" height="3" rx="1.5" fill="#4f46e5" opacity=".4" />
                <rect x="154" y="86" width="36" height="3" rx="1.5" fill="#4f46e5" opacity=".6" />
                <circle cx="100" cy="60" r="16" fill="#fbbf24" />
                <rect x="86" y="78" width="28" height="36" rx="8" fill="#2563eb" />
                <rect x="86" y="48" width="28" height="6" rx="2" fill="#1e293b" />
                <polygon points="100,44 112,52 100,56 88,52" fill="#1e293b" />
                <line x1="112" y1="52" x2="116" y2="58" stroke="#1e293b" stroke-width="2" />
                <circle cx="116" cy="60" r="3" fill="#f59e0b" />
                <circle cx="260" cy="60" r="16" fill="#fb923c" />
                <rect x="246" y="78" width="28" height="36" rx="8" fill="#7c3aed" />
                <rect x="250" y="52" width="20" height="12" rx="4" fill="#f0fdf4" />
                <rect x="253" y="56" width="14" height="2" rx="1" fill="#16a34a" />
                <rect x="253" y="60" width="10" height="2" rx="1" fill="#86efac" />
                <text x="180" y="70" text-anchor="middle" font-size="28" fill="#f59e0b">★</text>
                <path d="M118 96 Q180 130 242 96" stroke="#10b981" stroke-width="2.5" stroke-dasharray="6 3" fill="none" stroke-linecap="round" />
              </svg>
            </div>

            <!-- STUDENT CARD -->
            <div class="student-card">
              <div class="avatar"><?= htmlspecialchars($initials) ?></div>
              <div class="student-info">
                <div class="student-name"> <?= htmlspecialchars($student['full_name']) ?></div>
                <div class="student-dept"><?= htmlspecialchars($student['department'] ?? 'Department not available') ?></div>
                <div class="job-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                    <rect x="2" y="7" width="20" height="14" rx="2" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                  </svg>
                  <?= htmlspecialchars($student['title']) ?>
                </div>
              </div>
              <div class="student-meta">
                <span class="badge-complete">✓ Completed</span>
                <div class="stat-pill">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <rect x="2" y="3" width="20" height="14" rx="2" />
                    <line x1="8" y1="21" x2="16" y2="21" />
                    <line x1="12" y1="17" x2="12" y2="21" />
                  </svg>
                  <?= (int)$student['completed_jobs'] ?> jobs
                </div>
                <div class="stat-pill">
                  <svg viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1" width="14" height="14">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                  </svg>
                  <?= number_format((float)$student['rating'], 1) ?> avg
                </div>
              </div>
            </div>

            <!-- STARS -->
            <div class="rating-section">
              <div class="rating-label">How would you rate this student's performance?</div>
              <div class="stars-row" id="starsRow" role="group" aria-label="Star rating"></div>
              <div class="rating-text" id="ratingText" style="opacity:.55">Select a rating</div>
            </div>
            <input
              type="hidden"
              name="rating"
              id="ratingValue"
              required>

            <!-- REVIEW -->
            <div class="review-wrap">
              <label for="reviewText">Written Review</label>
              <textarea
                id="reviewText"
                name="comment"
                class="review-textarea"
                maxlength="600"
                placeholder="Share your experience working with this student..."
                oninput="updateCounter()"></textarea>
              <div class="char-counter" id="charCounter">0 / 600</div>
            </div>

            <!-- TAGS -->
            <div>
              <div class="tags-label">Quick Feedback Tags</div>
              <div class="tags-wrap" id="tagsWrap"></div>
            </div>

            <!-- NOTICE -->
            <div class="notice-card">
              <div class="notice-icons">
                <div class="notice-icon ai">
                  <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" />
                  </svg>
                </div>
                <div class="notice-icon trust">
                  <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  </svg>
                </div>
              </div>
              <div class="notice-text">
                <h4>Improve Future Recommendations</h4>
                <p>Reviews and ratings contribute to future applicant rankings and job recommendations. Honest feedback helps employers identify top-performing students.</p>
              </div>
            </div>

            <!-- ACTIONS -->
            <div class="modal-actions">
              <button type="submit" class="btn-primary" id="submitBtn" disabled>Submit Review</button>
              <!-- <button class="btn-secondary" onclick="closeModal()">Skip for Now</button> -->
            </div>

          </div><!-- /modal-body -->
        </form>

        <div class="modal-footer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
          Your review helps build a trusted and transparent university freelancing community.
        </div>

      </div><!-- /mainContent -->
    </div>
  </div>

  <script>
    let selectedRating = 0,
      hoveredRating = 0;
    const LABELS = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
    const TAGS_DEF = ['Professional', 'Good Communication', 'Met Deadline', 'High Quality Work',
      'Creative', 'Reliable', 'Easy to Work With', 'Technical Competence', 'Problem Solving', 'Attention to Detail'
    ];
    const selectedTags = new Set();

    function buildStars() {
      const row = document.getElementById('starsRow');
      row.innerHTML = '';

      for (let i = 1; i <= 5; i++) {

        const btn = document.createElement('button');

        btn.type = 'button'; // <-- THIS IS THE FIX

        btn.className = 'star';

        btn.setAttribute(
          'aria-label',
          `${i} star${i > 1 ? 's' : ''}`
        );

        btn.dataset.value = i;

        btn.innerHTML = `
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke-width="1.5"/>
      </svg>
    `;

        btn.addEventListener('click', () => setRating(i));
        btn.addEventListener('mouseenter', () => hoverRating(i));
        btn.addEventListener('mouseleave', () => hoverRating(0));

        row.appendChild(btn);
      }

      refreshStars();
    }

    function refreshStars() {
      const stars = document.querySelectorAll('.star');
      const active = hoveredRating || selectedRating;
      stars.forEach((s, i) => {
        s.classList.toggle('active', i < selectedRating && !hoveredRating);
        s.classList.toggle('hovered', i < active);
      });
      const lbl = document.getElementById('ratingText');
      if (hoveredRating) {
        lbl.textContent = LABELS[hoveredRating];
        lbl.style.opacity = 1;
      } else if (selectedRating) {
        lbl.textContent = LABELS[selectedRating];
        lbl.style.opacity = 1;
      } else {
        lbl.textContent = 'Select a rating';
        lbl.style.opacity = .55;
      }
    }

    function setRating(n) {

      selectedRating = n;

      document.getElementById(
        'ratingValue'
      ).value = n;

      checkSubmit();
      refreshStars();
    }

    function hoverRating(n) {
      hoveredRating = n;
      refreshStars();
    }

    function buildTags() {
      const wrap = document.getElementById('tagsWrap');
      wrap.innerHTML = '';
      TAGS_DEF.forEach(tag => {
        const el = document.createElement('span');
        el.className = 'tag';
        el.textContent = tag;
        el.addEventListener('click', () => {
          el.classList.toggle('selected');
          el.classList.contains('selected') ? selectedTags.add(tag) : selectedTags.delete(tag);
        });
        wrap.appendChild(el);
      });
    }

    function updateCounter() {
      const len = document.getElementById('reviewText').value.length;
      const c = document.getElementById('charCounter');
      c.textContent = `${len} / 600`;
      c.classList.toggle('warn', len > 550);
    }

    function checkSubmit() {
      document.getElementById('submitBtn').disabled = selectedRating === 0;
    }


    function openModal() {
      selectedRating = 0;
      hoveredRating = 0;
      selectedTags.clear();
      document.getElementById('reviewText').value = '';
      updateCounter();
      document.getElementById('mainContent').style.display = '';
      document.getElementById('successState').classList.remove('visible');
      document.getElementById('submitBtn').disabled = true;
      buildStars();
      buildTags();
      document.getElementById('overlay').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      document.getElementById('overlay').classList.remove('active');
      document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
      if (e.target === document.getElementById('overlay')) closeModal();
    }

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModal();
    });
    window.addEventListener('load', openModal);
  </script>
</body>

</html>