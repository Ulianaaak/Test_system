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
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, group_number FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['group_number'] = $user['group_number']; // Сохраняем номер группы

        header("Location: ../test.php");
        exit();
    } else {
        echo "Неверное имя пользователя или пароль.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница авторизации</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
</head>
<body>
<div class="container">
    <div class="login-box">
        <div class="login-box-form">
            <h2>Система тестирования</h2>
            <form action="php/avtoriz.php" method="post">
                <div class="input-group">
                    <img src="icons/user-icon.svg" alt="User Icon" class="input-icon">
                    <input type="email" name="email" placeholder="Имя пользователя" required autocomplete="email">
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <img id="pass" src="icons/lock-icon.svg" alt="Password Icon">
                    </div>
                    <input type="password" id="password-input" name="password" placeholder="Пароль" required autocomplete="current-password">
                </div>
                <button type="submit" class="input-group login-button">Вход</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
