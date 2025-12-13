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
        $conn->query(
            "CREATE TABLE IF NOT EXISTS HOBBYMART.Products(
                productID INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                stock INT DEFAULT 0,
                image VARCHAR(255)
            )"
        );
        $conn->query("GRANT SELECT, INSERT, UPDATE ON HOBBYMART.Users TO 'auth'@'localhost'");
        $checkUsers = $conn->prepare("SELECT COUNT(*) AS count FROM HOBBYMART.Users");
        $checkUsers->execute();
        $usersExists = ($checkUsers->get_result())->fetch_assoc()['count'];
        if ($usersExists == 0) {
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
            $createUsers->close();
        }
        $checkUsers->close();
        $checkProducts = $conn->prepare("SELECT COUNT(*) AS count FROM HOBBYMART.Products");
        $checkProducts->execute();
        $productsExists = ($checkProducts->get_result())->fetch_assoc()['count'];
        if ($productsExists == 0) {
            $createProducts = $conn->prepare(
                "INSERT INTO HOBBYMART.Products(name,description,price,stock,image) VALUES
                    ('Watercolor Set', '24-color professional watercolor set', 19.99, 20, 'img/watercolor.jpg'),
                    ('3D Printer Filament', '1kg PLA filament for 3D printing', 24.50, 15, 'img/filament.jpg'),
                    ('Sketchbook A4', '120gsm paper for drawing', 12.00, 25, 'img/sketchbook.jpg')
                "
            );
            $createProducts->execute();
            $createProducts->close();
        }
        $checkProducts->close();
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
    if ($_GET['action'] == "Register" || $_POST['submit'] == "Register") {
        getRegistration();
    }
    // Page to go to if continuing as guest, or successfully logged on
    elseif (isset($_SESSION['id']) || $_GET['action'] == "Continue as Guest") {
        if (!isset($_SESSION['id'])) {
            $_SESSION['guest'] = true;
        }
        header("Location: http://localhost/hobbymart/inventory/list_products.php");
    }
    else {
        getLogin();
    }
?>
