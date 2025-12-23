<?php
Require 'pdo_connect.php';

// SQL組み立て（プリペアステートメント作成）
$sql ="SELECT * FROM users";
$prepare = $con->prepare($sql);

// SQL実行
$prepare->execute(); //SQL文を実行
$results = $prepare->fetchAll(PDO::FETCH_ASSOC); //実行結果を$resultsに代入
?>

<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザー一覧</title>
</head>
<body>
 
<h2>usersテーブル一覧</h2>
 
<!-- 新規作成ボタン -->
<p>
<a href="user_create_form.php">新規作成</a>
</p>
 
<?php if ($results): ?>
<table border="1" cellspacing="0" cellpadding="6">
<tr>
<th>ID</th>
<th>PASSWORD</th>
<th>操作</th>
</tr>
 
    <?php foreach ($results as $row): ?>
<?php
        $user_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $user_password = htmlspecialchars($row['password'], ENT_QUOTES, 'UTF-8');
      ?>
<tr>
<td><?= $user_id ?></td>
<td><?= $user_password ?></td>
<td>
<!-- 変更ボタン -->
<a href="user_edit_form.php?id=<?= urlencode($row['id']) ?>">変更</a>
 
          <!-- 削除ボタン（GET削除は危険なのでPOST推奨） -->
<form method="post" action="user_delete.php" style="display:inline;">
<input type="hidden" name="id" value="<?= $user_id ?>">
<button type="submit" onclick="return confirm('削除しますか？');">削除</button>
</form>
</td>
</tr>
<?php endforeach; ?>
 
  </table>
<?php else: ?>
<p>usersテーブルにデータがありません。</p>
<?php endif; ?>
 
</body>
</html>