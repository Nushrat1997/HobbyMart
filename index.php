<?php
    // Create or access php session
    session_start();

    function dB_create() {
        $conn = new mysqli('localhost','root','');
        $conn->query("CREATE DATABASE IF NOT EXISTS HOBBYMART");
        $conn->query("CREATE USER IF NOT EXISTS 'auth'@'localhost' identified by 'auth'");
        $conn->query(
            "CREATE TABLE IF NOT EXISTS HOBBYMART.Users(
                userID INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(80) NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(40) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'user',
                UNIQUE(email)
            )"
        );
        $conn->query("GRANT SELECT, INSERT, UPDATE ON HOBBYMART.Users TO 'auth'@'localhost'");
        $check = $conn->prepare("SELECT COUNT(*) AS count FROM HOBBYMART.Users");
        $check->execute();
        $exists = ($check->get_result())->fetch_assoc()['count'];
        if ($exists == 0) {
            $admin = array('admin@hobbymart',password_hash('admin',PASSWORD_DEFAULT),'admin','admin');
            $user = array('test@hobbymart',password_hash('test',PASSWORD_DEFAULT),'test','user');
            $createUsers = $conn->prepare(
                "INSERT INTO HOBBYMART.Users(email,password,name,role) VALUES
                    (?,?,?,?),
                    (?,?,?,?)
                "
            );
            $createUsers->bind_param('ssssssss',$admin[0],$admin[1],$admin[2],$admin[3],$user[0],$user[1],$user[2],$user[3]);
            $createUsers->execute();
        }
        $conn->close();
    }

    // Access the login page
    function getLogin() {
        if (!isset($_SESSION['id'])) {
            include "auth/login.php";
        }
    }

    // Access the registration page
    function getRegistration() {
        if (!isset($_SESSION['id'])) {
            include "auth/register.php";
        }
    }

    function getLogout() {
        if (isset($_SESSION['id']) && isset($_GET['logout'])) {
            include "auth/logout.php";
        }
    }

    dB_create();
    getLogout();
    if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['register']) || $_POST['submit'] == "Register")) {
        getRegistration();
    }
    // Page to go to if continuing as guest, or successfully logged on
    // elseif (isset($_SESSION['id']) || $_SESSION['guest'] || ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guest']))) {
        // if (!isset($_SESSION['id'])) {
            // $_SESSION['guest'] = true;
        //}
    //}
    else {
        getLogin();
    }
?>
