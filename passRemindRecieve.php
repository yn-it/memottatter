<?php
// 関数ファイル読み込み
require('function.php');
debug('「「「「「「「「「「「「「　パスワードリマインダー受信ページ　「「「「「「「「「「「「「「「「「');
debugLogStart();
debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// セッションに認証キーが入っていない場合
if (empty($_SESSION['auth_key'])) {
  debug('【ファイル名: passRemindRecieve.php】セッションに認証キーが入っていませんので、送信ページへリダイレクトします');

  // 送信ページへリダイレクト
  header("Location:passRemindSend.php");
}

// POST送信されている場合
if (!empty($_POST)) {
  debug('【ファイル名: passRemindRecieve.php】POST送信があります');
  debug('【ファイル名: passRemindRecieve.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // 入力フォームの値を格納
  $auth_key = $_POST['token'];

  // バリデーションチェック
  // 未入力チェック
  validRequired($auth_key, 'token');

  // 未入力チェックOKの場合
  if (empty($err_msg)) {
    debug('【ファイル名: passRemindRecieve.php】未入力チェックOKです');

    // 半角英数字チェック
    validHalf($auth_key, 'token');

    // 固定長チェック
    validLength($auth_key, 'token', 8);
  }

  // 半角・固定長チェックがOKの場合
  if (empty($err_msg)) {
    debug('【ファイル名: passRemindRecieve.php】半角・固定長チェックOKです');

    // 認証キーの照合チェック
    if ($auth_key !== $_SESSION['auth_key']) {
      debug('【ファイル名: passRemindRecieve.php】認証キーが正しくありません');
      $err_msg['token'] = '認証キーが正しくありません';
    }

    // 認証キーの有効期限チェック
    if ($auth_key === $_SESSION['auth_key'] && time() > $_SESSION['auth_key_limit']) {
      debug('ファイル名: passRemindRecieve.php】認証キーの有効期限が過ぎています');
      $err_msg['token'] = '認証キーの有効期限が過ぎていますので<br>左下のリンクをクリックしてやり直してください';
    }
  }

  // 全てのバリデーションチェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passRemindRecieve.php】全てのバリデーションチェックOKですので、パスワードを生成して例外処理に入ります');

    // パスワードを生成
    $pass = makeRandKey();
    debug('【ファイル名: passRemindRecieve.php】新しく生成されたパスワードは [ ' . $pass . ' ] です');

    // 例外処理
    try {

      // DBへ接続
      $dbh = dbConnect();

      // SQL文作成
      $sql = 'UPDATE users SET password = :pass WHERE email = :email';
      $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {
        debug('【ファイル名: passRemindRecieve.php】クエリ成功しましたのでメール送信します');

        // メール送信準備
        $from = 'info_memottatter@yn-it.com';
        $to = $_SESSION['auth_email'];
        $subject = '【めもったったー】パスワード発行のお知らせ';
        $comment = <<<EOT
めもったったーをご利用いただき、誠にありがとうございます。

新しいパスワードを発行しました。

新しいパスワードは【{$pass}】になります。 

下記のURLをクリックして、新しいパスワードでログインしてください。
https://yn-it.com/memottatter/login.php

※セキュリティ上の理由により、パスワードを変更する必要がありますので
ログイン後、パスワードのご変更よろしくお願いいたします。


―――――――――――――――――――――――――――――――――――――――――――――――――――――
めもったったー
https://yn-it.com/memottatter/home.php
―――――――――――――――――――――――――――――――――――――――――――――――――――――


EOT;

        // メール送信
        sendMail($from, $to, $subject, $comment);
        debug('【ファイル名: passRemindRecieve.php】メール送信完了しましたので、セッションを削除します');

        // セッションを削除
        session_unset();

        // メッセージをセッションに格納
        $_SESSION['msg_suc'] = SUC02;

        // ログインページへ遷移
        debug('【ファイル名:passRemindRecieve.php】ログインページへ遷移します');
        header("Location:login.php");
        return;
      }
    } catch (Exception $e) {
      error_log('【ファイル名: passRemindRecieve.php】エラー発生 [ ' . $e->getMessage() . ' ]');
      $err_msg['common'] = MSG08;
    }
  }
}


debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>

<?php $siteTitle = 'パスワードリマインダー受信'; ?>
<?php require('head.php'); ?>

<body class="c-body p-body jsPositionFixed">

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- navigation -->
  <nav class="p-nav jsSpMenuTarget">
    <ul class="p-nav__menu">
      <li class="p-nav__item">
        <?php
        if (empty($_SESSION['user_id'])) {
        ?>
          <a href="login.php" class="p-nav__link">ログイン</a>
        <?php } else { ?>
          <a href="home.php" class="p-nav__link">ホーム</a>
        <?php } ?>
      </li>
      <li class="p-nav__item">
        <?php
        if (empty($_SESSION['user_id'])) {
        ?>
          <a href="signup.php" class="p-nav__link--button c-button">ユーザー登録</a>
          <a href="signup.php" class="p-nav__link p-nav__link--sp">ユーザー登録</a>
        <?php } else { ?>
          <a href="logout.php" class="p-nav__link">ログアウト</a>
        <?php } ?>
      </li>
    </ul>
  </nav>

  <!-- hamburger menu -->
  <div class="c-hamburger p-nav__spMenu jsSpMenu">
    <span class="c-hamburger__bar c-hamburger__bar--top"></span>
    <span class="c-hamburger__bar c-hamburger__bar--middle"></span>
    <span class="c-hamburger__bar c-hamburger__bar--bottom"></span>
    <span class="c-hamburger__title">MENU</span>
    <span class="c-hamburger__closeButton">CLOSE</span>
  </div>

  </div>
  </div>
  </header>

  <!-- msg slide -->
  <p class="p-header__msgSlide js-showMsg">
    <?php echo getSessionFlash('msg_suc'); ?>
  </p>

  <!-- main -->
  <main id="l-main">

    <!-- form -->
    <div class="c-form">
      <form action="" method="post" class="c-form__container">
        <span class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg['common'])) echo 'has-error'; ?>">
          <?php echo getErrMsg('common'); ?>
        </span>
        <div class="c-form__textWrap">
          <p class="c-form__text">
            メールに記載された認証キーをご入力ください
          </p>
        </div>

        <div class="c-form__group">
          <label class="c-form__label">
            認証キー
            <input type="text" name="token" class="p-form__input c-input js-validToken <?php if (!empty($err_msg['token'])) echo 'has-error'; ?>" value="<?php if (!empty($_POST['token'])) echo $_POST['token']; ?>">
          </label>
          <span class="c-form__areaMsg js-tokenAreaMsg <?php if (!empty($err_msg['token'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('token'); ?>
          </span>
        </div>

        <input type="submit" value="パスワードを再発行する" class="c-button" name="send">
      </form>
    </div>

    <!-- link -->
    <a href="passRemindSend.php" class="c-link">&lt; 認証キー発行送信ページへ</a>

  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>