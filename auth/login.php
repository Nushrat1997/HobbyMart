<?php
// Initialize variables
$error = "";
$email = "";
$password = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } else {
        // Prototype only — no DB connection yet
        $error = "Login successful (prototype).";
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
<div class="container">
    <h2>Login</h2>

    <?php if ($error) : ?>
        <p class="<?php echo ($error == 'Login successful (prototype).') ? 'success' : 'error'; ?>">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Log In</button>
    </form>
</div>
</body>
</html>
