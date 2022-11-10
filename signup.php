<?php

// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「　ユーザー登録ページ　「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ページ名: signup.php】POST送信があります');
  debug('【ページ名: signup.php】POST情報 [ ' . print_r($_POST, true) . ' ] です');

  // 変数に入力フォームの値を格納
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  // 未入力チェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ページ名: signup.php】未入力チェックOKです');

    // Email形式チェック
    validEmail($email, 'email');

    // Emailの最大文字数チェック
    validMaxLen($email, 'email');

    // Email重複チェック
    validEmailDup($email, 'email');

    // パスワードチェック
    validPass($pass, 'pass');

    // パスワード同値チェック
    validPassMatch($pass, $pass_re, 'pass_re');

    // バリデーションチェックがOKの場合
    if (empty($err_msg)) {
      debug('【ページ名: signup.php】全てのバリデーションチェックOKですので例外処理に入ります');

      // 例外処理
      try {

        // DBへ接続
        $dbh = dbConnect();

        // SQL文作成
        $sql = 'INSERT INTO users (email, password, login_at, create_at) VALUES (:email, :pass, :login_at, :create_at)';
        $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_at' => date('Y-m-d H:i:s'), ':create_at' => date('Y-m-d H:i:s'));

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if ($stmt) {

          // セッション情報を格納
          // ユーザーID
          $_SESSION['user_id'] = $dbh->lastInsertId();

          // ログイン日時を現在日時に
          $_SESSION['login_at'] = time();

          // ログイン有効期限を1時間に設定
          $_SESSION['login_limit'] = 60 * 60;
          debug('【ページ名: signup.php】セッション情報は [ ' . print_r($_SESSION, true) . ' ] です');

          // ホーム画面へ遷移
          debug('【ページ名: signup.php】ユーザー登録に成功しましたのでホーム画面へ遷移します');
          header("Location:home.php");
        } else {
          return false;
          debug('【ページ名: signup.php】ユーザー登録に失敗しました');
        }
      } catch (Exception $e) {
        error_log('【ページ名: signup.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
        $err_msg['common'] = MSG08;
      }
    }
  }
}

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');
?>

<?php $siteTitle = 'ユーザー登録'; ?>
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
        <h2 class="c-form__title">ユーザー登録</h2>
        <span class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg['common'])) echo 'has-error'; ?>">
          <?php echo getErrMsg('common'); ?>
        </span>

        <div class="c-form__group">
          <label class="c-form__label">
            メールアドレス
            <input type="text" name="email" class="p-form__input c-input js-formEmail <?php if (!empty($err_msg['email'])) echo 'has-error'; ?>" value="<?php if (!empty($email)) echo $email; ?>">
          </label>
          <span class="c-form__areaMsg js-emailAreaMsg <?php if (!empty($err_msg['email'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('email'); ?>
          </span>
        </div>

        <div class="c-form__group js-formGroupPass">
          <label class="c-form__label">
            パスワード
            <input type="password" name="pass" class="p-form__input c-input js-formPass <?php if (!empty($err_msg['pass'])) echo 'has-error'; ?>">
          </label>
          <span class="c-form__areaMsg js-passAreaMsg <?php if (!empty($err_msg['pass'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('pass'); ?>
          </span>
        </div>

        <div class="c-form__group">
          <label class="c-form__label">
            パスワード (確認)
            <input type="password" name="pass_re" class="p-form__input c-input js-formPassRe <?php if (!empty($err_msg['pass_re'])) echo 'has-error'; ?>">
          </label>
          <span class="c-form__areaMsg js-passReAreaMsg <?php if (!empty($err_msg['pass_re'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('pass_re'); ?>
          </span>
        </div>

        <input type="submit" value="登録する" class="c-button">

      </form>
    </div>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>