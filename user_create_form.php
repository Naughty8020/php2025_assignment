<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>本の貸し出し登録</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-100">

<!-- サイドバーを読み込む -->
<?php include 'sidebar.php'; ?>

<!-- メインコンテンツ -->
<div class="flex-1 flex justify-center items-center p-8">

  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-center">本の貸し出し登録</h1>

    <form method="post" action="book_insert.php" class="space-y-4">
      
      <div>
        <label class="block mb-1 font-medium">本の名前</label>
        <input type="text" name="book_name" required
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block mb-1 font-medium">日付</label>
        <input type="date" name="date" required
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block mb-1 font-medium">貸出状態</label>
        <select name="is_borrowed"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          <option value="0" selected>返却中</option>
          <option value="1">貸出中</option>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">貸した人の名前</label>
        <input type="text" name="borrower_name"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <button type="submit"
              class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">登録</button>
    </form>

    <div class="mt-4 text-center">
      <a href="book_list.php"
         class="inline-block bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400 transition">本の一覧へ戻る</a>
    </div>
  </div>

</div>

</body>
</html>
