<?php

function requireRole($roles)
{

    if (

        !isset(
            $_SESSION['admin_role']
        )

        ||

        !in_array(

            $_SESSION['admin_role'],

            $roles

        )

    ) {

        header(
            "Location: login.php"
        );

        exit();

    }

}