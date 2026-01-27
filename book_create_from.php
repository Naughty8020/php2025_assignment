<?php
require_once 'Database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

/* 本一覧取得 */
$books = $pdo
    ->query("SELECT id, title FROM books ORDER BY title")
    ->fetchAll();

/* フォーム送信処理 */
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'] ?? '';
    $borrower_name = trim($_POST['borrower_name'] ?? '');
    $borrow_date = $_POST['borrow_date'] ?? '';
    $return_date = $_POST['return_date'] ?? '';

    if ($book_id && $borrower_name && $borrow_date && $return_date) {
        $stmt = $pdo->prepare(
            "INSERT INTO borrow_records 
             (book_id, borrower_name, borrow_date, return_date)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $book_id,
            $borrower_name,
            $borrow_date,
            $return_date
        ]);

        // 二重送信防止
        header('Location: borrow.php?success=1');
        exit;
    } else {
        $message = 'すべての項目を入力してください。';
    }
}

/* メッセージ表示 */
if (isset($_GET['success'])) {
    $message = '貸出情報を登録しました！';
}

/* 貸出一覧取得 */
$records = $pdo->query("
    SELECT br.id, b.title, br.borrower_name, br.borrow_date, br.return_date
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    ORDER BY br.id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>貸出管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow w-full">
        <h1 class="text-2xl font-bold mb-6">貸出登録</h1>

        <?php if (!empty($message)): ?>
            <p class="mb-4 text-green-600 font-semibold">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <!-- 貸出登録フォーム -->
        <form method="POST" class="space-y-4 mb-8">
            <div>
                <label class="block font-medium mb-1">本</label>
                <select name="book_id" required
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">本を選択してください</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?= $book['id'] ?>">
                            <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">貸出者</label>
                <input type="text" name="borrower_name" required
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-medium mb-1">貸出日</label>
                <input type="date" name="borrow_date" required
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-medium mb-1">返却期限</label>
                <input type="date" name="return_date" required
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                登録
            </button>
        </form>

        <!-- 貸出一覧 -->
        <h2 class="text-2xl font-bold mb-4">貸出一覧</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">ID</th>
                        <th class="px-4 py-2 border-b">本のタイトル</th>
                        <th class="px-4 py-2 border-b">貸出者</th>
                        <th class="px-4 py-2 border-b">貸出日</th>
                        <th class="px-4 py-2 border-b">返却期限</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td class="px-4 py-2 border-b"><?= $row['id'] ?></td>
                            <td class="px-4 py-2 border-b">
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-2 border-b">
                                <?= htmlspecialchars($row['borrower_name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-2 border-b"><?= $row['borrow_date'] ?></td>
                            <td class="px-4 py-2 border-b"><?= $row['return_date'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
