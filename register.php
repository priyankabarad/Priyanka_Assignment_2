<?php
require('db_connection_mysqli.php');

// Initialize variables
$username = $password = "";
$usernameErr = $passwordErr = "";

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate form inputs after submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    // If all validations pass, proceed with registration
    if (empty($usernameErr) && empty($passwordErr)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $query = "INSERT INTO admin (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php"); // Redirect to login page
            exit;
        } else {
            echo "Error registering user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }
        .login-container {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
        }
        .btn-primary {
            background-color: #6a11cb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2575fc;
        }
        .text-danger {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="login-container">
<h2 class="text-center">Admin Registration</h2>
      
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span class="text-danger"><?php echo $usernameErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="text-danger"><?php echo $passwordErr; ?></span>
            </div>


            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
