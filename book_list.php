<?php
$pdo = new PDO("mysql:host=localhost;dbname=library;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// 30日経過した貸出記録を自動削除
$pdo->exec("
    DELETE FROM borrow_records
    WHERE return_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");

// 検索キーワード
$keyword = $_GET['keyword'] ?? '';

// 返却処理
if (isset($_GET['return_book_id'])) {
    $book_id = (int)$_GET['return_book_id'];
    $pdo->prepare("UPDATE books SET status = 'available' WHERE id = ?")
        ->execute([$book_id]);
    header("Location: book_list.php");
    exit;
}


// 📌 本の削除処理
if (isset($_GET['delete_book_id'])) {
    $book_id = (int)$_GET['delete_book_id'];

    // 紐づく貸出記録を先に削除
    $pdo->prepare("DELETE FROM borrow_records WHERE book_id = ?")
        ->execute([$book_id]);

    // 本を削除
    $pdo->prepare("DELETE FROM books WHERE id = ?")
        ->execute([$book_id]);

    header("Location: book_list.php");
    exit;
}

// 本一覧（検索対応）
if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM books
        WHERE title LIKE ?
        ORDER BY title
    ");
    $stmt->execute(['%' . $keyword . '%']);
    $books = $stmt->fetchAll();
} else {
    $books = $pdo->query("
        SELECT * FROM books
        ORDER BY title
    ")->fetchAll();
}

// 最近の貸出記録（3件）
$records = $pdo->query("
    SELECT br.*, b.title
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    ORDER BY br.id DESC
    LIMIT 3
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>本一覧と貸出管理</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen bg-gray-100">
<?php include 'sidebar.php'; ?>

<div class="max-w-4xl w-full mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-4">本一覧</h1>

    <!-- 検索 -->
    <form method="GET" class="mb-4 flex gap-2">
        <input
            type="text"
            name="keyword"
            value="<?= htmlspecialchars($keyword) ?>"
            placeholder="タイトルで検索"
            class="flex-1 border rounded px-3 py-2"
        >
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            検索
        </button>
    </form>

    <!-- 本一覧 -->
    <table class="min-w-full border border-gray-200 rounded mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b">ID</th>
                <th class="px-4 py-2 border-b">タイトル</th>
                <th class="px-4 py-2 border-b">状態</th>
                <th class="px-4 py-2 border-b">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td class="px-4 py-2 border-b"><?= $book['id'] ?></td>
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($book['title']) ?></td>
                <td class="px-4 py-2 border-b">
                    <?= $book['status'] === 'available' ? '貸出可能' : '貸出中' ?>
                </td>
               

<td class="px-4 py-2 border-b">
    <div class="grid grid-cols-2 gap-1">
        <?php if ($book['status'] === 'available'): ?>
            <a href="borrow_form.php?book_id=<?= $book['id'] ?>"
               class="bg-blue-500 text-white w-full px-2 py-1 rounded text-center text-sm hover:bg-blue-600">
                貸出
            </a>
        <?php else: ?>
            <a href="?return_book_id=<?= $book['id'] ?>"
               class="bg-green-500 text-white w-full px-2 py-1 rounded text-center text-sm hover:bg-green-600">
                返却
            </a>
        <?php endif; ?>

        <a href="?delete_book_id=<?= $book['id'] ?>"
           onclick="return confirm('この本を完全に削除しますか？');"
           class="bg-red-500 text-white w-full px-2 py-1 rounded text-center text-sm hover:bg-red-600">
            削除
        </a>
    </div>
</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 貸出記録 -->
    <h2 class="text-xl font-bold mb-2">最近の貸出記録</h2>
    <table class="min-w-full border border-gray-200 rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b">ID</th>
                <th class="px-4 py-2 border-b">本</th>
                <th class="px-4 py-2 border-b">貸出者</th>
                <th class="px-4 py-2 border-b">貸出日</th>
                <th class="px-4 py-2 border-b">返却期限</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td class="px-4 py-2 border-b"><?= $r['id'] ?></td>
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($r['title']) ?></td>
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($r['borrower_name']) ?></td>
                <td class="px-4 py-2 border-b"><?= $r['borrow_date'] ?></td>
                <td class="px-4 py-2 border-b"><?= $r['return_date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
