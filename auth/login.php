<?php
    session_start();

    function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['id'])) {
            $conn = new mysqli('localhost','auth','auth','HOBBYMART');
            $check = $conn->prepare("SELECT count(*) AS count FROM Users WHERE email=?");
            $check->bind_param('s',$_POST['email']);
            $check->execute();
            $exists = ($check->get_result())->fetch_assoc()['count'];
            if ($exists > 0) {
                $verify = $conn->prepare("SELECT password FROM Users where email=?");
                $verify->bind_param('s',$_POST['email']);
                $verify->execute();
                $password = ($verify->get_result())->fetch_assoc()['password'];
                if (password_verify($_POST['password'],$password)) {
                    $_SESSION['id'] = session_id();
                } else {
                    echo "Incorrect username or password provided.";
                }
            }
        }
    }
    function registered() {
        if ($_GET['registration'] == "success") {
            echo "Please log in with your newly registered account.";
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
        <?php login(); ?>
        <?php if (!isset($_SESSION['id'])) { ?>
            <div class="container">
                <h2>Login</h2>
                <h3><?php registered(); ?></h3>
                <form action="" method="POST">
                    <input type="email" name="email" required placeholder="Email" value="<?php echo $_POST["email"]; ?>">
                    <input type="password" name="password" required placeholder="Password">
                    <input type="submit" name="login" class="primary" value="Log In">
                </form>
                <form method="post">
                    <input type="submit" name="register" value="Register">
                    <input type="submit" name="guest" value="Continue as Guest">
                </form>
            </div>
        <?php } ?>
    </body>
</html>
