<?php
$id = $_POST['id'] ?? '';
$password = $_POST['password'] ?? '';

if ($id === '' || $password === '') {
    exit('入力が不足しています。');
}
?>
<!doctype html>
<html lang="ja">
<head><meta charset="utf-8"><title>更新確認</title></head>
<body>
    <h1>更新内容の確認</h1>
    <p>ユーザーID: <?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?></p>
    <p>新しいパスワード: ******** （表示されません）</p>

    <form method="post" action="user_update_execute.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="password" value="<?= htmlspecialchars($password, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">この内容で登録（更新）する</button>
        <button type="button" onclick="history.back()">修正する</button>
    </form>
</body>
</html>