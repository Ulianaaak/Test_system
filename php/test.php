<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$group_number = $_SESSION['group_number'] ?? null;

if (!$group_number) {
    die("Номер группы не найден.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$sql_tests = "SELECT * FROM tests WHERE group_number = ?";
$stmt = $conn->prepare($sql_tests);
$stmt->bind_param("i", $group_number);
$stmt->execute();
$result_tests = $stmt->get_result();
$tests = $result_tests->fetch_all(MYSQLI_ASSOC);

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
        <section class="available-tests">
            <h2>Доступные тесты:</h2>
            <?php if (!empty($tests)): ?>
                <?php foreach ($tests as $test): ?>
                    <div class="test-card" data-test-id="<?php echo htmlspecialchars($test['id']); ?>">
                        <p><?php echo htmlspecialchars($test['test_name']); ?></p>
                        <p>Группа <?php echo htmlspecialchars($test['group_number']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Нет доступных тестов.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<footer>
    <hr>
</footer>

<script>
    document.querySelectorAll('.test-card').forEach(card => {
        card.addEventListener('click', function () {
            const testId = this.getAttribute('data-test-id');
            if (testId) {
                window.location.href = `test_test.php?test_id=${testId}`;
            }
        });
    });
</script>
</body>
</html>