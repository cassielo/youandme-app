<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>You and Me</title>
<link rel="stylesheet" href="assets/css/bootstrap.css">
<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/style-responsive.css">
</head>
<body>
<section id="container">
    <!-- Header -->
    <header class="header black-bg">
        <div class="sidebar-toggle-box">
            <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
        </div>
        <a href="member.php" class="logo"><b>YOU AND ME</b></a>
        <div class="btn-group pull-right" style="margin-top:13px">
            <form action="logout.function.php" method="post">
                <?php echo csrf_field(); ?>
                <button type="button" class="btn btn-theme dropdown-toggle" data-toggle="dropdown">
                    <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><button type="submit" class="btn-link">Logout</button></li>
                </ul>
            </form>
        </div>
    </header>

    <!-- Sidebar -->
    <aside>
        <div id="sidebar" class="nav-collapse">
            <ul class="sidebar-menu" id="nav-accordion">
                <p class="centered"><a href="profile.php"><img src="<?php echo avatar_src($_SESSION['pic']); ?>" class="img-circle" width="60"></a></p>
                <h5 class="centered"><?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <li class="mt">
                    <a href="profile.php">
                        <i class="fa fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a href="mychatroom.php">
                        <i class="fa fa-comment"></i>
                        <span>My Chatroom</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a href="chatroomlist.php">
                        <i class="fa fa-users"></i>
                        <span>Join Chatroom</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a href="createchatroom.php">
                        <i class="fa fa-plus"></i>
                        <span>Create Chatroom</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main content -->
    <section id="main-content">
        <section class="wrapper">
            <img src="assets/img/friends/back.png" width="400" style="display:block; margin-top:130px; margin-left:350px;">
        </section>
    </section>
</section>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/common-scripts.js"></script>
</body>
</html>
