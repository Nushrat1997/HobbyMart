<?php
    session_start();
    include $_SERVER['DOCUMENT_ROOT'] . "/hobbymart/auth/index.php";

    function login() {
        $error ="";
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['id'])) {
            $conn = new mysqli('localhost','auth','auth','HOBBYMART');
            $check = $conn->prepare("SELECT count(*) AS count FROM Users WHERE email=?");
            $check->bind_param('s',$_POST['email']);
            $check->execute();
            $exists = ($check->get_result())->fetch_assoc()['count'];
            if ($exists > 0) {
                $verify = $conn->prepare("SELECT password, role FROM Users where email=?");
                $verify->bind_param('s',$_POST['email']);
                $verify->execute();
                $row = ($verify->get_result())->fetch_assoc();
                if (password_verify($_POST['password'],$row['password'])) {
                    $_SESSION['id'] = session_id();
                    $_SESSION['admin'] = ($row['role'] == "admin");
                    $_SESSION['guest'] = false;
                    header("Location: http://localhost/hobbymart/");
                } else {
                    $error = "Incorrect username or password provided.";
                }
                $verify->close();
            } else {
                $error = "Incorrect username or password provided.";
            }
            $check->close();
            $conn->close();
            echo "<h4 class=\"warning\">" . $error . "</h4>";
        }
    }
    
    function registered() {
        if ($_GET['registration'] == "success") {
            echo "<h4 class=\"success\">Please log in with your newly registered account.</h4>";
        }
    }
    function loggedOut() {
        if ($_GET['logout'] == "success" && !isset($_SESSION['id'])) {
            echo "<h4 class=\"success\">Logged out successfully.</h4>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/auth.css">
        <title>HobbyMart Login</title>
    </head>

    <body>
        <?php if (!isset($_SESSION['id'])) { ?>
            <div class="container">
                <h2>Login</h2>
                <?php login(); loggedOut(); registered(); ?>
                <form method="POST" action="index.php">
                    <input type="email" name="email" class="entry" required placeholder="Email" value="<?php echo $_POST["email"]; ?>">
                    <input type="password" name="password" class="entry" required placeholder="Password">
                    <input type="submit" name="login" class="primary" value="Log In">
                </form>
                <form method="get" action="index.php">
                    <input type="submit" name="action" value="Register">
                    <input type="submit" name="action" value="Continue as Guest">
                </form>
            </div>
        <?php } else { 
            header("Location: http://localhost/hobbymart/");
        } ?>
    </body>
</html>
