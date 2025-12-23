<?php
    /** データベース接続URL */
$dsn = 'mysql:host=localhost:3306;dbname=phpdb';
/** ユーザー名 */
$username = 'root';
/** パスワード */
$password = '';
/**
 * データベースの接続を取得する。
 */
$con = null;
try {
    $con = new PDO($dsn, $username, $password);
    echo "接続成功\n";
} catch(PDOException $e) {
    echo "接続失敗: " . $e->getMessage() . "\n";
    exit();
}
