<?php
    session_start();
    if ($_SERVER['PHP_SELF'] <> "/hobbymart/index.php") {
        header("Location: http://localhost/hobbymart/");
    }
?>