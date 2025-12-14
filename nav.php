<?php
    if (isset($_SESSION['id'])) {
        echo "<a href=\"/HobbyMart/?logout\">Log Out</a>";
    } elseif ($_SESSION['guest'] = true) {
        echo "<a href=\"/HobbyMart\">Return to Login</a>";
    }
?>