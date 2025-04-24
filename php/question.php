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

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;

if ($test_id <= 0) {
    die("ID теста не указан или некорректен.");
}

$sql_test = "SELECT * FROM tests WHERE id = ?";
$stmt = $conn->prepare($sql_test);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result_test = $stmt->get_result();
$test = $result_test->fetch_assoc();

if (!$test) {
    die("Тест с указанным ID не найден.");
}

$currentQuestionIndex = isset($_GET['question']) ? intval($_GET['question']) : 0;

$sql_questions = "SELECT * FROM questions WHERE test_id = ?";
$stmt = $conn->prepare($sql_questions);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result_questions = $stmt->get_result();
$questions = $result_questions->fetch_all(MYSQLI_ASSOC);

if (empty($questions)) {
    die("Вопросы для теста не найдены.");
}

if (!isset($questions[$currentQuestionIndex])) {
    die("Вопрос не найден.");
}

$currentQuestion = $questions[$currentQuestionIndex];

$options = [];
if ($currentQuestion['question_type'] === 'multiple-choice') {
    $sql_options = "SELECT * FROM options WHERE question_id = ?";
    $stmt = $conn->prepare($sql_options);
    $stmt->bind_param("i", $currentQuestion['id']);
    $stmt->execute();
    $result_options = $stmt->get_result();
    $options = $result_options->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование</title>
    <link rel="stylesheet" href="/kiu_test/css/style.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="/kiu_test/images/xlogo-2019.png.pagespeed.ic.HVPxFxoHdA.webp" alt="Логотип КИУ">
    </div>
    <h1><?php echo htmlspecialchars($test['test_name'] ?? 'Неизвестный тест'); ?></h1>
    <div class="user-info">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?> <?php echo htmlspecialchars($_SESSION['group_number']); ?></span>
        <div class="separator"></div>
        <form action="index.php" method="post" style="display: inline;">
            <button type="submit"><img src="/kiu_test/icons/ic_exit_to_app.svg" alt="Выйти"></button>
        </form>
    </div>
</header>

<main>
    <section class="navigation">
        <p>Осталось: <span id="countdown">00:00</span></p>
        <div class="pagination-indicators">
            <?php foreach ($questions as $index => $question): ?>
                <div class="indicator <?php echo $index === $currentQuestionIndex ? 'active' : ''; ?>"></div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="question-container">
        <div class="question-block">
            <p class="question-text"><?php echo htmlspecialchars($currentQuestion['question_text']); ?></p>
            <?php if (isset($currentQuestion['image_url']) && $currentQuestion['image_url']): ?>
                <img src="<?php echo htmlspecialchars($currentQuestion['image_url']); ?>" alt="Изображение вопроса">
            <?php endif; ?>
        </div>
        <div class="answer-block">
            <?php if ($currentQuestion['question_type'] === 'multiple-choice'): ?>
                <?php foreach ($options as $option): ?>
                    <label>
                        <input type="radio" name="answer" value="<?php echo htmlspecialchars($option['id']); ?>">
                        <?php echo htmlspecialchars($option['option_text']); ?>
                    </label><br>
                <?php endforeach; ?>
            <?php elseif ($currentQuestion['question_type'] === 'text'): ?>
                <textarea placeholder="Введите ответ..."></textarea>
            <?php endif; ?>
        </div>
    </div>

    <div class="pagination">
        <a href="?test_id=<?php echo $test_id; ?>&question=<?php echo $currentQuestionIndex - 1; ?>" class="prev-btn">&larr; Предыдущая страница</a>
        <?php if ($currentQuestionIndex < count($questions) - 1): ?>
            <a href="?test_id=<?php echo $test_id; ?>&question=<?php echo $currentQuestionIndex + 1; ?>" class="next-btn">Следующая страница &rarr;</a>
        <?php else: ?>
            <form method="post" action="finish.php" style="display: inline;">
                <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
                <button type="submit" id="finish-btn">Завершить &rarr;</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    <hr>
    <button id="back-btn">&larr; Назад</button>
</footer>
</body>
</html>