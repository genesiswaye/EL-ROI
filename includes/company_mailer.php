<?php
require_once "../config/mail_config.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../vendor/autoload.php";

function sendCompanyApprovalEmail(
    string $email,
    string $companyName,
) {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(
            MAIL_USER,
            'StudentLancer'
        );

        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject =
            "StudentLancer Company Verification Approved";

        $mail->Body = "

        <h2>Hello {$companyName}</h2>

        <p>
        Congratulations.
        </p>

        <p>
        Your company registration on StudentLancer has been successfully reviewed and approved.
        </p>

        <p>
        You can now log in using the email address and password you provided during registration.
        </p>

        <p>
        We look forward to helping you connect with talented student freelancers.
        </p>

        <br>

        <p>
        Regards,<br>
        StudentLancer Team
        </p>
        ";

        $mail->send();

        return true;
    } catch (Exception $e) {

    die(
        "Mailer Error: " .
        $mail->ErrorInfo
    );
}
}

function sendCompanyRejectionEmail(
    string $email,
    string $companyName
) {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(
            MAIL_USER,
            'StudentLancer'
        );

        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject =
            "StudentLancer Company Verification Update";

        $mail->Body = "

        <h2>Hello {$companyName}</h2>

        <p>
        Unfortunately your company application
        was not approved.
        </p>

        <p>
        You may contact platform support
        for additional clarification.
        </p>

        <br>

        <p>
        Regards,<br>
        StudentLancer Team
        </p>
        ";

        $mail->send();

        return true;
    } catch (Exception $e) {

    die(
        "Mailer Error: " .
        $mail->ErrorInfo
    );
}
}
