<?php
require_once 'Database.php';

$db  = Database::getInstance();
$pdo = $db->getConnection();

/* 30日経過した貸出記録を自動削除 */
$pdo->exec("
    DELETE FROM borrow_records
    WHERE return_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");

/* 検索キーワード */
$keyword = $_GET['keyword'] ?? '';

/* 貸出処理 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book_id'])) {
    $book_id       = (int)$_POST['borrow_book_id'];
    $borrower_name = $_POST['borrower_name'] ?? '';
    $borrow_date   = $_POST['borrow_date'] ?? '';
    $return_date   = $_POST['return_date'] ?? '';

    if ($borrower_name && $borrow_date && $return_date) {
        $stmt = $pdo->prepare("
            INSERT INTO borrow_records (book_id, borrower_name, borrow_date, return_date)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$book_id, $borrower_name, $borrow_date, $return_date]);

        $stmt = $pdo->prepare("UPDATE books SET status='borrowed' WHERE id=?");
        $stmt->execute([$book_id]);

        header("Location: book_list.php");
        exit;
    } else {
        $message = "すべての項目を入力してください";
    }
}

/* 返却処理 */
if (isset($_GET['return_book_id'])) {
    $book_id = (int)$_GET['return_book_id'];
    $pdo->prepare("UPDATE books SET status='available' WHERE id = ?")->execute([$book_id]);
    header("Location: book_list.php");
    exit;
}

/* 本の削除処理 */
if (isset($_GET['delete_book_id'])) {
    $book_id = (int)$_GET['delete_book_id'];
    $pdo->prepare("DELETE FROM borrow_records WHERE book_id = ?")->execute([$book_id]);
    $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$book_id]);
    header("Location: book_list.php");
    exit;
}

/* 本一覧 */
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? ORDER BY title");
    $stmt->execute(['%' . $keyword . '%']);
    $books = $stmt->fetchAll();
} else {
    $books = $pdo->query("SELECT * FROM books ORDER BY title")->fetchAll();
}

/* 最近の貸出記録（3件） */
$records = $pdo->query("
    SELECT br.id, b.title, br.borrower_name, br.borrow_date, br.return_date
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

<div class="max-w-5xl w-full mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-4">本一覧</h1>

    <!-- 検索 -->
    <form method="GET" class="mb-4 flex gap-2">
        <input
            type="text"
            name="keyword"
            value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
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
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-4 py-2 border-b"><?= $book['status']==='available' ? '貸出可能' : '貸出中' ?></td>
                <td class="px-4 py-2 border-b">
                    <?php if ($book['status']==='available'): ?>
                        <form method="POST" class="grid grid-cols-1 gap-1">
                            <input type="hidden" name="borrow_book_id" value="<?= $book['id'] ?>">
                            <input type="text" name="borrower_name" placeholder="貸出者名" required class="border rounded px-2 py-1">
                            <input type="date" name="borrow_date" value="<?= date('Y-m-d') ?>" required class="border rounded px-2 py-1">
                            <input type="date" name="return_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required class="border rounded px-2 py-1">
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">貸出</button>
                        </form>
                    <?php else: ?>
                        <a href="?return_book_id=<?= $book['id'] ?>"
                           class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 inline-block">返却</a>
                    <?php endif; ?>
                    <a href="?delete_book_id=<?= $book['id'] ?>"
                       onclick="return confirm('この本を完全に削除しますか？');"
                       class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 inline-block mt-1">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 最近の貸出記録 -->
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
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-4 py-2 border-b"><?= htmlspecialchars($r['borrower_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-4 py-2 border-b"><?= $r['borrow_date'] ?></td>
                <td class="px-4 py-2 border-b"><?= $r['return_date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
zz