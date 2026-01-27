<?php
require_once 'Database.php';
$db  = Database::getInstance();
$pdo = $db->getConnection();

$book_id = $_GET['book_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();
if (!$book) die("本が見つかりません");

$message = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $borrower_name = $_POST['borrower_name'] ?? '';
    $borrow_date   = $_POST['borrow_date'] ?? '';
    $return_date   = $_POST['return_date'] ?? '';

    if ($borrower_name && $borrow_date && $return_date) {
        $pdo->prepare("INSERT INTO borrow_records (book_id, borrower_name, borrow_date, return_date) VALUES (?,?,?,?)")
            ->execute([$book_id,$borrower_name,$borrow_date,$return_date]);
        $pdo->prepare("UPDATE books SET status='borrowed' WHERE id=?")->execute([$book_id]);
        header("Location: index.php"); exit;
    } else {
        $message = "すべての項目を入力してください";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>貸出登録</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* メインコンテンツをスライドバーに合わせて調整 */
.main-content {
  transition: margin-left 0.3s;
  margin-left: 0; /* 初期はサイドバー閉じている状態 */
}

/* sidebar.php 側で開閉時に body に class 追加する場合はここで調整可能 */
body.sidebar-open .main-content {
  margin-left: 220px; /* サイドバー幅分だけ右にずらす */
}
</style>
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- サイドバー -->
    <div class="w-56 bg-white shadow">
        <?php include 'sidebar.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <div class="flex-1 p-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold mb-4">貸出登録</h1>
            <p class="mb-4">本: <?=htmlspecialchars($book['title'],ENT_QUOTES,'UTF-8')?></p>
            <?php if($message): ?><p class="text-red-600 mb-2"><?=$message?></p><?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-1">貸出者</label>
                    <input type="text" name="borrower_name" required class="w-full border px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block mb-1">貸出日</label>
                    <input type="date" name="borrow_date" value="<?=date('Y-m-d')?>" required class="w-full border px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block mb-1">返却期限</label>
                    <input type="date" name="return_date" value="<?=date('Y-m-d',strtotime('+7 days'))?>" required class="w-full border px-3 py-2 rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full hover:bg-blue-600">登録</button>
            </form>
        </div>
    </div>

</div>

</body>

</html>
