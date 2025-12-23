<?php
require 'pdo_connect.php';

// POST以外は弾く処理（直アクセス対策）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('POSTで送信してください。');
}

// 受け取り
// ?? ''->NULLの時に''
// trim 前後の空白を削除 "user01"
$id = trim($_POST['id'] ?? '');
// (string)強制的に文字型に変換
$password = (string)($_POST['password']);

// 入力チェック
if ($id === '' || $password === '') {
    exit('IDとPASSWORDを入力してください。');
}

$sql ="INSERT INTO users (id, password) VALUES (?, ?)";
$stmt = $con->prepare($sql);
$stmt->execute([$id, $password]);

echo "登録成功：ID = " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
echo "<br>";
echo '<a href="pdo_select_all.php"><button type="button">ユーザー一覧へ戻る</button></a>';



