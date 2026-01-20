<?php
$pdo = new PDO("mysql:host=db;dbname=library;charset=utf8mb4", "root", "password", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// GETパラメータから本情報取得
$book_id = $_GET['book_id'] ?? 0;
$book = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$book->execute([$book_id]);
$book = $book->fetch();

if (!$book) die("本が見つかりません");

// フォーム送信処理
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrower_name = $_POST['borrower_name'] ?? '';
    $borrow_date = $_POST['borrow_date'] ?? '';
    $return_date = $_POST['return_date'] ?? '';

    if ($borrower_name && $borrow_date && $return_date) {
        // 貸出登録
        $stmt = $pdo->prepare("INSERT INTO borrow_records (book_id, borrower_name, borrow_date, return_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$book_id, $borrower_name, $borrow_date, $return_date]);

        // 本の状態を貸出中に更新
        $stmt = $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
        $stmt->execute([$book_id]);

        // ここでリダイレクト
        header("Location: book_list.php");
        exit; // 重要：処理をここで止める
    } else {
        $message = "すべての項目を入力してください。";
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>貸出登録</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">本の貸出登録</h1>

    <p class="mb-4 font-medium">本: <?= htmlspecialchars($book['title']) ?></p>

    <?php if ($message): ?>
        <p class="mb-4 text-green-600"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">貸出者</label>
            <input type="text" name="borrower_name" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block mb-1 font-medium">貸出日</label>
            <input type="date" name="borrow_date" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= date('Y-m-d') ?>">
        </div>
        <div>
            <label class="block mb-1 font-medium">返却期限</label>
            <input type="date" name="return_date" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">登録</button>
    </form>
</div>
</body>
</html>
