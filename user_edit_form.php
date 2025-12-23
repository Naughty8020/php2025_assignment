<?php
require 'pdo_connect.php';

$id = $_GET['id'] ?? '';

if ($id === '') {
    exit('IDが指定されていません。');
}

// レコードを1件取得
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('該当するユーザーが見つかりません。');
}
?>
<!doctype html>
<html lang="ja">
<head><meta charset="utf-8"><title>ユーザー更新</title></head>
<body>
    <h1>ユーザー更新フォーム</h1>
    <form method="post" action="user_update_confirm.php">
        <div>
            ID（変更不可）：
            <strong><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></strong>
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <br>
        <div>
            <label>新しいPASSWORD：
                <input type="password" name="password" required>
            </label>
        </div>
        <br>
        <button type="submit">更新内容を確認する</button>
    </form>
    <br>
    <a href="pdo_select_all.php">戻る</a>
</body>
</html>