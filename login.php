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
    <link rel="stylesheet" href="styles/main.css"/>
    <link href="login-box.css" rel="stylesheet" type="text/css"/>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/openui5/resources/sap-ui-core.js"></script>
    <script
        id="sap-ui-bootstrap"
        src="js/openui5/resources/sap-ui-core.js"
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
<div style="padding: 100px 0 0 250px;">


    <div id="login-box">

        <H2>Login</H2>
        Mineur bienvenue !!!!
        <br/>
        <br/>

        <form action="includes/process_login.php" method="post" name="login_form">
            <div id="login-box-name" style="margin-top:20px;">Email:</div>
            <div id="login-box-field" style="margin-top:20px;"><input name="q" class="form-login" title="Username"
                                                                      value=""
                                                                      size="30" maxlength="2048"/></div>
            <div id="login-box-name">Password:</div>
            <div id="login-box-field"><input name="q" type="password" class="form-login" title="Password" value=""
                                             size="30"
                                             maxlength="2048"/></div>
            <br/>
        <span class="login-box-options"><input type="checkbox" name="1" value="1"> Remember Me <a href="#"
                                                                                                  style="margin-left:30px;">Forgot
                password?</a></span>

            <br/>
            <br/>

            <a href="#"><img src="images/login-btn.png" width="103" height="42" style="margin-left:90px;"
                             onclick="formhash(this.form, this.form.password);"/></a>
        </form>
        <p>If you don't have a login, please <a href="register.php">register</a></p>

        <p>If you are done, please <a href="includes/logout.php">log out</a>.</p>

        <p>You are currently logged <?php echo $logged ?>.</p>

    </div>

</div>
<!--<form action="includes/process_login.php" method="post" name="login_form">-->
<!--    Email: <input type="text" name="email"/>-->
<!--    Password: <input type="password"-->
<!--                     name="password"-->
<!--                     id="password"/>-->
<!--    <input type="button"-->
<!--           value="Login"-->
<!--           onclick="formhash(this.form, this.form.password);"/>-->
<!--</form>-->
</body>
</html>