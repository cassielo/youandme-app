<?php
require_once __DIR__ . '/session.php';

session_start();
$serverName = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

// Encrypted connection when DB_SSL_CA names a CA certificate (Aiven requires it)
if (getenv('DB_SSL_CA')) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = getenv('DB_SSL_CA');
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}

try {
    // utf8mb4, like the tables (emoji included)
    $conn = new PDO("mysql:host=$serverName;port=$port;dbname=$dbName;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Service temporarily unavailable.');
}

function handle_db_error(string $context, PDOException $e): void {
    error_log("DB error [$context] at " . $e->getFile() . ':' . $e->getLine() . ': ' . $e->getMessage());
    http_response_code(500);
    die('Something went wrong. Please try again.');
}

// Show an alert, go to $page and stop
function alert_and_go(string $message, string $page): void {
    $flags = JSON_HEX_TAG | JSON_HEX_AMP;
    echo '<script>alert(' . json_encode($message, $flags) . '); window.location.assign(' . json_encode($page, $flags) . ');</script>';
    exit;
}

// Length in characters; false for invalid UTF-8
function text_length(string $text): int|false {
    return preg_match_all('/./su', $text);
}

// The utf8mb4 tables store any character, but not invalid UTF-8
function valid_text(string $text): bool {
    return preg_match('//u', $text) === 1;
}

// Shared demo account (sign-in in the README)
const DEMO_USERNAME = 'demo';

// By the stored name: "DEMO " logs in to the demo account too
function is_demo_account(PDO $conn, int|string $userId): bool {
    try {
        $q = $conn->prepare("SELECT username FROM user WHERE id = :id");
        $q->execute(['id' => $userId]);
        return $q->fetchColumn() === DEMO_USERNAME;
    } catch (PDOException $e) {
        handle_db_error('check demo account', $e);
    }
}

// Picture 1-7, or the default one (0 when new)
function avatar_src($pic): string {
    $n = (int) $pic;
    return 'assets/img/friends/fr-0' . ($n >= 1 && $n <= 7 ? $n : 8) . '.jpg';
}
