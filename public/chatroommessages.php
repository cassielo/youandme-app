<?php

// The chatroom's message and member lists as JSON, for chatroom.js (read only)
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/chat.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$roomId = (int) ($_SESSION['chatroomID'] ?? 0);
$userId = (int) $_SESSION['userID'];
$flags = JSON_INVALID_UTF8_SUBSTITUTE;

try {
    // Members of the chosen room only
    $check = $conn->prepare("SELECT 1 FROM list WHERE chatroomID = :room AND userID = :user");
    $check->execute(['room' => $roomId, 'user' => $userId]);
    if (!$check->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Not a member of this chatroom.'], $flags);
        exit;
    }
    $messages = chat_messages($conn, $roomId, $userId);
    echo json_encode(['last' => $messages['last'], 'messages' => $messages['html'], 'members' => chat_members($conn, $roomId)], $flags);
} catch (PDOException $e) {
    handle_db_error('update chatroom', $e);
}
