<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
require_once dirname(__DIR__) . '/includes/chat.php';

if (isset($_POST['button'])) {
    csrf_verify();
    $chatId = (int) $_POST['button'];
    $memberCheck = $conn->prepare("SELECT chatroom.chatname
        FROM chatroom JOIN list ON chatroom.chatid = list.chatroomID
        WHERE list.chatroomID = :chatid AND list.userID = :userID");
    $memberCheck->execute(['chatid' => $chatId, 'userID' => $_SESSION['userID']]);
    $room = $memberCheck->fetch();
    if (!$room) {
        alert_and_go('You are not a member of this chatroom.', 'mychatroom.php');
    }
    $_SESSION['chatroomID'] = $chatId;
    $_SESSION['chatroomName'] = $room['chatname'];
}

// No room chosen (e.g. opened from the address bar)
if (!isset($_SESSION['chatroomID'])) {
    alert_and_go('Please choose a chatroom first.', 'mychatroom.php');
}

try {
    $messages = chat_messages($conn, (int) $_SESSION['chatroomID'], (int) $_SESSION['userID']);
    $members = chat_members($conn, (int) $_SESSION['chatroomID']);
} catch (PDOException $e) {
    handle_db_error('load chatroom', $e);
}
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
<link rel="stylesheet" href="assets/css/chatroom.css">
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
                    <a class="active" href="mychatroom.php">
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
            <div class="row">
                <div class="col-sm-9">
                    <div class="form-panel chat-panel" style="height:520px;">
                        <h3 class="mb"><i class="fa fa-angle-right"></i> Welcome to <?php echo htmlspecialchars($_SESSION['chatroomName'], ENT_QUOTES, 'UTF-8'); ?></h3>

                        <!-- data-last: newest message id, for chatroom.js -->
                        <div class="chat-messages" id="chat-messages" data-last="<?php echo $messages['last']; ?>"><?php echo $messages['html']; ?></div>
                        <div class="chat-input">
                            <form class="form-horizontal style-form chat-form" action="addcontent.function.php" method="post">
                                <?php echo csrf_field(); ?>
                                <input type="text" class="form-control" name="Content" placeholder="Say Something" autocomplete="off" maxlength="255" required>
                                <button type="submit" class="btn btn-theme">Enter</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Members -->
                <div class="col-lg-3 ds">
                    <h3>CHATROOM MEMBERS</h3>
                    <div id="chat-members"><?php echo $members; ?></div>
                </div>
            </div>
        </section>
    </section>
</section>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/common-scripts.js"></script>

<!-- Live messages and local send times -->
<script src="assets/js/chatroom.js"></script>
</body>
</html>
