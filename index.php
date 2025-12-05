<?php
    // Create or access php session
    session_start();

    // Access the login page
    function login() {
        include "auth/login.php";
    }
?>

<?php login(); ?>
