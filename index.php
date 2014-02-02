<?php

if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
    echo "<b>Game Over</b>";
}
if (isset($_GET['success'])) {
    echo '<p class="success">you have been delogged</p>';
    echo "<b>Game Over</b>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Easy Pognon</title>
    <link rel="stylesheet" href="styles/main.css"/>
</head>
<body>
<h1>Login</h1>

<p>You can now go <a href="login.php">login page</a> and log in</p>
</body>
</html>