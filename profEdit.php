<?php
// 関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「　プロフィール編集ページ　「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// DBからユーザー情報を取得
$dbProfData = dbGetProf($_SESSION['user_id']);
debug('【変数名: $dbProfData】DBから取得したユーザー情報は [ ' . print_r($dbProfData, true) . ' ] です');

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// POST送信がある場合
if (!empty($_POST)) {
  debug('【ファイル名: profEdit.php】POST送信があります');
  debug('【ファイル名: profEdit.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');
  debug('【ファイル名: profEdit.php】FILE情報は [ ' . print_r($_FILES, true) . ' ] です');

  // 画像をアップロードし、パスを格納
  $user_pic = (!empty($_FILES['user_pic']['name'])) ? uploadImg($_FILES['user_pic'], 'user_pic') : '';

  // DBに画像がある場合、DBのパスを入れる
  $user_pic = (!empty($dbProfData['user_pic'] && (empty($user_pic)))) ? $dbProfData['user_pic'] : $user_pic;

  // 入力フォームの値を変数に格納
  $name = $_POST['name'];
  $intro = $_POST['intro'];

  // DBの情報とPOSTされた情報が違う場合 (バリデーションチェック)
  // 名前チェック
  if ($dbProfData['name'] !== $name) {

    // 未入力チェック
    validRequired($name, 'name');

    // 最大文字数チェック
    validMaxLen($name, 'name', 15);
  }

  // 自己紹介文
  if ($dbProfData['intro'] !== $intro) {

    // 未入力チェック
    validRequired($intro, 'intro');

    // 最大文字数チェック
    validMaxLen($intro, 'intro', 120);
  }

  // バリデーションチェックOKの場合
  if (empty($err_msg)) {
    debug('【ファイル名: profEdit.php】バリデーションチェックOKですので例外処理に入ります');

    try {

      // DBへ接続
      $dbh = dbConnect();

      // SQL文作成
      $sql = 'UPDATE users SET name = :name, user_pic = :user_pic, intro = :intro WHERE id = :u_id AND delete_flg = 0';
      $data = array(':name' => $name, ':user_pic' => $user_pic, ':intro' => $intro, ':u_id' => $dbProfData['id']);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {

        // メッセージをセッションに格納
        $_SESSION['msg_suc'] = SUC03;

        // トップページへ遷移
        debug('【ファイル名: profEdit.php】クエリに成功しましたのでトップページへ遷移します');
        header("Location:home.php");
      } else {
        return false;
      }
    } catch (Exception $e) {
      error_log('【ファイル名: profEdit.php】エラー発生 [ ' . $e->getMessage() . ' ] ');
      $err_msg['common'] = MSG08;
    }
  }
}

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>

<?php $siteTitle = 'プロフィール編集'; ?>
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

      <!-- スマホ用リンク -->
      <li class="p-nav__item p-nav__item--sp">
        <a href="myMemo.php<?php echo !empty($_SESSION['user_id']) ? '?u_id=' . $_SESSION['user_id'] : ''; ?>" class="p-nav__link p-nav__link--sp">メモを投稿</a>
      </li>
      <li class="p-nav__item p-nav__item--sp">
        <a href="passChange.php" class="p-nav__link p-nav__link--sp">パスワード変更</a>
      </li>
      <li class="p-nav__item p-nav__item--sp">
        <a href="deactivate.php" class="p-nav__link p-nav__link--sp">退会する</a>
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

  <!-- page 2column -->
  <div class="c-page2column">

    <!-- page title -->
    <h2 class="c-page2column__title">プロフィール編集</h2>

    <div class="c-page2column__container">

      <!-- main -->
      <main id="l-main">

        <!-- form -->
        <div class="c-form p-formPage2column">
          <form action="" method="post" class='c-form__container p-formPage2column__container' enctype="multipart/form-data">

            <div class="p-formPage2column__formArea">

              <!-- error msg area -->
              <div class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg['user_pic'])) echo 'has-error'; ?>">
                <?php echo getErrMsg('user_pic'); ?>
              </div>

              <!-- profile edit image area -->
              <div class="p-profEditThumbnails">
                <label for="" class="c-form__label p-profEditThumbnails__label">
                  <div class="c-thumbnails p-profEditThumbnails__wrap js-dropArea">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="user_pic" class="c-inputFile p-profEditThumbnails__inputFile js-fileInput">
                    <div class="c-inputFile__info p-profEditThumbnails__info js-dropInfo" style="<?php if (!empty($dbProfData['user_pic'])) echo 'display: none;'; ?>">
                      ここにファイルをドロップ<br>または<br>ファイルを選択
                    </div>
                    <div class="c-inputFile__info c-inputFile__info--sp p-profEditThumbnails__info p-profEditThumbnails__info--sp js-dropInfo" style="<?php if (!empty($dbProfData['user_pic'])) echo 'display: none;'; ?>">
                      タップして<br>ファイルを選択
                    </div>
                    <div class="p-profEditThumbnails__imgWrap">
                      <img src="<?php echo getFormData('user_pic'); ?>" alt="" class="c-thumbnails__img p-profEditThumbnails__img js-prevImg" style="<?php if (!empty($err_msg['user_pic']) || empty($dbProfData['user_pic'])) echo 'display:none;' ?>">
                    </div>
                  </div>
                </label>
              </div>

              <!-- nickname -->
              <div class="p-profEditName">
                <label class="c-form__label p-profEditName__label">
                  ニックネーム
                  <input type="text" name="name" class="c-input p-profEditName__input js-formName <?php if (!empty($err_msg['name'])) echo 'has-error'; ?>" value="<?php echo getFormData('name'); ?>" placeholder="15文字以内">
                </label>
                <span class="c-form__areaMsg js-nameAreaMsg <?php if (!empty($err_msg['name'])) echo 'has-error'; ?>">
                  <?php echo getErrMsg('name'); ?>
                </span>
              </div>

              <!-- introduction -->
              <div class="p-profEditIntro">
                <label class="c-form__label p-profEditIntro__label">
                  自己紹介文
                  <textarea name="intro" class="p-profEditIntro__textarea c-textarea js-count js-formIntro <?php if (!empty($err_msg['intro'])) echo 'has-error'; ?>"><?php echo getFormData('intro'); ?></textarea>
                </label>
                <div class="c-counter">
                  <span class="c-counterNum js-countNum">0</span>/120
                </div>
                <span class="js-areaMsg c-form__areaMsg c-form__areaMsg--textarea js-introAreaMsg <?php if (!empty($err_msg['intro'])) echo 'has-error'; ?>">
                  <?php echo getErrMsg('intro'); ?>
                </span>
              </div>

              <input type="submit" value="保存する" class="c-button">
            </div>
          </form>
        </div>

      </main>

      <!-- sidebar -->
      <aside id="l-sidebar">
        <div class="c-sidebar">
          <ul class="c-sidebar__list">
            <li class="c-sidebar__item">
              <a href="myMemo.php<?php echo !empty($_SESSION['user_id']) ? '?u_id=' . $_SESSION['user_id'] : ''; ?>" class="c-sidebar__link">メモを投稿</a>
            </li>
            <li class="c-sidebar__item">
              <a href="passChange.php" class="c-sidebar__link">パスワード変更</a>
            </li>
            <li class="p-sidebar__item">
              <a href="deactivate.php" class="c-sidebar__link">退会する</a>
            </li>
          </ul>
        </div>
      </aside>

    </div>

  </div>

  <!-- footer -->
  <?php require('footer.php'); ?>