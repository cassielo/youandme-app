<?php
require_once dirname(__DIR__) . '/includes/session.php';

session_start();
require_once dirname(__DIR__) . '/includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>You and Me</title>
<link rel="stylesheet" href="assets/css/bootstrap.css">
<link rel="stylesheet" href="assets/css/home.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic">
</head>
<body>

<!-- Header -->
<section class="hero" id="hero">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center">
                <h1 class="animated bounceInDown">You and Me</h1>
                <p class="animated fadeInUpDelay">Bridging the Distance Between Us.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <a href="#getstarted" class="get-started-btn">Start Chatting</a>
            </div>
        </div>
    </div>
</section>

<!-- Login and register forms -->
<section class="download-now" id="getstarted">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-login">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="login-form" action="login.function.php" method="post" role="form" style="display: block;">
                                    <?php echo csrf_field(); ?>
                                    <h2>LOGIN</h2>
                                    <div class="form-group">
                                        <input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password" required>
                                    </div>
                                    <div class="col-xs-6 col-sm-offset-3">
                                        <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                    </div>
                                </form>
                                <form id="register-form" action="register.function.php" method="post" role="form" style="display: none;">
                                    <?php echo csrf_field(); ?>
                                    <h2>REGISTER</h2>
                                    <div class="form-group">
                                        <input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="" maxlength="50" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password (8 characters at least)" minlength="8" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" tabindex="1" class="form-control" placeholder="First and Last Name" value="" maxlength="50" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="mail" id="mail" tabindex="1" class="form-control" placeholder="Email Address" value="" maxlength="255" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-6 tabs">
                                <a href="#" class="active" id="login-form-link"><div class="login">LOGIN</div></a>
                            </div>
                            <div class="col-xs-6 tabs">
                                <a href="#" id="register-form-link"><div class="register">REGISTER</div></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/home.js"></script>
</body>
</html>
