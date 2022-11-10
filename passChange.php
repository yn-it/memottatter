<?php

// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「　パスワード変更ページ　「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// DBからユーザー情報を取得
$dbGetUser = dbGetUser($_SESSION['user_id']);
debug('【変数名: $dbGetUser】DBから取得したユーザー情報は [ ' . print_r($dbGetUser, true) . ' ] です');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ファイル名: passChange.php】POST送信があります');
  debug('【ファイル名: passChange.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');

  // 変数に入力フォームの値を格納
  $pass = $_POST['pass'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  // バリデーションチェック
  // 未入力チェック
  validRequired($pass, 'pass');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  // 未入力チェックOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passChange.php】未入力チェックOKです');

    // パスワードチェック
    validPass($pass, 'pass');
    validPass($pass_new, 'pass_new');
  }

  // パスワードチェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passChange.php】パスワードチェックOKです');

    // DBに登録された現在のパスワードと入力した現在のパスワードが違う場合
    if (!password_verify($pass, $dbGetUser['password'])) {
      debug('【ファイル名: passChange.php】DBに登録された現在のパスワードと入力した現在のパスワードが違います');
      $err_msg['pass'] = '現在のパスワードが正しくありません';
    } else {
      debug('【ファイル名: passChange.php】DBに登録された現在のパスワードと入力した現在のパスワードが一致しました');
    }

    // 現在のパスワードと新しいパスワードが同じ場合
    if ($pass === $pass_new) {
      debug('【ファイル名: passChange.php】現在のパスワードと同じです');
      $err_msg['pass_new'] = MSG10;
    }

    // 新しいパスワードと確認用パスワードが違う場合
    if ($pass_new !== $pass_new_re) {
      debug('【ファイル名: passChange.php】パスワード (確認) が正しくありません');
      $err_msg['pass_new_re'] = MSG07;
    }
  }

  // 全てのバリデーションチェックがOKだった場合
  if (empty($err_msg)) {
    debug('【ファイル名: passChange.php】全てのバリデーションチェックがOKですのでDBへ接続します');

    // 例外処理
    try {

      // DBへ接続
      $dbh = dbConnect();

      // SQL文作成
      $sql = 'UPDATE users SET password = :pass WHERE id = :u_id AND delete_flg = 0';
      $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':u_id' => $dbGetUser['id']);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {
        debug('【ファイル名: passChange.php】クエリに成功しましたのでメール送信作成準備します');

        // メッセージをセッションに格納
        $_SESSION['msg_suc'] = SUC01;

        // メール送信準備
        $name = (!empty($dbGetUser['name'])) ? $dbGetUser['name'] : $dbGetUser['email'];
        $from = 'info_memottatter@yn-it.com';
        $to = $dbGetUser['email'];
        $subject = '【めもったったー】パスワード変更のお知らせ';
        $comment = <<<EOT
{$name} 様

パスワードを変更いたしました。
下記のURLをクリックして
新しいパスワードでログインして下さいませ。
https://yn-it.com/memottatter/login.php


―――――――――――――――――――――――――――――――――――――――――――――――――――――
めもったったー
https://yn-it.com/memottatter/home.php
―――――――――――――――――――――――――――――――――――――――――――――――――――――


EOT;

        // メールを送信
        sendMail($from, $to, $subject, $comment);

        // ホーム画面へ遷移
        debug('【ファイル名: passChange.php】メール送信完了しましたのでホーム画面へ遷移します');
        header("Location:home.php");

        // メール送信に失敗した場合
      } else {
        debug('【ファイル名: passChange.php】メール送信に失敗しました');
        $err_msg['common'] = MSG08;
      }
    } catch (Exception $e) {
      error_log('【ファイル名: passChange.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
      $err_msg['common'] = MSG08;
    }
  }
}

?>

<?php $siteTitle = 'パスワード変更'; ?>
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
        <h2 class="c-form__title">パスワード変更</h2>
        <span class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg)) echo 'has-error'; ?>">
          <?php echo getErrMSg('common'); ?>
        </span>

        <div class="c-form__group">
          <label class="c-form__label">
            現在のパスワード
            <input type="password" name="pass" class="p-form__input c-input js-formPass <?php if (!empty($err_msg['pass'])) echo 'has-error'; ?>">
          </label>
          <span class="c-form__areaMsg js-passAreaMsg <?php if (!empty($err_msg['pass'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('pass') ?>
          </span>
        </div>

        <div class="c-form__group">
          <label class="c-form__label">
            新しいパスワード
            <input type="password" name="pass_new" class="p-form__input c-input js-formPassNew <?php if (!empty($err_msg['pass_new'])) echo 'has-error'; ?>">
          </label>
          <span class="c-form__areaMsg js-passNewAreaMsg <?php if (!empty($err_msg['pass_new'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('pass_new'); ?>
          </span>
        </div>

        <div class="c-form__group">
          <label class="c-form__label">
            新しいパスワード (確認)
            <input type="password" name="pass_new_re" class="p-form__input c-input js-formPassNewRe <?php if (!empty($err_msg['pass_new_re'])) echo 'has-error'; ?>">
          </label>
          <span class="c-form__areaMsg js-passNewReAreaMsg <?php if (!empty($err_msg['pass_new_re'])) echo 'has-error'; ?>">
            <?php echo getErrMsg('pass_new_re'); ?>
          </span>
        </div>

        <input type="submit" value="パスワードを変更する" class="c-button">
      </form>

    </div>

    <!-- link -->
    <a href="home.php" class="c-link">&lt; トップページへ</a>

  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>