<?php
require 'pdo_connect.php';

// デバッグ用
// echo '<pre>';
// var_dump($_GET);
// var_dump($_POST);
// echo '</pre>';
// exit; 
//

$id = $_POST['id'] ?? '';

if ($id === '') {
    exit('IDが指定されていません。');
}

// 念のため、本当に存在するユーザーか確認（1件取得）
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('ユーザーが見つかりませんでした。');
}
?>
<!doctype html>
<html lang="ja">
<head><meta charset="utf-8"><title>削除確認</title></head>
<body>
    <h1>削除確認</h1>
    <p>以下のユーザーを削除します。よろしいですか？</p>
    <p>ユーザーID: <strong><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></strong></p>

    <form method="post" action="user_delete_execute.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">本当に削除する</button>
        <a href="pdo_select_all.php"><button type="button">キャンセル</button></a>
    </form>
</body>
</html>