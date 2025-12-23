<?php
// わざと危険そうな文字を含める
$input = '<script>alert("XSS")</script> " \' & < >';

$escaped = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>htmlspecialchars 動作確認</title>
</head>
<body>
  <h1>htmlspecialchars 動作確認</h1>

  <h2>そのまま表示（危険：HTMLとして解釈される）</h2>
  <div><?= $input ?></div>

  <h2>htmlspecialchars して表示（安全：文字として表示される）</h2>
  <div><?= $escaped ?></div>

  <h2>変換後の文字列（確認用）</h2>
  <pre><?= $escaped ?></pre>
</body>
</html>
