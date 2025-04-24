<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$sql_test = "SELECT * FROM tests WHERE id = ?";
$stmt = $conn->prepare($sql_test);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result_test = $stmt->get_result();
$test = $result_test->fetch_assoc();

if (!$test) {
    die("Тест не найден.");
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Прохождение теста</title>
    <link rel="stylesheet" href="/kiu_test/css/style.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="/kiu_test/images/xlogo-2019.png.pagespeed.ic.HVPxFxoHdA.webp" alt="Логотип КИУ">
    </div>
    <h1>Прохождение теста</h1>
    <div class="user-info">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?> <?php echo htmlspecialchars($_SESSION['group_number']); ?></span>
        <div class="separator"></div>
        <form action="logout.php" method="post" style="display: inline;">
            <button type="submit"><img src="/kiu_test/icons/ic_exit_to_app.svg" alt="Выйти"></button>
        </form>
    </div>
</header>

<main>
    <div class="main-container">
        <section class="test-details">
            <h2><?php echo htmlspecialchars($test['test_name']); ?></h2>
            <p>Время прохождения: <?php echo htmlspecialchars($test['duration']); ?></p>
            <p>Вы готовы пройти тест?</p>
            <a href="question.php?test_id=<?php echo $test_id; ?>" class="start-button">Перейти</a>
        </section>
    </div>
</main>

<footer>
    <hr>
</footer>
</body>
</html>