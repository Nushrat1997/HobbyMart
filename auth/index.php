<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if ($_SERVER['PHP_SELF'] <> "/HobbyMart/index.php") {
        header("Location: http://localhost/HobbyMart/");
    }
?>