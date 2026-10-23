<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

csrf_verify();
$chatId = $_POST['button'] ?? '';
$userId = $_SESSION['userID'];

// Not a number, or no such chatroom
if (!is_string($chatId) || !preg_match('/^[0-9]+$/', $chatId)) {
    alert_and_go('Chatroom not found.', 'chatroomlist.php');
}

try {
    $checkStmt = $conn->prepare("SELECT chatid FROM chatroom WHERE chatid = :chatid");
    $checkStmt->execute(['chatid' => $chatId]);
    if (!$checkStmt->fetch()) {
        alert_and_go('Chatroom not found.', 'chatroomlist.php');
    }

    // Already a member: joining again would list the room twice
    $memberStmt = $conn->prepare("SELECT 1 FROM list WHERE userID = :userid AND chatroomID = :chatid");
    $memberStmt->execute(['userid' => $userId, 'chatid' => $chatId]);
    if ($memberStmt->fetch()) {
        alert_and_go('You have already joined this chatroom.', 'mychatroom.php');
    }
    $stmt = $conn->prepare("INSERT INTO list (userID, chatroomID) VALUES (:userid, :chatid)");
    if ($stmt->execute(['userid' => $userId, 'chatid' => $chatId])) {
        alert_and_go('Joined the chatroom!', 'mychatroom.php');
    } else {
        alert_and_go('Could not join the chatroom.', 'chatroomlist.php');
    }
} catch (PDOException $e) {
    handle_db_error('join chatroom', $e);
}
