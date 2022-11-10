<?php

// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「　ログインページ　「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ページ名: login.php】POST送信があります');
  debug('【ページ名: login.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // 変数に入力フォームの値を格納
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  // 未入力チェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ページ名: login.php】未入力チェックOKです');

    // Email形式チェック
    validEmail($email, 'email');

    // Emailの最大文字数チェック
    validMaxLen($email, 'email');

    // パスワードチェック
    validPass($pass, 'pass');

    // バリデーションがOKの場合
    if (empty($err_msg)) {
      debug('【ページ名: login.php】バリデーションチェックOKですので例外処理に入ります');

      // 例外処理
      try {

        // DBへ接続
        $dbh = dbConnect();

        // SQL文作成
        $sql = 'SELECT id, password FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ結果の値を変数に格納
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        debug('【ページ名: login.php】クエリ結果の値は [ ' . print_r($result, true) . ' ] です');

        // パスワード照合
        // パスワードが一致した場合
        if ((!empty($result)) && password_verify($pass, $result['password'])) {
          debug('【ページ名: login.php】パスワードが一致しました');
          $sessionLimit = 60 * 60;

          // ログイン日時を現在日時に
          $_SESSION['login_at'] = time();

          // ログイン保持にチェックがある場合
          if ($pass_save) {
            debug('【ページ名: login.php】ログイン保持にチェックがあります');

            // ログイン有効期限を30日に設定
            $_SESSION['login_limit'] = $sessionLimit * 24 * 30;

            // ログイン保持にチェックがない場合  
          } else {
            debug('【ページ名: login.php】ログイン保持にチェックがありません');

            // ログイン有効期限を1時間に設定
            $_SESSION['login_limit'] = $sessionLimit;
          }

          // ユーザーIDを格納
          $_SESSION['user_id'] = $result['id'];

          // メッセージをセッションに格納
          $_SESSION['msg_suc'] = SUC08;

          // ホーム画面へ遷移
          debug('【ページ名: login.php】ログインに成功しましたのでホーム画面へ遷移します');
          header("Location:home.php");
          exit;

          // パスワードが一致しなかった場合
        } else {
          debug('【ページ名: login.php】パスワードが一致しませんでした');

          // エラーメッセージ表示
          $err_msg['common'] = MSG09;
        }
      } catch (Exception $e) {
        error_log('【ページ名: login.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
        $err_msg['common'] = MSG08;
      }
    }
  }
}

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>

<?php $siteTitle = 'ログイン'; ?>
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
        <h2 class="c-form__title">ログイン</h2>
        <span class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg['common'])) echo 'has-error'; ?>">
          <?php echo getErrMsg('common'); ?>
        </span>

        <div class="c-form__group js-formGroupEmail">
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

        <label class="c-form__label">
          <input type="checkbox" name="pass_save" class="c-input__checkbox">ログイン状態を保持する
        </label>
        <input type="submit" value="ログイン" class="c-button">
        <a href="passRemindSend.php" class="c-form__link">パスワードをお忘れの場合</a>
      </form>
    </div>

  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>