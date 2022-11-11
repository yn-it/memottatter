<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「　ajaxDelete ページ　「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// POST送信、ユーザーID、ログインがある場合
if (isset($_POST) && isset($_POST['boardid']) && isLogin()) {
  debug('【ページ名: ajaxDelete.php】POST送信、ユーザーID、ログインがあります');
  debug('【ページ名: ajaxDelete.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // POST情報を変数に格納
  $b_id = $_POST['boardid'];

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'UPDATE board SET delete_flg = 1 WHERE id = :b_id AND delete_flg = 0';
    $data = array(':b_id' => $b_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if ($stmt) {

      // メッセージをセッションに格納
      $_SESSION['msg_suc'] = SUC06;
      debug('【ページ名: ajaxDelete.php】メッセージは [ ' . $_SESSION['msg_suc'] . ' ] です');
      return;
      
      // ホーム画面へ遷移
      debug('【ページ名: ajaxDelete.php】ホーム画面へ遷移します');

    }
  } catch (Exception $e) {
    error_log('【ページ名: ajaxDelete.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
  }
} else {
  debug('【ページ名: ajaxDelete.php】POST送信、ユーザーID、ログインがありません');
}
