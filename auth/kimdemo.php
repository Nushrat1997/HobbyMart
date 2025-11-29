<?php
    session_start();

    function database_reset() {
        $conn = new mysqli('localhost','root','');
        $conn->query("DROP DATABASE IF EXISTS DEMOKIM");
        $conn->query("CREATE DATABASE DEMOKIM");
        $dbUser = 'register';
        $conn->query("DROP USER IF EXISTS '" . $dbUser . "'@'localhost'");
        $conn->query("CREATE USER IF NOT EXISTS '" . $dbUser . "'@'localhost' identified by '" . $dbUser . "'");
        $conn->close();
        $conn = new mysqli('localhost','root','','DEMOKIM');
        $conn->query(
            "CREATE TABLE Users(
                userID INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(80) NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(40) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'user',
                UNIQUE(email)
            )"
        );
        $conn->query("GRANT SELECT, INSERT, UPDATE ON Users TO '" . $dbUser . "'@'localhost'");
        $conn->close();
    }
    function registration() {
        if (!isset($_SESSION['id'])) {
            try {
                include 'register.php';
            } catch (Exception $e) {
                echo "There is an issue with logins and registration. Come back again later, or continue as a guest.";
            }
        } else {
            echo "You are already logged in!";
        }
    }
    function fakeSession() {
        $_SESSION['id'] = session_id();
    }
    function logout() {
        try {
            include 'logout.php';
        } catch (Exception $e) {
            echo "Error with logging out.";
        }
    }
    
    switch ($_POST['submit']) {
        case 'Reset':
            database_reset();
            break;
        case 'Fake Login':
            fakeSession();
            break;
        case 'Logout':
            logout();
            break;
    }

?>
<!DOCTYPE html>
<html>

    <?php 
        if ($_POST['submit'] == "Register New User" || $_POST['submit'] == "Register") {
            registration();
        }
    ?>
    <form method="post">
        <?php if ($_SERVER['REQUEST_METHOD'] == "GET") { ?>
            <input type="submit" name="submit" value="Reset"><br>
        <?php } ?>
        <?php if ($_POST['submit'] != "Register New User") { ?>
        <input type="submit" name="submit" value="Register New User"><br>
        <?php } ?>
        <input type="submit" name="submit" value="Fake Login"><br>
        <input type="submit" name="submit" value="Logout"><br>
    </form>

</html>
