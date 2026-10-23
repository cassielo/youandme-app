<?php

// Message and member lists of the chatroom page, for chatroom.php and chatroommessages.php

// Messages in order (own ones on the right) and the newest id; times as UTC seconds for chatroom.js
function chat_messages(PDO $conn, int $roomId, int $viewerId): array {
    $q = $conn->prepare("SELECT chat.id, chat.userid, chat.content, user.name, user.pic,
            TIMESTAMPDIFF(SECOND, '1970-01-01', chat.created_at) AS sent
        FROM chat JOIN user ON user.id = chat.userid
        WHERE chat.chatroomid = :room
        ORDER BY chat.id");
    $q->execute(['room' => $roomId]);
    $html = '';
    $last = 0;
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
        $last = (int) $r['id'];
        $text = htmlspecialchars((string) $r['content'], ENT_QUOTES, 'UTF-8');
        $time = '<time class="chat-time" data-sent="' . (int) $r['sent'] . '"></time>';
        if ((int) $r['userid'] === $viewerId) {
            $html .= '<div class="chat-msg chat-msg-mine"><div class="chat-body">'
                . '<div class="alert alert-info chat-bubble">' . $text . '</div>' . $time . '</div></div>';
        } else {
            $html .= '<div class="chat-msg"><img class="img-circle chat-avatar" src="' . avatar_src($r['pic']) . '" alt="">'
                . '<div class="chat-body"><div class="chat-name">' . htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') . '</div>'
                . '<div class="alert alert-success chat-bubble">' . $text . '</div>' . $time . '</div></div>';
        }
    }
    return ['html' => $html, 'last' => $last];
}

// The room's members, in the order they joined
function chat_members(PDO $conn, int $roomId): string {
    $q = $conn->prepare("SELECT user.name, user.pic
        FROM user JOIN list ON list.userID = user.id
        WHERE list.chatroomID = :room
        ORDER BY list.listID");
    $q->execute(['room' => $roomId]);
    $html = '';
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
        $html .= '<div class="desc"><div class="thumb"><img class="img-circle" src="' . avatar_src($r['pic']) . '" width="35" height="35" alt=""></div>'
            . '<div class="details"><p>' . htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') . '</p></div></div>';
    }
    return $html;
}
