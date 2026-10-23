<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

// Strings only (a forged request can send arrays)
if (isset($_POST['username']) && isset($_POST['password']) && is_string($_POST['username']) && is_string($_POST['password'])) {
    csrf_verify();
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Older data may hold duplicate usernames: try each account
        $stmt = $conn->prepare("SELECT id, username, password, name, mail, pic FROM user WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $rows = false;
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (password_verify($password, $row['password'])) {
                $rows = $row;
                break;
            }
        }
        if ($rows) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $userId = $rows['id'];
            $_SESSION['userID'] = $userId;
            $name = $rows['name'];
            $_SESSION['name'] = $name;
            $mail = $rows['mail'];
            $_SESSION['mail'] = $mail;
            $pic = $rows['pic'];
            $_SESSION['pic'] = $pic;
            header('Location: member.php');
        } else {
            alert_and_go('Invalid username or password.', 'index.php');
        }
    } catch (PDOException $e) {
        handle_db_error('login', $e);
    }
} else {
    alert_and_go('Please enter your username and password.', 'index.php');
}
