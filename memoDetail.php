<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「　メモ詳細ページ　「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// メモ一覧のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['page'])) ? $_GET['page'] : '';
debug('【変数名: $currentPageNum】ページのGETパラメータは [ ' . $currentPageNum . ' ] です');

// 検索フォームのGETパラメータを取得
$search_box = (!empty($_GET['search-box'])) ? $_GET['search-box'] : '';
debug('【変数名: $search_box】検索フォームのGETパラメータは [ ' . $search_box . ' ] です');

// ユーザーIDのGETパラメータを取得
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
debug('【変数名: $u_id】ユーザーIDのGETパラメータは [ ' . $u_id . ' ] です');

// 掲示板のGETパラメータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
debug('【変数名: $b_id】掲示板のGETパラメータは [ ' . $b_id . ' ] です');

// このページを見ているユーザーIDを取得
$browsingUser = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
debug('【変数名: $browsingUser】このページを見ているユーザーIDは [ ' . $browsingUser . ' ] です');

// DBから掲示板の情報を取得
$dbBoardsData = getBoardsData($b_id, $u_id);
debug('【変数名: $dbBoardsData】DBから取得した掲示板の情報は [ ' . print_r($dbBoardsData, true) . ' ] です');

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>

<?php $siteTitle = 'メモ詳細'; ?>
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
      <a href="myMemo.php<?php echo !empty($_SESSION['user_id']) ? '?u_id=' . $_SESSION['user_id'] : ''; ?>" class="p-nav__link p-nav__link--sp">メモを投稿</a>
    </li>
    <li class="p-nav__item p-nav__item--sp">
      <a href="profEdit.php" class="p-nav__link p-nav__link--sp">プロフィール編集</a>
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

<div class="c-page2column">
  <div class="c-page2column__container">

    <!-- main -->
    <main id="l-main">

      <div class="c-memo p-memoDetail">
        <div class="c-memo__container p-memoDetail__container">
          <div class="c-thumbnails p-memoDetail__thumbnails">
            <a href="profile.php?u_id=<?php echo sanitize($dbBoardsData['user_id']); ?>" class="c-memo__link p-memoDetail__link">
              <img src="<?php echo (!empty($dbBoardsData['user_pic'])) ? sanitize($dbBoardsData['user_pic']) : sanitize('img/sample-img.png'); ?>" alt="" class="c-thumbnails__img p-memoDetail__img">
            </a>
          </div>
          <div class="c-memo__commentInfo p-memoDetail__commentInfo">
            <div class="c-memo__commentHead p-memoDetail__commentHead">
              <a href="profile.php?u_id=<?php echo sanitize($dbBoardsData['user_id']); ?>" class="c-memo__link c-memo__link--underline p-memoDetail__link--shortStr">
                <span class="c-memo__nickname p-memoDetail__name"><?php echo (empty(sanitize($dbBoardsData['name']))) ? '名無し' : sanitize($dbBoardsData['name']); ?></span>
              </a>
              <a href="profile.php?u_id=<?php echo sanitize($dbBoardsData['user_id']); ?>" class="c-memo__link c-memo__link--underline p-memoDetail__link">
                <span class="c-memo__sendDate p-memoDetail__sendDate"><?php echo sanitize(date('y年n月d日 G:i', strtotime($dbBoardsData['update_at']))); ?></span>
              </a>
            </div>
            <div class="c-memo__commentBody p-memoDetail__commentBody">
              <p class="c-memo__comment p-memoDetail__comment"><?php echo nl2br(sanitize($dbBoardsData['message'])); ?></p>
            </div>
            <?php
            if (!empty($dbBoardsData['board_pic'])) {
            ?>
              <div class="c-memo__imgWrap p-memoDetail__imgWrap">
                <img src="<?php echo sanitize($dbBoardsData['board_pic']); ?>" alt="" class="c-memo__img p-memoDetail__img">
              </div>
            <?php
            }
            ?>
            <div class="c-memo__iconWrap p-memoDetail__iconWrap">
              <div class="c-memo__icon p-memoDetail__icon">
                <i class="fa-regular fa-heart c-memo__iconFont1 js-click-good1 <?php if (isGood(sanitize($dbBoardsData['board_id']), sanitize($dbBoardsData['user_id']), $_SESSION['user_id'])) echo 'fas'; ?>" aria-hidden="true" data-boardid="<?php echo sanitize($dbBoardsData['board_id']) ?>" data-userid="<?php echo sanitize($dbBoardsData['user_id']); ?>"></i>
              </div>
              <div class="c-memo__icon p-memoDetail__icon <?php if ($browsingUser !== $dbBoardsData['user_id']) echo 'c-memo__icon--erase'; ?>">
                <a href="myMemo.php?u_id=<?php echo sanitize($dbBoardsData['user_id']); ?>&b_id=<?php echo sanitize($dbBoardsData['board_id']); ?>" class="c-memo__iconLink">
                  <i class="far fa-edit" aria-hidden="true"></i>
                </a>
              </div>
              <div class="c-memo__icon p-memoDetail__icon <?php if ($browsingUser !== sanitize($dbBoardsData['user_id'])) echo 'c-memo__icon--erase'; ?>">
                <i class="fa-solid fa-trash-can js-showModal c-memo__iconTrash" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- modal -->
        <div class="c-modal js-showModalTarget">
          <div class="c-modal__icon js-hideModal">
            <i class="fa fa-close c-modal__icon--close"></i>
          </div>
          <p class="c-modal__msg">メモを削除しますか</p>
          <div class="c-modal__btnWrap">
            <button class="c-modal__btn js-click-yes" data-boardid="<?php echo sanitize($dbBoardsData['board_id']); ?>">はい</button>
            <button class="c-modal__btn js-hideModal">いいえ</button>
          </div>
        </div>

        <!-- modal cover -->
        <div class="c-modal__cover js-showModalCover"></div>
      </div>

      <!-- link -->
      <a href="home.php?page=<?php echo $currentPageNum; ?><?php if (!empty($_GET['search-box'])) echo '&search-box=' . $search_box; ?>" class="c-link">&lt; トップページへ戻る</a>

    </main>

    <!-- sidebar -->
    <aside id="l-sidebar">
      <div class="c-sidebar">
        <ul class="c-sidebar__list">
          <li class="c-sidebar__item">
            <a href="myMemo.php<?php echo !empty($_SESSION['user_id']) ? '?u_id=' . $_SESSION['user_id'] : ''; ?>" class="c-sidebar__link">メモを投稿</a>
          </li>
          <li class="c-sidebar__item">
            <a href="profEdit.php" class="c-sidebar__link">プロフィール編集</a>
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

<?php require('footer.php'); ?>