<?php

function runRecommendations()
{
    ob_start();

    require __DIR__ . "/update_recommendations.php";
    

    ob_end_clean();
}


