<?php
Require 'pdo_connect.php';
// echo "Requireでpdo_connect.php を読み込んで実行されました。";

$id = $_POST['id'];
$password = $_POST['pw'];

echo $id;
echo $password;

$sql ="SELECT * FROM users WHERE id=? and password=?";
$prepare = $con->prepare($sql);
$prepare->bindvalue(1, $id);
$prepare->bindvalue(2, $password);

// SQL実行
$prepare->execute(); //SQL文を実行
$result = $prepare->fetch(PDO::FETCH_ASSOC); //実行結果を$resultに代入

if ($result) {
    // データが取得できた場合の処理
    var_dump($result);
    $user_id = $result['id'];
    $user_password = $result['password'];

    echo "<br>\n";
    echo "IDは、" . $user_id . "<br>\n";
    echo "パスワードは、" . $user_password . "<br>\n";

} else {
    // データが取得できなかった場合の処理（認証失敗など）
    echo "ユーザが見つかりませんでした。ID、パスワードを確認してください。";
}