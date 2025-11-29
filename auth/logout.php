<?php
    // Retrieve the existing session
    session_start();
    if (isset($_SESSION['id'])) {
        // Remove all session variables
        session_unset();
        // Destroy the session ie. log out
        session_destroy();
        echo "Successfully logged out.";
    } else {
        echo "You are not currently logged in.";
    }
?>