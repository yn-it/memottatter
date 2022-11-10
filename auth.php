<?php
debug('「「「「「「「「「「「「「「「「「　ログイン認証　「「「「「「「「「「「「「「「「「「「「「「「');

// ログインしている場合
if (!empty($_SESSION['user_id'])) {
  debug('【ファイル名: auth.php】ログイン済みユーザーです');

  // ログイン有効期限内の場合
  if (($_SESSION['login_at'] + $_SESSION['login_limit']) > time()) {
    debug('【ファイル名: auth.php】ログイン有効期限内です');

    // 最新ログイン日時を現在日時に
    $_SESSION['login_at'] = time();
    debug('【ファイル名: auth.php】最新のログイン日時に更新しました');
    debug('【ファイル名: auth.php】最新のログイン日時は [ ' . $_SESSION['login_at'] . ' ] です');

    // ホーム画面へ遷移
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
      header("Location:home.php");
    }

    // ログイン期限切れの場合
  } else {
    debug('【ファイル名: auth.php】ログイン有効期限が切れました');

    // ログアウトする
    $_SESSION = array();
    session_destroy();
    debug('【ファイル名: auth.php】ログアウトしました');

    // ログイン画面へ遷移
    header("Location:login.php");
  }

  // ログインしていない場合  
} else {
  debug('【ファイル名: auth.php】未ログインユーザーです');

  // ログインページへ遷移
  if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header("Location:login.php");
  }
}
