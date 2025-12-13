<?php
    if (isset($_SESSION['id'])) {
        echo "<a href=\"/hobbymart/?logout\">Log Out</a>";
    } elseif ($_SESSION['guest'] = true) {
        echo "<a href=\"/hobbymart\">Return to Login</a>";
    }
?>