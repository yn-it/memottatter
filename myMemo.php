<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「　メモ投稿ページ　「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// 掲示板のGETパラメータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
debug('【変数名: $b_id】掲示板のGETパラメータは [ ' . $b_id . ' ] です');

// ユーザーIDを変数に格納
$u_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';

// ユーザーIDのGETパラメータを取得
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
debug('【変数名: $u_id】ユーザーIDは [ ' . $u_id . ' ] です');

// DBから掲示板の情報を取得
$dbBoardsData = getBoardsData($b_id, $u_id);
debug('【変数名: $dbBoardsData】DBから取得した掲示板の情報は [ ' . print_r($dbBoardsData, true) . ' ] です');

// 新規投稿か更新か判定フラグ
$edit_flg = (empty($dbBoardsData)) ? true : false;

// GETパラメータに不正な値が入った場合
if (!empty($_SESSION['user_id'])) {
  if ($u_id !== $_SESSION['user_id']) {

    // トップページへ遷移
    debug('【ファイル名: myMemo.php】GETパラメータに不正な値が入りましたのでトップページへ遷移します');
    header('Location:home.php');
  }
}

// POST送信された場合
if (!empty($_POST)) {
  debug('【ファイル名: myMemo.php】POST送信があります');
  debug('【ファイル名: myMemo.php】POST情報は [ ' . print_r($_POST, true) . ' ] です');
  debug('【ファイル名: myMemo.php】FILE情報は [ ' . print_r($_FILES, true) . ' ] です');

  // 入力フォームの値を変数に格納
  $memo = $_POST['memo'];
  $board_pic = (!empty($_FILES['board_pic']['name'])) ? uploadImg($_FILES['board_pic'], 'board_pic') : '';
  $board_pic = (empty($board_pic) && !empty($dbBoardsData['board_pic'])) ? $dbBoardsData['board_pic'] : $board_pic;

  // バリデーションチェック
  // メモの未入力チェック
  validRequired($memo, 'memo');

  // メモの最大文字数チェック
  validMaxLen($memo, 'memo', 250);

  // バリデーションOKの場合
  if (empty($err_msg)) {
    debug('【ファイル名: myMemo.php】バリデーションチェックOKなので例外処理に入ります');

    // 例外処理
    try {

      // DBへ接続
      $dbh = dbConnect();

      // SQL文作成
      // 新規メモを作成する場合
      if ($edit_flg) {
        $sql = 'INSERT INTO board (user_id, message, board_pic, send_at, update_at) VALUES (:u_id, :msg, :board_pic, :date, :date)';
        $data = array(':u_id' => $_SESSION['user_id'], ':msg' => $memo, ':board_pic' => $board_pic, ':date' => date('Y-m-d'), ':date' => date('Y-m-d H:i:s'));

        // メモを更新する場合
      } else {
        $sql = 'UPDATE board SET message = :msg, board_pic = :board_pic, send_at = :date, update_at = :date WHERE id = :b_id AND user_id = :u_id AND delete_flg = 0';
        $data = array(':msg' => $memo, ':board_pic' => $board_pic, ':date' => date('Y-m-d H:i:s'), ':date' => date('Y-m-d H:i:s'), ':b_id' => $b_id, ':u_id' => $u_id);
      }

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {

        if ($edit_flg) {

          // メッセージをセッションに格納
          $_SESSION['msg_suc'] = SUC04;
        } else {
          $_SESSION['msg_suc'] = SUC05;
        }

        // トップページへ遷移
        header("Location:home.php");
      }
    } catch (Exception $e) {
      error_log('【ファイル名: myMemo.php】エラー発生 [ ' . $e->getMessage() . ' ]');
      $err_msg['common'] = MSG08;
    }
  }
}

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');


?>


<?php $siteTitle = 'メモ作成'; ?>
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
          <a href="signup.php" class="p-nav__link  p-nav__link--sp">ユーザー登録</a>
        <?php } else { ?>
          <a href="logout.php" class="p-nav__link">ログアウト</a>
        <?php } ?>
      </li>

      <!-- スマホ用リンク -->
      <li class="p-nav__item p-nav__item--sp">
        <a href="passChange.php" class="p-nav__link p-nav__link--sp">パスワード変更</a>
      </li>
      <li class="p-nav__item p-nav__item--sp">
        <a href="profEdit.php" class="p-nav__link p-nav__link--sp">プロフィール編集</a>
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

  <!-- page-2column -->
  <div class="c-page2column">

    <!-- page title -->
    <h2 class="c-page2column__title">
      <?php echo ($edit_flg) ? 'メモ投稿' : 'メモ編集'; ?>
    </h2>

    <div class="c-page2column__container">

      <!-- main -->
      <main id="l-main">

        <!-- form -->
        <div class="c-form p-formPage2column">
          <form action="" method="post" class="c-form__container p-formPage2column__container" enctype="multipart/form-data">

            <div class="p-formPage2column__formArea">

              <!-- error msg area -->
              <div class="c-form__areaMsg c-form__areaMsg--top <?php if (!empty($err_msg['common'])) echo 'has-error'; ?>">
                <?php echo getErrMsg('common'); ?>
              </div>

              <!-- memo area -->
              <div class="p-myMemo">
                <label class="c-form__label p-myMemo__label">
                  メモを入力してください
                  <?php
                  if (!empty($b_id)) {
                  ?>
                    <textarea name="memo" class="c-textarea p-myMemo__textarea js-count js-formMemo<?php if (!empty($err_msg['memo'])) echo 'has-error'; ?>"><?php echo sanitize($dbBoardsData['message']); ?></textarea>
                  <?php
                  } elseif (!empty($_POST['memo'])) {
                  ?>
                    <textarea name="memo" class="c-textarea p-myMemo__textarea js-count js-formMemo <?php if (!empty($err_msg['memo'])) echo 'has-error'; ?>"><?php echo $_POST['memo']; ?></textarea>
                  <?php
                  } else {
                  ?>
                    <textarea name="memo" class="c-textarea p-myMemo__textarea js-count js-formMemo <?php if (!empty($err_msg['memo'])) echo 'has-error'; ?>"></textarea>
                  <?php
                  }
                  ?>
                </label>
                <div class="c-counter p-myMemo__counter">
                  <span class="c-counterNum p-myMemo__counterNum js-countNum">1</span>/250
                </div>
                <span class="js-areaMsg c-form__areaMsg c-form__areaMsg--textarea js-memoAreaMsg <?php if (!empty($err_msg['memo'])) echo 'has-error'; ?>">
                  <?php echo getErrMsg('memo'); ?>
                </span>
              </div>

              <!-- memo image area -->
              <div class="p-myMemoImg">
                <label for="" class="c-form__label p-myMemoImg__label">
                  <?php
                  if (!empty($dbBoardsData['board_pic'])) {
                  ?>
                    <div class="p-myMemoImg__wrap js-dropMemoImgArea">
                      <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                      <input type="file" name="board_pic" class="c-inputFile p-myMemoImg__inputFile js-fileInput">
                      <div class="c-inputFile__info p-myMemo__info js-dropInfo" style="<?php if (!empty($dbBoardsData['board_pic'])) echo 'display: none;'; ?>">
                        ここにファイルをドロップ<br>または<br>ファイルを選択
                      </div>
                      <div class="c-inputFile__info c-inputFile__info--sp p-myMemo__info p-myMemo__info--sp js-dropInfo" style="<?php if (!empty($dbBoardsData['board_pic'])) echo 'display: none;'; ?>">
                        タップして<br>ファイルを選択
                      </div>
                      <img src="<?php echo sanitize($dbBoardsData['board_pic']); ?>" alt="" class="p-myMemoImg__pic js-prevImg">
                    </div>
                  <?php
                  } else {
                  ?>
                    <div class="p-myMemoImg__wrap js-dropMemoImgArea">
                      <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                      <input type="file" name="board_pic" class="c-inputFile p-myMemoImg__inputFile js-fileInput">
                      <div class="c-inputFile__info p-myMemo__info js-dropInfo">
                        ここにファイルをドロップ<br>または<br>ファイルを選択
                      </div>
                      <div class="c-inputFile__info c-inputFile__info--sp p-myMemo__info p-myMemo__info--sp js-dropInfo">
                        タップして<br>ファイルを選択
                      </div>
                      <img src="<?php if (!empty($dbBoardsData['board_pic'])) echo sanitize($dbBoardsData['board_pic']); ?>" alt="" class="p-myMemoImg__pic js-prevImg">
                    </div>
                  <?php
                  }
                  ?>
                </label>
              </div>
              <span class="c-form__areaMsg <?php if (!empty($err_msg['board_pic'])) echo 'has-error'; ?>">
                <?php echo getErrMsg('board_pic'); ?>
              </span>

              <input type="submit" value="投稿する" class="c-button">

            </div>

          </form>
        </div>
      </main>

      <!-- sidebar -->
      <aside id="l-sidebar">
        <div class="c-sidebar">
          <ul class="c-sidebar__list">
            <li class="c-sidebar__item">
              <a href="passChange.php" class="c-sidebar__link">パスワード変更</a>
            </li>
            <li class="c-sidebar__item">
              <a href="profEdit.php" class="c-sidebar__link">プロフィール編集</a>
            </li>
            <li class="c-sidebar__item">
              <a href="deactivate.php" class="c-sidebar__link">退会する</a>
            </li>
          </ul>
        </div>
      </aside>

    </div>

  </div>

  <!-- footer -->
  <?php require('footer.php'); ?>