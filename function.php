<?php
// =========================================
//  ログ設定
// =========================================
// E_STRICTレベル以外のエラーを出力する
error_reporting(E_ALL);

// 画面にエラーを表示する (本番ではOff)
ini_set('display_errors', "On");

// エラーログを保存
ini_set('log_errors', "On");

// エラーログの出力ファイルを設定
ini_set('error_log', 'php.log');


// =========================================
//  デバッグ出力用関数
// =========================================
function debug($str)
{
  $debug_flg = false; // 本番ではfalse
  switch (!empty($debug_flg)) {
    case $str === null:
      error_log('デバッグ: 中身は空です');
      break;
    case $str:
      error_log('デバッグ: ' . $str);
  }
}


// =========================================
//  エラーメッセージ表示用関数
// =========================================
// グローバル変数 (エラーメッセージ表示用)
$err_msg = array();

function getErrMsg($key)
{
  global $err_msg;
  if (isset($err_msg[$key])) {
    return $err_msg[$key];
  }
}


// =========================================
//  サニタイズ関数
// =========================================
function sanitize($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}


// =========================================
//  セッション設定
// =========================================
// セッションファイルの置き場を変更
session_save_path("/var/tmp/");
debug('セッションファイルの置き場は [ ' . session_save_path() . ' ] です');

// セッションの有効期限を30日に設定
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);

// クッキーの有効期限を30日に設定
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);

// セッションスタート
session_start();
debug('セッションスタートしました');
debug('セッションIDは [ ' . session_id() . ' ] です');

// セッションIDを新しく生成
session_regenerate_id();
debug('セッションを新しく生成しました');
debug('新しいセッションIDは [ ' . session_id() . ' ] です');


// =========================================
//  デバッグログ出力関数
// =========================================
function debugLogStart()
{

  // セッションID
  debug('【関数名: debugLogStart】セッションIDは [ ' . session_id() . ' ] です');

  // セッション変数の中身
  debug('【関数名: debugLogStart】セッション変数の中身は [ ' . print_r($_SESSION, true) . ' ] です');

  // 現在日時
  debug('【関数名: debugLogStart】現在日時は [ ' . time() . ' ] です');

  // ログイン有効期限
  if (!empty($_SESSION['login_at']) && !empty($_SESSION['login_limit'])) {
    debug('【関数名: debugLogStart】ログイン有効期限は [ ' . ($_SESSION['login_at'] + $_SESSION['login_limit']) . ' ] です');
  }
}


// =========================================
//  ログイン認証関数
// =========================================
function isLogin()
{

  // ログインしている場合
  if (!empty($_SESSION['user_id'])) {
    debug('【関数名: isLogin】ログイン済みユーザーです');

    // ログイン有効期限が過ぎている場合
    if (($_SESSION['login_at'] + $_SESSION['login_limit']) < time()) {
      debug('【関数名: isLogin】ログイン有効期限が過ぎましたのでログアウトします');

      // ログアウトする
      $_SESSION = array();
      session_destroy();

      // falseを返す
      return false;
    } else {

      // trueを返す
      return true;
    }

    // ログインしていない場合
  } else {
    debug('【関数名: isLogin】未ログインユーザーです');

    // falseを返す
    return false;
  }
}


// =========================================
//  DB接続関数
// =========================================
function dbConnect()
{
  debug('【関数名: dbConnect】DBへ接続準備します');

  // DBへの接続準備 (GitHubに上げる場合はrootにする)
  $dsn = 'mysql:dbname=ynit_memotatter;host=localhost;charset=utf8';
  $user = 'root';
  $pass = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
  );

  // PDOオブジェクト生成
  $dbh = new PDO($dsn, $user, $pass, $options);

  // PDOオブジェクトを返却
  return $dbh;
}


// =========================================
//  クエリ関数
// =========================================
function queryPost($dbh, $sql, $data)
{
  debug('【関数名: queryPost】クエリを作成します');

  // クエリ作成
  $stmt = $dbh->prepare($sql);

  // プレースホルダに値をセット
  if ($stmt->execute($data)) {
    debug('【関数名: queryPost】SQL文を実行し、クエリに成功しました');
    debug('【関数名: queryPost】作成されたSQL文は [ ' . print_r($stmt, true) . ' ] です');

    // 値を返却
    return $stmt;
  } else {
    debug('【関数名: queryPost】クエリに失敗しました');
    $err_msg['common'] = MSG08;
    return false;
  }
}


// =========================================
//  定数
// =========================================
define('MSG01', '入力必須項目です');
define('MSG02', 'メールアドレスの形式で入力してください');
define('MSG03', 'このメールアドレスは既に使用されています');
define('MSG04', '文字以上で入力してください');
define('MSG05', '文字以内で入力してください');
define('MSG06', '半角英数字のみご利用いただけます');
define('MSG07', 'パスワード (確認) が正しくありません');
define('MSG08', 'エラーが発生しました。しばらく経ってからご利用ください');
define('MSG09', 'メールアドレスまたはパスワードが正しくありません');
define('MSG10', '現在のパスワードと同じです');
define('MSG11', '新しいパスワード (確認) が正しくありません');
define('MSG12', '文字で入力してください');

define('SUC01', 'パスワードを変更したったー');
define('SUC02', 'メールを送信したったー');
define('SUC03', 'プロフィール更新したったー');
define('SUC04', 'めもったったー');
define('SUC05', 'メモを変更したったー');
define('SUC06', 'メモを削除したったー');
define('SUC07', '退会したったー');
define('SUC08', 'ログインしたったー');


// =========================================
//  バリデーションチェック関数
// =========================================
// 未入力チェック
function validRequired($str, $key)
{
  global $err_msg;
  if ($str === '') {
    debug('【関数名: validRequired】入力必須項目です');
    $err_msg[$key] = MSG01;
  }
}


// 固定長チェック
function validLength($str, $key, $len = '')
{
  global $err_msg;
  if (mb_strlen($str) !== $len) {
    debug('【関数名: validLength】' . $len . '文字で入力してください');
    $err_msg[$key] = $len . MSG12;
  }
}


// 最小文字数チェック
function validMinLen($str, $key, $len = 8)
{
  global $err_msg;
  $str = str_replace("\r\n", '', $str);
  if (mb_strlen($str) < $len) {
    debug('【関数名: validMinLen】' . $len . '文字以上で入力してください');
    $err_msg[$key] = $len . MSG04;
  }
}


// 最大文字数チェック
function validMaxLen($str, $key, $len = 255)
{
  global $err_msg;
  $str = str_replace("\r\n", '', $str);
  if (mb_strlen($str) > $len) {
    debug('【関数名: validMaxLen】' . $len . '文字以内で入力してください');
    $err_msg[$key] = $len . MSG05;
  }
}


// 半角英数字チェック
function validHalf($str, $key)
{
  global $err_msg;
  if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
    $err_msg[$key] = MSG06;
  }
}

// Email形式チェック
function validEmail($str, $key)
{
  global $err_msg;
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
    debug('【関数名: validEmail】メールアドレスの形式で入力してください');
    $err_msg[$key] = MSG02;
  }
}


// Email重複チェック
function validEmailDup($email)
{
  global $err_msg;

  // 例外接続
  try {

    // DBへ接続
    $dbh = dbConnect();

    // 登録されたEmailの件数を取得
    $sql = 'SELECT count(email) FROM users WHERE email = :email';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ結果の値を変数に格納
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    debug('【関数名: validEmailDup】クエリ結果の値は [ ' . print_r($result, true) . ' ] です');

    // 件数がある場合 (バリデーションエラー)
    if ($result && !empty(array_shift($result))) {
      debug('【関数名: validEmailDup】このメールアドレスは既に使われています');
      $err_msg['email'] = MSG03;
    }
  } catch (Exception $e) {
    debug('【関数名: validEmailDup】エラー発生');
    error_log('【関数名: validEmail】エラーが発生しました' . $e->getMessage());
  }
}


// パスワード同値チェック
function validPassMatch($str1, $str2, $key)
{
  global $err_msg;
  if ($str1 !== $str2) {
    debug('【関数名: validPassMatch】パスワードが正しくありません');
    $err_msg[$key] = MSG07;
  }
}

// パスワードチェック
function validPass($str, $key)
{

  // 半角英数字チェック
  validHalf($str, $key);

  // 最小文字数チェック
  validMinLen($str, $key);

  // 最大文字数チェック
  validMaxLen($str, $key);
}


// =========================================
//  ユーザー情報取得関数
// =========================================
function dbGetUser($u_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT id, name, email, password, user_pic, login_at, create_at, update_at FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    debug('【関数名: dbGetUsers】作成したSQL文は [ ' . $sql . ' ] です');

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {

      // クエリ結果の値を返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {

      // falseを返却
      return false;
    }
  } catch (Exception $e) {
    error_log('【関数名: dbGetUsers】エラー発生 [ ' . $e->getMessage() . ' ]');
    $err_msg['common'] = MSG08;
  }
}


// =========================================
//  プロフィール情報取得関数
// =========================================
function dbGetProf($u_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT id, name, user_pic, intro FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if ($stmt) {

      // クエリ結果の値を返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('【関数名: getProf】エラー発生 [ ' . $e->getMessage() . ' ] ');
    $err_msg['common'] = MSG08;
  }
}

// =========================================
//  自分の掲示板リスト取得関数
// =========================================
function getMyBoardsList($currentMinNum = 1, $span, $u_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT b.id AS board_id, u.id AS user_id, u.name, u.user_pic, b.message, b.board_pic, b.send_at, b.update_at FROM board AS b INNER JOIN users AS u ON b.user_id = u.id WHERE b.user_id = :u_id AND b.delete_flg = 0 AND u.delete_flg = 0 ORDER BY b.update_at DESC';
    debug('【関数名: getMyBoardsList】作成されたSQL文は [ ' . $sql . ' ] です');

    $data = array(':u_id' => $u_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // 総レコード数を変数に格納
    $result['total'] = $stmt->rowCount();

    // 総ページを変数に格納
    $result['total_page'] = ceil($result['total'] / $span);

    // LIMITとOFFSETを結合
    $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
    debug('【関数名: getMyBoardsList】作成されたSQL文は [ ' . $sql . ' ] です');

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $result['data'] = $stmt->fetchAll();
      return $result;
    }
  } catch (Exception $e) {
    error_log('【関数名: getMyBoardsList】エラー発生 [ ' . $e->getMessage() . ' ] ');
  }
}


// =========================================
//  掲示板リスト取得関数
// =========================================
function getBoardsList($currentMinNum = 1, $span, $search_box)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // 検索フォームに値がある場合
    if (!empty($_GET['search-box'])) {
      debug('【関数名: getBoardList】検索フォームに値があります');

      // SQL文作成
      $sql = "SELECT b.id AS board_id, u.id AS user_id, u.name, u.user_pic, b.message, b.board_pic, b.send_at, b.update_at, b.delete_flg FROM board AS b INNER JOIN users AS u ON b.user_id = u.id WHERE b.delete_flg = 0 AND u.delete_flg = 0 AND b.message LIKE '%$search_box%' OR u.name LIKE '%$search_box%' ORDER BY b.update_at DESC";
      debug('【関数名: getBoardList】作成されたSQL文は [ ' . $sql . ' ] です');

      // クエリ実行
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':search-box', $search_box, PDO::PARAM_STR);
      $stmt->execute();

      // 検索に引っかかった総レコード数を変数に格納
      $result['total'] = $stmt->rowCount();
      debug('【関数名: getBoardList】検索に引っかかった総レコード数は [ ' . $result['total'] . ' ] です');

      // 検索に引っかかった総ページ数を変数に格納
      $result['total_page'] = ceil($result['total'] / $span);
      debug('【関数名: getBoardList】検索に引っかかった総ページ数は [ ' . $result['total_page'] . ' ] です');

      // LIMITとOFFSETを結合
      $sql .= " LIMIT " . $span . " OFFSET " . $currentMinNum;
      debug('【関数名: getBoardList】作成されたSQL文は [ ' . $sql . ' ] です');

      // クエリ実行
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':search-box', $search_box, PDO::PARAM_STR);
      $stmt->execute();

      // クエリ成功の場合
      if ($stmt) {

        // 件数に引っかかった全データを変数に格納し、返却
        $result['data'] = $stmt->fetchAll();
        return $result;
      }

      // 検索フォームに値がない場合
    } else {
      debug('【関数名: getBoardList】検索フォームに値がありません');

      // SQL文作成
      $sql = 'SELECT b.id AS board_id, u.id AS user_id, u.name, u.user_pic, b.message, b.board_pic, b.send_at, b.update_at FROM board AS b INNER JOIN users AS u ON b.user_id = u.id WHERE b.delete_flg = 0 AND u.delete_flg = 0 ORDER BY b.
      update_at DESC';
      $data = array();

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // 総レコード数の件数を変数に格納
      $result['total'] = $stmt->rowCount();
      debug('【関数名: getBoardList】総レコードの件数は [ ' . $result['total'] . ' ] です');

      // 総ページ数を変数に格納
      $result['total_page'] = ceil($result['total'] / $span);
      debug('【関数名: getBoardList】総ページ数は [ ' . $result['total_page'] . ' ] です');

      // LIMITとOFFSETを結合
      $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
      $data = array();

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {

        // 全データを変数に格納し、返却
        $result['data'] = $stmt->fetchAll();
        return $result;
      }
    }
  } catch (Exception $e) {
    error_log('【関数名: getBoardList】エラー発生 [ ' . $e->getMessage() . ' ] ');
  }
}


// =========================================
//  掲示板ID取得関数
// =========================================
function getBoardsData($b_id, $u_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT b.id AS board_id, b.user_id, u.name, u.user_pic, b.message, b.board_pic, b.send_at, b.update_at FROM board AS b INNER JOIN users AS u ON b.user_id = u.id WHERE b.id = :b_id AND b.user_id = :u_id AND b.delete_flg = 0 AND u.delete_flg = 0';
    $data = array(':b_id' => $b_id, ':u_id' => $u_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('【関数名: getBoardsId】エラー発生: ' . $e->getMessage() . ' ] ');
  }
}


// =========================================
//  画像処理関数
// =========================================
function uploadImg($file, $key)
{

  // エラーメッセージ用変数
  global $err_msg;

  // 画像がある場合
  if (isset($file['error']) && is_int($file['error'])) {
    debug('【関数名: uploadImg】画像があります');
    debug('【関数名: uploadImg】画像情報は [ ' . print_r($file, true) . ' ] です');

    // バリデーションチェック
    // 例外処理
    try {
      switch ($file['error']) {
        case UPLOAD_ERR_OK:
          debug('【関数名: uploadImg】エラーはありません');
          break;
        case UPLOAD_ERR_NO_FILE:
          debug('【関数名: uploadImg】画像ファイルが選択されてません');
          throw new RuntimeException('画像ファイルが選択されてません');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          debug('【関数名: uploadImg】画像ファイルが大きすぎます');
          throw new RuntimeException('画像ファイルが大きすぎます');
        default:
          debug('【関数名: uploadImg】その他のエラーが発生しました');
          throw new RuntimeException('その他のエラーが発生しました');
      }

      // MIMEタイプチェック
      $type = @exif_imagetype($file['tmp_name']);
      debug('【関数名: uploadImg】MIMEタイプは [ ' . $type . ' ] です');

      // 画像形式が対応していない場合
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        debug('【関数名: uploadImg】画像形式が未対応です');
        throw new RuntimeException('画像形式が未対応です');
      }

      // ファイル名決定
      $path = 'uploads/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);

      // ファイル保存
      if (move_uploaded_file($file['tmp_name'], $path)) {
        debug('【関数名: uploadImg】ファイル保存に成功しました');
      } else {
        debug('【関数名: uploadImg】ファイル保存時にエラーが発生しました');
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      // ファイルの権限を変更
      chmod($path, 0644);

      // ファイルパスを返却
      return $path;
    } catch (RuntimeException $e) {
      debug('【関数名: uploadImg】エラーは発生しました [ ' . $e->getMessage() . ' ]');
      $err_msg[$key] = $e->getMessage();
    }
  }
}


// =========================================
//  メール作成関数
// =========================================
function sendMail($from, $to, $subject, $comment)
{

  // データが全て入っている場合
  if (!empty($to) && !empty($subject) && !empty($comment)) {
    debug('【関数名: sendMail】データが全て入ってます');

    // 言語設定
    mb_language("Japanese");

    // エンコーディング設定
    mb_internal_encoding("UTF-8");

    // メール送信
    $result = mb_send_mail($to, $subject, $comment, "From: " . $from);

    // 送信結果のログを出力
    if ($result) {
      debug('【関数名: sendMail】メールが送信されました');
      debug('【関数名: sendMail】送信者: ' . $from);
      debug('【関数名: sendMail】受信者: ' . $to);
      debug('【関数名: sendMail】件名: ' . $subject);
      debug('【関数名: sendMail】内容: ' . $comment);
    } else {
      debug('【関数名: sendMail】メールの送信に失敗しました');
    }
  }
}


// =========================================
//  セッション取得関数 (1回のみの取得)
// =========================================
function getSessionFlash($key)
{

  // セッションが入っている場合
  if (!empty($_SESSION[$key])) {
    debug('【関数名: getSessionFlash】セッションの中身は [ ' . $_SESSION[$key] . ' ] です');

    // セッションを変数に格納
    $data = $_SESSION[$key];

    // セッションを空にする
    $_SESSION[$key] = '';

    // 変数を返却
    return $data;
  }
}

// =========================================
//  認証キー発行用関数
// =========================================
function makeRandKey($len = 8)
{

  // 空の変数を準備
  $str = '';

  // 文字列を変数に格納
  $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

  // ランダムな文字列を連結 (8文字)
  for ($i = 0; $i < $len; ++$i) {
    $str .= $chars[random_int(0, 55)];
  }

  // 文字列を返却
  return $str;
}


// =========================================
//  フォーム入力保持関数
// =========================================
function getFormData($str, $flg = false)
{

  // ユーザー情報を変数に格納
  $formData = dbGetProf($_SESSION['user_id']);

  // エラーメッセージ用変数
  global $err_msg;

  // GETかPOSTを判定
  if ($flg) {
    $method = $_GET;
  } else {
    $method = $_POST;
  }

  // ユーザー情報がある場合
  if (!empty($formData)) {

    // フォームにエラーがある場合
    if (!empty($err_msg[$str])) {

      // GETかPOSTにデータがある場合
      if (isset($method[$str])) {

        // そのまま表示
        return sanitize($method[$str]);
      }

      // フォームにエラーがない場合
    } else {

      // $methodデータがあり、DBの情報と違う場合
      if (isset($method[$str]) && $method[$str] !== $formData[$str]) {

        // $methodデータをそのまま表示
        return sanitize($method[$str]);

        // $methodデータがない場合
      } else {

        // DBの情報をそのまま表示
        return sanitize($formData[$str]);
      }
    }

    // ユーザー情報がない場合
  } else {

    // $methodデータをそのまま表示
    if (isset($method[$str])) {
      return sanitize($method[$str]);
    }
  }
}


// =========================================
//  ページネーション関数
// =========================================
function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5)
{

  // 5ページ目の場合
  if ($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;

    // 4ページ目の場合
  } else if ($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;

    // 2ページ目の場合
  } else if ($currentPageNum == 2 && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;

    // 1ページ目の場合
  } else if ($currentPageNum == 1 && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum;
    $maxPageNum = $pageColNum;

    // 総ページ数が5ページ以下の場合
  } else if ($totalPageNum < $pageColNum) {
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;

    // それ以外は左右に2個ずつ出す
  } else {
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  // HTML出力
  echo '<div class="c-pagination">';
  if ($maxPageNum != 0) {
    echo '<ul class="c-pagination__list">';
    if ($currentPageNum != 1) :
      echo '<li class="c-pagination__item">';
      echo '<a href="?page=1' . $link . '" class="c-pagination__link">';
      echo '<i class="fa-solid fa-angle-left"></i>';
      echo '</a>';
      echo '</li>';
    endif;

    for ($i = $minPageNum; $i <= $maxPageNum; $i++) :
      echo '<li class="c-pagination__item">';
      echo '<a href="?page=' . $i . $link . '" class="c-pagination__link ';
      if ($currentPageNum == $i) {
        echo 'c-pagination__link--active';
      }
      echo '">' . $i . '</a>';
      echo '</li>';
    endfor;

    if ($currentPageNum != $maxPageNum) :
      echo '<li class="c-pagination__item">';
      echo '<a href="?page=' . $totalPageNum . $link . '" class="c-pagination__link"><i class="fa-solid fa-angle-right"></i></a>';
      echo '</li>';
    endif;

    echo '</ul>';
  } else {
    echo '<ul style="display:none;">';
    echo '</ul>';
  }
  echo '</div>';
}


// =========================================
//  いいね情報確認関数
// =========================================
function isGood($b_id, $u_id, $gu_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT board_id, user_id, good_user, create_at, update_at FROM good WHERE board_id = :b_id AND user_id = :u_id AND good_user = :gu_id';
    $data = array(':b_id' => $b_id, ':u_id' => $u_id, ':gu_id' => $gu_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if ($stmt) {

      // クエリ結果の件数を取得
      $result = $stmt->rowCount();
      debug('【関数名: isGood】いいねの件数は [ ' . $result . ' ] 件です');
      return $result;
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('【関数名: isGood】エラー発生 [ ' . $e->getMessage() . ' ] ');
  }
}


// =========================================
//  いいねの数取得関数
// =========================================
function isGoodCount($b_id)
{

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'SELECT board_id FROM good WHERE board_id = :b_id';
    $data = array(':b_id' => $b_id);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetchAll();
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('【関数名: isGoodCount】エラー発生 [ ' . $e->getMessage()) . ' ] ';
  }
}
