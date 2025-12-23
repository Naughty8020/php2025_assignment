<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ユーザー登録</title>
</head>
<body>
<h1>ユーザー登録（INSERT）</h1>
 
  <form method="post" action="pdo_insert.php">
<div>
<label>ID：
<input type="text" name="id" required>
</label>
</div>
 
    <div>
<label>PASSWORD：
<input type="password" name="password" required>
</label>
</div>
 
    <button type="submit">登録</button>
</form>
<br>
<a href="pdo_select_all.php"><button type="button">ユーザー一覧へ戻る</button></a>
</body>
</html>