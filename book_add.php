<?php
require_once 'Database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);

    if ($title !== '') {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("INSERT INTO books (title) VALUES (:title)");
        $stmt->execute([
            ':title' => $title
        ]);

        $message = '本を登録しました！';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>本の登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded w-full shadow">
        <h1 class="text-2xl font-bold mb-6">本の登録</h1>

        <?php if (!empty($message)): ?>
            <p class="mb-4 text-green-600 font-semibold">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-medium mb-1">
                    タイトル <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                登録
            </button>
        </form>
    </div>
</body>
</html>
