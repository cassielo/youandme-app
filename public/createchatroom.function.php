<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

csrf_verify();

// Strings only (a forged request can send arrays or leave fields out)
$newChatName = is_string($_POST['NewChatName'] ?? null) ? $_POST['NewChatName'] : '';
$newChatIntro = is_string($_POST['NewChatIntro'] ?? null) ? $_POST['NewChatIntro'] : '';
if (trim($newChatName) != '') {
    if (text_length($newChatName) > 50) {
        alert_and_go('Chatroom name is too long (50 characters at most).', 'createchatroom.php');
    }
    if (text_length($newChatIntro) > 255) {
        alert_and_go('Introduction is too long (255 characters at most).', 'createchatroom.php');
    }
    if (!valid_text($newChatName)) {
        alert_and_go("The chatroom name has a character that can't be used.", 'createchatroom.php');
    }
    if (!valid_text($newChatIntro)) {
        alert_and_go("The introduction has a character that can't be used.", 'createchatroom.php');
    }
    $managerId = $_SESSION['userID'];

    // The chatroom and its creator's membership together, or neither
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("INSERT INTO chatroom (managerID, chatname, intro)
            VALUES (:managerID, :chatname, :intro)");
        $stmt->execute(['managerID' => $managerId, 'chatname' => $newChatName, 'intro' => $newChatIntro]);
        $chatId = $conn->lastInsertId();
        $listStmt = $conn->prepare("INSERT INTO list (userID, chatroomID) VALUES (:userID, :chatid)");
        $listStmt->execute(['userID' => $managerId, 'chatid' => $chatId]);
        $conn->commit();
        alert_and_go('Chatroom created!', 'mychatroom.php');
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log('DB error [create chatroom] at ' . $e->getFile() . ':' . $e->getLine() . ': ' . $e->getMessage());
        alert_and_go('Could not create the chatroom.', 'createchatroom.php');
    }
} else {
    alert_and_go('Please enter a chatroom name.', 'createchatroom.php');
}
