<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「　トップページ　「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// 現在のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['page'])) ? $_GET['page'] : 1;
debug('【ファイル名: home.php】現在のページは [ ' . $currentPageNum . ' ] ページ目です');

// 検索フォームを入力する変数を用意
$search_box = '';

// 検索フォームのGETパラメータを取得
$search_box = (isset($_GET['search-box'])) ? $_GET['search-box'] : '';
debug('【ファイル名: home.php】検索フォームの値は [ ' . $search_box . ' ] です');

// 表示件数を変数に格納
$listSpan = 5;

// 表示レコードの先頭を算出
$currentMinNum = ($currentPageNum - 1) * $listSpan;
debug('【ファイル名: home.php】ページの先頭は [ ' . $currentMinNum . ' ] です');

// DBから掲示板情報を取得
$dbBoardsData = getBoardsList($currentMinNum, $listSpan, $search_box);
debug('【変数名: $dbBoardsData】DBから取得した掲示板情報は [ ' . print_r($dbBoardsData, true) . ' ] です');
foreach ($dbBoardsData['data'] as $key => $val) {
  debug('テスト: ' . $val['user_id']);
}
// DBからお気に入りの情報を取得

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>

<?php $siteTitle = "トップページ"; ?>
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
        <a href="profEdit.php" class="p-nav__link  p-nav__link--sp">プロフィール編集</a>
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

  <!-- msg slide -->
  <p class="p-header__msgSlide js-showMsg">
    <?php echo getSessionFlash('msg_suc'); ?>
  </p>

  <!-- page 2column -->
  <div class="c-page2column">

    <div class="c-page2column__container">

      <!-- main -->
      <main id="l-main">
        <img src="screenshot.png" alt="screenshot" style="display:none;">
        <!-- memo -->
        <div class="c-memo p-homeMemo">
          <?php
          foreach ($dbBoardsData['data'] as $key => $val) {
          ?>
            <a href="memoDetail.php?page=<?php echo $currentPageNum; ?><?php if (!empty($search_box)) echo '&search-box=' . $search_box; ?>&u_id=<?php echo sanitize($val['user_id']); ?>&b_id=<?php echo sanitize($val['board_id']); ?>" class="c-memo__link p-homeMemo__link">
              <div class="c-memo__container p-homeMemo__container">
                <div class="c-thumbnails p-homeMemo__thumbnails">
                  <img src="<?php echo (!empty($val['user_pic'])) ? sanitize($val['user_pic']) : 'img/sample-img.png'; ?>" alt="" class="c-thumbnails__img p-homeMemo__thumbnailsImg">
                </div>
                <div class="c-memo__commentInfo p-homeMemo__commentInfo">
                  <div class="c-memo__commentHead p-homeMemo__commentHead">
                    <span class="c-memo__nickname p-homeMemo__nickname"><?php echo (empty(sanitize($val['name']))) ? '名無し' : sanitize($val['name']); ?></span>
                    <span class="c-memo__sendDate p-homeMemo__sendDate">
                      <?php echo date('y年n月d日 G:i', strtotime(sanitize($val['update_at']))); ?>
                    </span>
                  </div>
                  <div class="c-memo__commentBody p-homeMemo__commentBody">
                    <p class="c-memo__comment p-homeMemo__comment"><?php echo nl2br(sanitize($val['message'])); ?></p>
                    <?php if (!empty($val['board_pic'])) : ?>
                      <div class="c-memo__imgWrap p-homeMemo__imgWrap">
                        <img src="<?php echo sanitize($val['board_pic']); ?>" alt="" class="c-memo__img p-homeMemo__img">
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="c-memo__iconWrap p-homeMemo__iconWrap">
                    <div class="c-memo__icon p-homeMemo__icon">
                      <i class="fa-regular fa-heart c-memo__iconFont1 p-homeMemo__iconFont js-click-good1 <?php if (isGood(sanitize($val['board_id']), sanitize($val['user_id']), $_SESSION['user_id'])) echo 'fas'; ?>" aria-hidden="true" data-boardid="<?php echo sanitize($val['board_id']) ?>" data-userid="<?php echo sanitize($val['user_id']); ?>"></i>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          <?php
          }
          ?>

      </main>

      <!-- sidebar -->
      <aside id="l-sidebar">
        <div class="c-sidebar p-homeSidebar">

          <!-- search -->
          <div class="c-search">
            <form action="" method="get" class="c-search__container">
              <input type="text" name="search-box" class="c-search__input" value="<?php if (!empty($_GET['search-box'])) echo $_GET['search-box']; ?>" placeholder="キーワード検索">
              <input type="submit" value="&#xf002;" class="c-search__fontAwesome">
            </form>
          </div>

          <!-- sidebar list -->
          <ul class="c-sidebar__list p-homeSidebar__list">
            <li class="c-sidebar__item p-homeSidebar__item">
              <a href="myMemo.php<?php echo !empty($_SESSION['user_id']) ? '?u_id=' . $_SESSION['user_id'] : ''; ?>" class="c-sidebar__link">メモを投稿</a>
            </li>
            <li class="c-sidebar__item p-homeSidebar__item">
              <a href="profEdit.php" class="c-sidebar__link">プロフィール編集</a>
            </li>
            <li class="c-sidebar__item p-homeSidebar__item">
              <a href="passChange.php" class="c-sidebar__link">パスワード変更</a>
            </li>
            <li class="c-sidebar__item p-homeSidebar__item">
              <a href="deactivate.php" class="c-sidebar__link">退会する</a>
            </li>
          </ul>
        </div>
      </aside>

    </div>

    <!-- pagination -->
    <?php echo pagination($currentPageNum, $dbBoardsData['total_page'], '&search-box=' . $search_box); ?>

  </div>

  <!-- footer -->
  <?php require('footer.php'); ?>