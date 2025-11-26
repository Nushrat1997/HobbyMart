<?php
    // Retrieve the existing session
    session_start()
    // Remove all session variables
    session_unset();
    // Destroy the session ie. log out
    session_destroy();
?>