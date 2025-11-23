<?php
//    echo $_SERVER['PHP_SELF'];
//    error_reporting(E_ALL);
//    ini_set('display_errors', 'On');
?>
    <form method="post"><input type="submit" name="submit" value="Reset Tables"></form>
    <form method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="password_match">Please reenter your Password:</label><br>
        <input type="password" id="password_match" name="password_match" required oninput="match()"><br>
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <p><input type="submit" name="submit" value="Register"></p>
    </form>
    <script>
        function match() {
            password = document.getElementById('password');
            match = document.getElementById('password_match');
            if (password.value != match.value) {
                match.setCustomValidity('Passwords do not match.');
            } else {
                match.setCustomValidty('');
            }
        }
    </script>
<?php
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        switch ($_POST['submit']) {
            case 'Register':
                $conn = new mysqli('localhost','register','register','HOBBYMART');
                // Check for an existing email and sanitise inputs
                $hashedPassword = password_hash($_POST['password'],PASSWORD_DEFAULT);
                try {
                    mysqli_query($conn,"INSERT INTO Users(email,password,name,role) VALUES ('{$_POST['email']}','{$hashedPassword}','{$_POST['name']}','user')");
                    echo "Registered.";
                } catch (mysqli_sql_exception $e) {
                    echo "There was an issue with registration. Please try again or continue as a guest.";
                }
                mysqli_close($conn);
                break;
            case 'Reset Tables':
                $conn = new mysqli('localhost','root', '', 'HOBBYMART');
                $user = "register";
                mysqli_query($conn, "DROP TABLE IF EXISTS Users");
                mysqli_query($conn, "CREATE USER IF NOT EXISTS '" . $user .  "'@'localhost' identified by '" . $user . "'");
                mysqli_query(
                    $conn,
                    "CREATE TABLE IF NOT EXISTS Users(
                        userID INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(80) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        name VARCHAR(40) NOT NULL,
                        role VARCHAR(20) NOT NULL,
                        UNIQUE(email)
                    )"
                );
                mysqli_query($conn, "GRANT SELECT, INSERT, UPDATE ON Users TO '" . $user . "'@'localhost'");
                mysqli_close($conn);
                break;
        }
    }
?>