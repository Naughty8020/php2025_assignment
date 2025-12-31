<?php
// DB接続
$host = 'localhost';
$db   = 'library';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "DB接続失敗: " . $e->getMessage();
    exit;
}

// borrow_records と books を JOIN してタイトルを取得
$stmt = $pdo->query("
    SELECT br.id, br.borrower_name, br.borrow_date, br.return_date, b.title
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    ORDER BY br.id DESC
");
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>貸出リスト</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-100">

    <!-- サイドバー -->
    <?php include 'sidebar.php'; ?>

    <!-- メインコンテンツ -->
    <div class="flex-1 p-6">
        <div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
            <h1 class="text-3xl font-bold mb-6">貸出リスト</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border-b">ID</th>
                            <th class="px-4 py-2 border-b">本の名前</th>
                            <th class="px-4 py-2 border-b">貸出者</th>
                            <th class="px-4 py-2 border-b">貸出日</th>
                            <th class="px-4 py-2 border-b">返却期限</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['borrower_name']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['borrow_date']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['return_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
