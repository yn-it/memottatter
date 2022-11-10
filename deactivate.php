<?php
// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「　退会ページ　「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ファイル名: deactivate.php】POST送信があります');
  debug('【ファイル名: deactivate.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // POST情報を変数に格納
  $withdrawal = $_POST['withdrawal'];

  // 例外処理
  try {

    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $_SESSION['user_id']);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {

      // セッションを削除
      session_unset();

      // メッセージをセッションに格納
      $_SESSION['msg_suc'] = SUC07;

      // ホーム画面へ遷移
      debug('【ファイル名: deactivate.php】ホーム画面へ遷移します');
      header("Location:home.php");
      return;

    } else {
      $err_msg['common'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('【ファイル名: deactivate.php】エラー発生: ' . $e->getMessage());
  }
}

?>


<?php $siteTitle = '退会ページ'; ?>
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
            退会される方は、下記の「退会する」ボタンをクリックしてください
          </p>
        </div>
        <input type="submit" value="退会する" class="c-button" name="withdrawal">
      </form>

    </div>

    <!-- link -->
    <a href="home.php" class="c-link">&lt; トップページへ</a>

  </main>



  <!-- footer -->
  <?php require('footer.php'); ?>