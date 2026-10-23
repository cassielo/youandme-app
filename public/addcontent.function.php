<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

csrf_verify();
if (!isset($_SESSION['chatroomID'])) {
    alert_and_go('Please choose a chatroom first.', 'mychatroom.php');
}

// Strings only (a forged request can send arrays)
$content = is_string($_POST['Content'] ?? null) ? $_POST['Content'] : '';
if (trim($content) != '') {
    if (text_length($content) > 255) {
        alert_and_go('Message is too long (255 characters at most).', 'chatroom.php');
    }
    if (!valid_text($content)) {
        alert_and_go("Your message has a character that can't be used.", 'chatroom.php');
    }
    $chatroomId = $_SESSION['chatroomID'];
    $userId = $_SESSION['userID'];

    try {
        // UTC, so the time does not depend on the database's time zone
        $stmt = $conn->prepare("INSERT INTO chat (userid, chatroomid, content, created_at)
            VALUES (:userid, :chatroomid, :content, UTC_TIMESTAMP())");
        if ($stmt->execute(['userid' => $userId, 'chatroomid' => $chatroomId, 'content' => $content])) {
            header('Location: chatroom.php');
        } else {
            alert_and_go('Could not send your message.', 'chatroom.php');
        }
    } catch (PDOException $e) {
        handle_db_error('add chat content', $e);
    }
} else {
    alert_and_go('Please type a message.', 'chatroom.php');
}
