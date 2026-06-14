<?php

require_once "company_mailer.php";

try {

    $result = sendCompanyApprovalEmail(
        "genesis.ighomwaye@stu.cu.edu.ng",
        "ABC Technologies",
        "Temp1234"
    );

    if ($result) {

        echo "Email Sent Successfully";

    } else {

        echo "Email Failed";
    }

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();
}