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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['group_number'] = $user['group_number'];

            header("Location: /kiu_test/php/test.php");
            exit();
        } else {
            echo "Неверный пароль!";
        }
    } else {
        echo "Пользователь с таким email не найден!";
    }
}

$conn->close();
?>