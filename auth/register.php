<?php
    session_start();
    include $_SERVER['DOCUMENT_ROOT'] . "/hobbymart/auth/index.php";

    function registrationForm() {
    // Check if the $_SESSION superglobal has an id value; if not, show the registration form
        if (!isset($_SESSION['id'])) {
?>
    <div class="container">
        <h2>Register New User</h2>
        <h4><?php register(); ?></h4>
        <form method="post">
            <label for="email" hidden>Email</label>
            <input type="email" id="email" name="email" class="entry" required placeholder="Email" pattern="^.*@.*\..*"><br>
            <label for="password" hidden>Password</label>
            <input type="password" id="password" name="password" class="entry" required placeholder="Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|\W)).{8,}$" oninput="validate(this)"><br>
            <div id="invalid_warn" class="warning"></div>
            <label for="password_match" hidden>Reenter password</label>
            <input type="password" id="password_match" name="password_match" class="entry" required placeholder="Reenter password" oninput="match(this)"><br>
            <div id="match_warn" class="warning"></div>
            <label for="name" hidden>Name:</label>
            <input type="text" id="name" name="name" class="entry" required placeholder="Name"><br>
            <input type="submit" name="submit" class="primary" value="Register">
        </form>
        <form method="get">
            <input type="submit" name="action" value="Return to login">
        </form>
    </div>
    <script>
        function validate(password) {
            warning = document.getElementById("invalid_warn");
            requirements = ["<p>A valid password must contain:"];
            if (!password.checkValidity()) {
                if (!(/^(?=.*[A-Z])/.test(password.value))) {requirements.push("<br>at least 1 uppercase letter")}
                if (!(/^(?=.*[a-z])/.test(password.value))) {requirements.push("<br>at least 1 lowercase letter")}
                if (!(/^(?=.*\d)/.test(password.value))) {requirements.push("<br>at least 1 digit")}
                if (!(/^(?=.*(_|\W))/.test(password.value))) {requirements.push("<br>at least 1 special character")}
                if (!(/^.{8,}$/.test(password.value))) {requirements.push("<br>at least 8 characters")}
                requirements.push("</p>");
                warning.innerHTML = requirements.reduce((current,next)=> current + next);
            } else {
                warning.innerHTML = "";
            }
        }
        function match(match) {
            warning = document.getElementById('match_warn');
            if (match.value != document.getElementById('password').value) {
                match.setCustomValidity('Passwords do not match.');
                warning.innerHTML = "<p>Passwords do not match.</p>";
            } else {
                match.setCustomValidity('');
                warning.innerHTML = "";
            }
        }
    </script>
<?php
        } else {
            header("Location: http://localhost/hobbymart/");
        }
    }

    function register() {
        // If posting registration values without a valid session id, try to create the user
        if ($_POST['submit'] == "Register" && !isset($_SESSION['id'])) {
            $conn = new mysqli('localhost','auth','auth','HOBBYMART');
            $check = $conn->prepare("SELECT COUNT(*) AS registered FROM Users WHERE email=?");
            $check->bind_param('s',$_POST['email']);
            $check->execute();
            $exists = ($check->get_result())->fetch_assoc()['registered'];
            if ($exists == 0) {
                $hashedPassword = password_hash($_POST['password'],PASSWORD_DEFAULT);
                try {
                    $insert = $conn->prepare("INSERT INTO Users(email,password,name) VALUES(?,'{$hashedPassword}',?)");
                    $insert->bind_param('ss',$_POST['email'],$_POST['name']);
                    $insert->execute();
                    $insert->close();
                    $check->close();
                    $conn->close();
                    header("Location: http://localhost/hobbymart/?registration=success");
                } catch (mysqli_sql_exception $e) {
                    echo "There was an issue with registration. Please try again or continue as a guest.";
                }
            } else {
                echo "A user with this email address already exists. Please register a different email address.";
            }
            $check->close();
            $conn->close();
        }
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/auth.css">
        <title>HobbyMart Registration</title>
    </head>
    <body>
        <?php registrationForm(); ?>
    </body>
</html>
