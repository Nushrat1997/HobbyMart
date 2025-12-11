<?php
    session_start();

    function register() {
    // Check if the $_SESSION superglobal has an id value; if not, show the registration form
        if (!isset($_SESSION['id'])) {
?>
    <form method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Email" pattern="^.*@.*\..*"><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|\W)).{8,}$" oninput="validate(this)"><br>
        <div id="invalid_warn"></div>
        <label for="password_match">Please reenter your Password:</label>
        <input type="password" id="password_match" name="password_match" required placeholder="Reenter password" oninput="match(this)"><br>
        <div id="match_warn"></div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required placeholder="Name"><br>
        <p><input type="submit" name="submit" value="Register"></p>
    </form>
    <form method="post">
        <input type="submit" name="login" value="Return to login">
    </form>
    <script>
        function validate(password) {
            warning = document.getElementById("invalid_warn");
            requirements = ["A valid password must contain:<br>"];
            if (!password.checkValidity()) {
                if (!(/^(?=.*[A-Z])/.test(password.value))) {requirements.push("at least 1 uppercase letter<br>")}
                if (!(/^(?=.*[a-z])/.test(password.value))) {requirements.push("at least 1 lowercase letter<br>")}
                if (!(/^(?=.*\d)/.test(password.value))) {requirements.push("at least 1 digit<br>")}
                if (!(/^(?=.*(_|\W))/.test(password.value))) {requirements.push("at least 1 special character<br>")}
                if (!(/^.{8,}$/.test(password.value))) {requirements.push("at least 8 characters<br>")}
                warning.innerHTML = requirements.reduce((current,next)=> current + next);
            } else {
                warning.innerHTML = "";
            }
        }
        function match(match) {
            warning = document.getElementById('match_warn');
            if (match.value != document.getElementById('password').value) {
                match.setCustomValidity('Passwords do not match.');
                warning.innerHTML = "Passwords do not match.";
            } else {
                match.setCustomValidity('');
                warning.innerHTML = "";
            }
        }
    </script>
<?php
    } else {
        echo "You are already logged in!";
    }
}
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
                header("Location: http://localhost/hobbymart/?registration=success");
            } catch (mysqli_sql_exception $e) {
                echo "There was an issue with registration. Please try again or continue as a guest.";
            }
        } else {
            echo "A user with this email address already exists. Please register a different email address.";
        }
        mysqli_close($conn);
    }
?>


<!DOCTYPE html>
<html>
    <head>
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
            input[value=Register] {
                background-color: #007bff; 
                border: none; 
                color: white; 
                cursor: pointer;
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
        <title>HobbyMart Registration</title>
    </head>
    <body>
        <div class="container">
        <?php register(); ?>
        </div>
    </body>
</html>
