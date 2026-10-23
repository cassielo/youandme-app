<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

// Strings only (a forged request can send arrays)
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['mail']) && isset($_POST['name'])
    && is_string($_POST['username']) && is_string($_POST['password']) && is_string($_POST['mail']) && is_string($_POST['name'])) {
    csrf_verify();
    $username = $_POST['username'];
    $mail = $_POST['mail'];
    $name = $_POST['name'];

    // Both required; new passwords need 8 characters (invalid UTF-8 counts as too short)
    if (trim($username) === '') {
        alert_and_go('Please enter a username.', 'index.php');
    }
    if ($_POST['password'] === '') {
        alert_and_go('Please enter a password.', 'index.php');
    }
    if (text_length($_POST['password']) < 8) {
        alert_and_go('Password must be at least 8 characters.', 'index.php');
    }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if (text_length($username) > 50) {
        alert_and_go('Username is too long (50 characters at most).', 'index.php');
    }
    if (text_length($name) > 50) {
        alert_and_go('Name is too long (50 characters at most).', 'index.php');
    }
    if (text_length($mail) > 255) {
        alert_and_go('Email is too long (255 characters at most).', 'index.php');
    }
    if (!valid_text($username)) {
        alert_and_go("Your username has a character that can't be used. Please choose a different username.", 'index.php');
    }
    if (!valid_text($name)) {
        alert_and_go("Your name has a character that can't be used. Please use a different name.", 'index.php');
    }
    if (!valid_text($mail)) {
        alert_and_go("Your email has a character that can't be used. Please use a different email.", 'index.php');
    }
    if (filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
        alert_and_go('Please enter a valid email address.', 'index.php');
    }

    try {
        // Compared as at login: "AMY" and "amy " match "amy"
        $check = $conn->prepare("SELECT COUNT(*) FROM user WHERE username = :username");
        $check->execute(['username' => $username]);
        if ($check->fetchColumn() > 0) {
            alert_and_go('This username is already taken. Please choose another one.', 'index.php');
        }
        $stmt = $conn->prepare("INSERT INTO user (username, password, mail, name)
            VALUES (:username, :password, :mail, :name)");
        if ($stmt->execute(['username' => $username, 'password' => $password, 'mail' => $mail, 'name' => $name])) {
            $newUserId = $conn->lastInsertId();
            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['userID'] = $newUserId;
            $_SESSION['username'] = $username;
            $_SESSION['mail'] = $mail;
            $_SESSION['name'] = $name;
            $_SESSION['pic'] = 0;
            alert_and_go('Registered successfully!', 'index.php');
        } else {
            alert_and_go('Registration failed. Please try again.', 'index.php');
        }
    } catch (PDOException $e) {
        handle_db_error('register', $e);
    }
} else {
    alert_and_go('Please enter a username, password, name and email.', 'index.php');
}
