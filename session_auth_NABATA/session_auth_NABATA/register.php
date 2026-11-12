<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $message = "<div style='color: red; text-align: center; margin-bottom: 10px;'>All fields are required.</div>";
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $existingUser = $checkStmt->get_result();

        if ($existingUser->num_rows > 0) {
            $message = "<div style='color: red; text-align: center; margin-bottom: 10px;'>Email already registered.</div>";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
            $insertStmt->bind_param('sss', $name, $email, $passwordHash);

            if ($insertStmt->execute()) {
                $_SESSION['user_id'] = $insertStmt->insert_id;
                $_SESSION['user_name'] = $name;
                header('Location: dashboard.php');
                exit();
            }

            $message = "<div style='color: red; text-align: center; margin-bottom: 10px;'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-color: yellow;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #c7c402;
            margin-top: 0;
        }
        label {
            font-weight: bold;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #5bc702;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #0369a1;
        }
        .link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .link a {
            color: #c7a302;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Create Account</h2>

        <?php echo $message; ?>

        <form method="POST" action="register.php">
            <label>Username</label>
            <input type="text" name="name" placeholder="Enter username" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Create password" required>

            <button type="submit">Register</button>
        </form>

        <div class="link">
            <a href="index.php">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
