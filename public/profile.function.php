<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

csrf_verify();

// By id: older data may hold duplicate usernames
$id = $_SESSION['userID'];

// An empty, missing or array field means "leave unchanged"
function posted(string $name): string {
    $value = $_POST[$name] ?? '';
    return is_string($value) ? $value : '';
}

$password = posted('password');
$name = posted('name');
$pic = posted('selectpic');

// Check every field before saving any
if ($name != '' && text_length($name) > 50) {
    alert_and_go('Name is too long (50 characters at most).', 'profile.php');
}
if ($name != '' && !valid_text($name)) {
    alert_and_go("Your name has a character that can't be used. Please use a different name.", 'profile.php');
}

// 1-7 can be picked; 0 is the starting picture
if ($pic != '' && !(preg_match('/^[0-9]+$/', $pic) && $pic <= 7)) {
    alert_and_go('Please choose one of the pictures.', 'profile.php');
}

// Invalid UTF-8 (length false) counts as too short
if ($password != '' && text_length($password) < 8) {
    alert_and_go('Password must be at least 8 characters.', 'profile.php');
}

// The shared demo account keeps its published password
if ($password != '' && is_demo_account($conn, $id)) {
    alert_and_go("The demo account's password can't be changed.", 'profile.php');
}

try {
    if ($password != '') {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $password, 'id' => $id]);
    }
    if ($name != '') {
        $stmt = $conn->prepare("UPDATE user SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
        $_SESSION['name'] = $name;
    }
    if ($pic != '') {
        $pic = (int) $pic;
        $stmt = $conn->prepare("UPDATE user SET pic = :pic WHERE id = :id");
        $stmt->execute(['pic' => $pic, 'id' => $id]);
        $_SESSION['pic'] = $pic;
    }
    if ($password != '') {
        // A new password ends the session
        session_destroy();
        alert_and_go('Profile updated. Please log in again with your new password.', 'index.php');
    } else {
        alert_and_go('Profile updated!', 'member.php');
    }
} catch (PDOException $e) {
    handle_db_error('update profile', $e);
}
