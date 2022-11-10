<?php

// 関数ファイル読み込み
require('function.php');

// ログイン認証
require('auth.php');

// ログアウトする
debug('【ファイル名: logout.php】ログアウトします');
$_SESSION = array();
session_destroy();

// ログアウト画面へ遷移
debug('【ファイル名: logout.php】ログアウト画面へ遷移します');
if (basename($_SERVER['PHP_SELF']) !== 'logout.php') {
  header("Location:logout.php");
}

debug('「「「「「「「「「「「「「「「「　ログアウトページ　「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ファイル名: logout.php】POST送信があります');
  debug('【ファイル名: logout.php】POSt情報は [ ' . print_r($_POST, true) . ' ] です');
  debug('【ファイル名: logout.php】ホーム画面へ遷移します');
  header("Location:home.php");
}

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>
<?php $siteTitle = 'ログアウト'; ?>
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
        <h2 class="c-form__title c-form__title--small">ログアウトしました</h2>
        <input type="submit" value="トップページへ" class="c-button" name="fileDest">
      </form>
    </div>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>