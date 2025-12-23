<?php
// user_delete_execute.php
require 'pdo_connect.php';

// POSTで受け取る
$id = $_POST['id'] ?? '';
$id = trim($id);

if ($id === '') {
    exit('IDが指定されていません。');
}

try {
    // 念のため存在確認（削除メッセージ用）
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        exit('ユーザーが見つかりませんでした。');
    }

    // 削除実行
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    // 削除できたか確認
    if ($stmt->rowCount() > 0) {
        $safe_id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        echo "削除しました：ID = {$safe_id}<br>";
        echo '<a href="pdo_select_all.php">一覧へ戻る</a>';
    } else {
        echo "削除対象が見つかりませんでした。<br>";
        echo '<a href="pdo_select_all.php">一覧へ戻る</a>';
    }

} catch (PDOException $e) {
    echo "削除に失敗しました。";
    // 開発中だけ詳細を見たいなら一時的に↓
    // echo "<br>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
