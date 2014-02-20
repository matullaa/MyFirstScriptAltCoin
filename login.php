<?php
include_once 'include/db_connect.php';
include_once 'include/functions.php';

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
    <title>Easy Pognon: LogIn</title>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'/>
    <link rel="stylesheet" href="styles/main.css"/>
    <link href="styles/login-box.css" rel="stylesheet" type="text/css"/>
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


<form action="include/process_login.php" method="post" name="login_form" id="login_form">
    <div id="login-box">

        <H2>Login</H2>
        Easy Pognon veuillez vous connecter.
        <br/>
        <br/>

        <div id="login-box-name" style="margin-top:20px;">Email:</div>
        <div id="login-box-field" style="margin-top:20px;">
            <input name="email" id="email" class="form-login" title="Username" value="" size="30" maxlength="2048"/>
        </div>
        <div id="login-box-name">Password:</div>
        <div id="login-box-field">
            <input name="password" id="password" type="password" class="form-login" title="Password" value="" size="30"
                   maxlength="2048"/>
        </div>
        <br/>
        <span class="login-box-options">
<!--            <a href="#" style="color: white">Forgot password?</a></span>-->
        <br/>
        <br/>
        <a href="#"><img src="images/login-btn.png"
                         onclick="formhash(document.getElementById('login_form'), document.getElementById('password'))"
                         width="103"
                         height="42" style="margin-left:90px;"/></a>

        <p>If you don't have a login, please <a href="register.php" style="color:white">register</a></p>

        <p>If you are done, please <a href="include/logout.php">log out</a>.</p>

        <p>You are currently logged <?php echo $logged ?>.</p>

        <!--    <input type="button"-->
        <!--           value="Login"-->
        <!--           onclick="formhash(this.form, this.form.password);"/>-->


</form>
</body>
</html>