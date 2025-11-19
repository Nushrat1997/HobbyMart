<?php
//    echo $_SERVER['PHP_SELF'];
//    error_reporting(E_ALL);
//    ini_set('display_errors', 'On');
?>
    <form method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <p><input type=submit name="submit" value="Register"></p>
    </form>
<?php
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $conn = new mysqli('localhost','auth','auth','HOBBYMART');
        // Check for an existing email and sanitise inputs
        $hashedPassword = password_hash($_POST['password'],PASSWORD_DEFAULT);
        try {
            mysqli_query($conn,"INSERT INTO USERS(email,password) VALUES ('{$_POST['email']}','{$hashedPassword}')");
            echo "Registered.";
        } catch (mysqli_sql_exception $e) {
            echo "There was an issue with registration. Please try again or continue as a guest.";
        }
        mysqli_close($conn);
    }
?>
