<?php
require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = 'Username already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':username', $username);
            $insert_stmt->bindParam(':password', $hashed_password);
            $insert_stmt->bindParam(':email', $email);

            if ($insert_stmt->execute()) {
                $success = 'Account created successfully. <a href="login.php">Login here</a>.';
            } else {
                $error = 'Error creating account. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="signup-page">
    <div class="form-container">
        <form method="POST" action="signup.php">
            <label for="username" class="labels">Username:</label><br>
            <input type="text" id="username" class="field" name="username" required><br><br>

            <label for="password" class="labels">Password:</label><br>
            <input type="password" id="password" class="field" name="password" required><br><br>

            <label for="email" class="labels">Email:</label><br>
            <input type="email" id="email" class="field" name="email" required><br>

            <button type="submit" class="login-button">Sign Up</button>
        </form>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
