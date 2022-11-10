<?php
// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「　パスワードリマインダー送信ページ　「「「「「「「「「「「「「「「「「');
debugLogStart();

// POST送信されている場合
if (!empty($_POST)) {
  debug('【ファイル名: passRemindSend.php】POST送信があります');
  debug('【ファイル名: passRemindSend.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // 入力フォームの値を格納
  $email = $_POST['email'];

  // 未入力チェック
  validRequired($email, 'email');

  // 未入力チェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passRemindSend.php】未入力チェックOKですのでバリデーションチェックに入ります');

    // Email形式チェック
    validEmail($email, 'email');

    // 最大文字数チェック
    validMaxLen($email, 'email');
  }

  // バリデーションチェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passRemindSend.php】バリデーションチェックがOKですので例外処理に入ります');

    // 例外処理
    try {

      // DBへ接続
      $dbh = dbConnect();

      // SQL文作成
      $sql = 'SELECT COUNT(email) FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ結果の値を1レコード取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // クエリ成功の場合
      if ($result && !empty(array_shift($result))) {
        debug('【ファイル名: passRemindSend.php】クエリに成功しましたのでメール送信します');

        // メッセージをセッションに格納
        $_SESSION['msg_suc'] = SUC02;

        // 認証キーを変数に格納
        $auth_key = makeRandKey();
        debug('【ファイル名: passRemindSend.php】発行された認証キーは [ ' . $auth_key . ' ] です');

        // メール送信準備
        $from = 'info_memottatter@yn-it.com';
        $to = $email;
        $subject = '【めもったったー】パスワード再発行用認証キー送付のお知らせ';
        $comment = <<<EOT
めもったったーをご利用いただき、誠にありがとうございます。

パスワード再発行用認証キーを発行しました。

発行された認証キーは 【{$auth_key}】になります。


下記のURLをクリックして
発行されました認証キーをご入力頂きますと
パスワードが再発行されます。
https://yn-it.com/memottatter/passRemindRecieve.php

※認証キーの有効期限は60分になります


―――――――――――――――――――――――――――――――――――――――――――――――――――――
めもったったー
https://yn-it.com/memottatter/home.php
―――――――――――――――――――――――――――――――――――――――――――――――――――――

EOT;

        // メール送信
        sendMail($from, $to, $subject, $comment);

        // 認証に必要な情報をセッションに保存
        $_SESSION['auth_key'] = $auth_key;
        $_SESSION['auth_email'] = $email;
        $_SESSION['auth_key_limit'] = time() + (60 * 60); //現在時刻より60分有効
        debug('【ファイル名: passRemindSend.php】認証に必要なセッション情報は [ ' . print_r($_SESSION, true) . ' ] です');

        // 認証キー入力ページへ遷移
        debug('【ファイル名: passRemindSend.php】認証キー入力ページへ遷移します');
        header("Location:passRemindRecieve.php");
      } else {
        debug('【ファイル名: passRemindSend.php】登録がないメールアドレスです');
        $err_msg['email'] = '登録がないメールアドレスです';
      }
    } catch (Exception $e) {
      error_log('【ファイル名: passRemindSend.php】エラー発生' . $e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
}
?>


<?php $siteTitle = 'パスワードリマインダー送信'; ?>
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
            メールアドレスを入力して「送信する」ボタンを押してください。<br>
            ご指定のメールアドレス宛に認証キーをお送りします。
          </p>
        </div>

        <div class="c-form__group">
          <label class="c-form__label">
            メールアドレス
            <input type="text" name="email" class="p-form__input c-input js-formEmail <?php if (!empty($err_msg['email'])) echo 'has-error'; ?>" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <span class="c-form__areaMsg js-emailAreaMsg <?php if (!empty($err_msg['email'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('email') ?>
          </span>
        </div>

        <input type="submit" value="送信する" class="c-button" name="send">
      </form>
    </div>

    <!-- link -->
    <a href="login.php" class="c-link">&lt; ログインページへ</a>

  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>