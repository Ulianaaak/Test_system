<?php
session_start();

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Поиск пользователя в базе данных
    $sql = "SELECT id FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Создаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;

        // Логируем действие
        logAction($conn, $user['id'], 'Вход в систему');

        // Перенаправляем на главную страницу
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Неверное имя пользователя или пароль.";
    }
}

// Функция для логирования действий
function logAction($conn, $user_id, $action) {
    $sql = "INSERT INTO user_actions (user_id, action) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}
?>