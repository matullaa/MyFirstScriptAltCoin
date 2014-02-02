<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Easy Pognon: Log In</title>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'/>
    <link rel="stylesheet" href="styles/main.css"/>
    <link href="login-box.css" rel="stylesheet" type="text/css"/>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script
        id="sap-ui-bootstrap"
        src='https://openui5.hana.ondemand.com/resources/sap-ui-core.js'
        data-sap-ui-libs="sap.m"
        data-sap-ui-theme="sap_bluecrystal">
    </script>
</head>
<body>
<?php
if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}
?>


<form action="includes/process_login.php" method="post" name="login_form">
    Email: <input type="text" name="email"/>
    Password: <input type="password"
                     name="password"
                     id="password"/>
    <input type="button"
           value="Login"
           onclick="formhash(this.form, this.form.password);"/>

    <p>If you don't have a login, please <a href="register.php">register</a></p>

    <p>If you are done, please <a href="includes/logout.php">log out</a>.</p>

    <p>You are currently logged <?php echo $logged ?>.</p>

</form>
</body>
</html>