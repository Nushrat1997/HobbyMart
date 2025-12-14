<?php
    // Retrieve the existing session
    session_start();
    if (isset($_SESSION['id'])) {
        // Remove all session variables
        session_unset();
        // Destroy the session ie. log out
        session_destroy();
        // Redirect to landing page with successful log out
        header("Location: http://localhost/hobbymart/?logout=success");
    } else {
        header("Location: http://localhost/hobbymart/");
    }
?>
