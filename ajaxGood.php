<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「　ajaxGood ページ　「「「「「「「「「「「「「「「「「「');
debugLogStart();

// POST送信、ユーザーID、ログインがある場合
if (isset($_POST) && isset($_SESSION['user_id']) && isLogin()) {
  debug('【ページ名: ajaxGood.php】POST送信、ユーザーID、ログインがあります');
  debug('【ページ名: ajaxGood.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // POST情報を変数に格納
  $b_id = $_POST['boardid'];
  $u_id = $_POST['userid'];

  // いいねしたIDを変数に格納
  $gu_id = $_SESSION['user_id'];

  debug('【変数名: $b_id】$b_idは [ ' . $b_id . ' ] です');
  debug('【変数名: $u_id】$u_idは [ ' . $u_id . ' ] です');
  debug('【変数名: $gu_id】$gu_idは [ ' . $gu_id . ' ] です');

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT board_id, user_id, good_user FROM good WHERE board_id = :b_id AND user_id = :u_id AND good_user = :gu_id';
    $data = array(':b_id' => $b_id, ':u_id' => $u_id, ':gu_id' => $gu_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ結果の件数を取得し、変数を格納
    $resultCount = $stmt->rowCount();

    // レコードがある場合
    if (!empty($resultCount)) {
      debug('【ページ名: ajaxGood.php】いいね登録がありますので削除します');

      // レコードを削除
      $sql = 'DELETE FROM good WHERE board_id = :b_id AND user_id = :u_id AND good_user = :gu_id';
      $data = array(':b_id' => $b_id, ':u_id' => $u_id, ':gu_id' => $gu_id);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // レコードがない場合
    } else {
      debug('【ページ名: ajaxGood.php】いいね登録がありませんので登録します');

      // レコードを登録
      $sql = 'INSERT INTO good (board_id, user_id, good_user, create_at) VALUES (:b_id, :u_id, :gu_id, :date)';
      $data = array(':b_id' => $b_id, ':u_id' => $u_id, ':gu_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }

  } catch (Exception $e) {
    error_log('【ページ名: ajaxGood.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
  }
} else {
  debug('【ページ名: ajaxGood.php】POST送信、ユーザーID、ログインがありません');
}
