<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

logAction($conn, $_SESSION['user_id'], 'Выход из системы');

session_destroy();

exit();
function logAction($conn, $user_id, $action) {
    $sql = "INSERT INTO user_actions (user_id, action) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}
?>