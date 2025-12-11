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
        <title>HobbyMart Login</title>
        <style>
            body { 
                font-family: Arial; 
                background-color: #f4f4f4; 
            }
            .container {
                width: 350px; 
                margin: 100px auto; 
                padding: 20px; 
                background: white; 
                border-radius: 8px; 
                box-shadow: 0px 0px 10px #ccc;
            }
            input, button {
                width: 100%; 
                padding: 10px; 
                margin-top: 10px;
            }
            button {
                background-color: #007bff; 
                border: none; 
                color: white; 
                cursor: pointer;
            }
            .error { color: red; }
            .success { color: green; }
        </style>
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
                    <button type="submit">Log In</button>
                </form>
                <form method="post">
                    <input type="submit" name="register" value="Register">
                    <input type="submit" name="guest" value="Continue as Guest">
                </form>
            </div>
        <?php } ?>
    </body>
</html>
