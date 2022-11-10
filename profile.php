<?php
// 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「　プロフィールページ　「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('画面処理開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

// 現在のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['page'])) ? $_GET['page'] : 1;
debug('【ページ名: profile.php】現在のページは [ ' . $currentPageNum . ' ] ページ目です');

$user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
debug('【変数名: $user_id】閲覧しているユーザーIDは [ ' . $user_id . ' ] です');

// ユーザーのGETパラメータを取得
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
debug('【ページ名: profile.php】ユーザーIDは [ ' . $u_id . ' ] です');

// 表示件数を変数に格納
$listSpan = 5;

// 表示レコードの先頭を算出
$currentMinNum = ($currentPageNum - 1) * $listSpan;
debug('【ページ名: profile.php】ページの先頭は [ ' . $currentPageNum . ' ] です');

// DBから掲示板情報を取得
$dbBoardsData = getMyBoardsList($currentMinNum, $listSpan, $u_id);
debug('【ページ名: profile.php】DBから取得した掲示板情報は [ ' . print_r($dbBoardsData, true) . ' ] です');

// ※検証
$dbBoardsUserData = $dbBoardsData['data'];
debug('あ [ ' . print_r($dbBoardsUserData, true) . ' ] ');
$dbBoardsUserId = $dbBoardsUserData[0]['user_id'];
debug('い [ ' . $dbBoardsUserId . ' ] ');

// DBからプロフィール情報を取得
$dbProfData = dbGetProf($dbBoardsUserId);
debug('【ページ名: $dbProfData】DBから取得したプロフィール情報は [ ' . print_r($dbProfData, true) . ' ] です');

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 画面処理終了');

?>
<?php $siteTitle = 'プロフィールページ'; ?>
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

  <!-- page 2column -->
  <div class="c-page2column">
    <div class="c-page2column__container">


      <!-- main -->
      <main id="l-main">

        <div class="p-profile">

          <!-- profile area -->
          <div class="p-profile__container">
            <div class="c-thumbnails p-profile__thumbnailsWrap">
              <img src="<?php echo !empty($dbProfData['user_pic']) ? sanitize($dbProfData['user_pic']) : sanitize('img/sample-img.png');; ?>" class="c-thumbnails__img p-profile__thumbnails">
            </div>
            <div class="p-profile__info">
              <p class="p-profile__name"><?php echo (empty(sanitize($dbProfData['name']))) ? '名無し' : sanitize($dbProfData['name']); ?></p>
              <p class="p-profile__intro"><?php echo nl2br(sanitize($dbProfData['intro'])); ?></p>
            </div>
          </div>

          <!-- users memo area -->
          <div class="c-memo p-profile__memoArea">
            <?php
            foreach ($dbBoardsData['data'] as $key => $val) :
            ?>
              <div class="c-memo__container p-profile__memoContainer">
                <div class="c-thumbnails p-profile__memoThumbnails">
                  <a href="memoDetail.php?u_id=<?php echo sanitize($dbProfData['id']); ?>&b_id=<?php echo sanitize($val['board_id']); ?>" class="c-memo__link p-profile__memoLink">
                    <img src="<?php echo (!empty($val['user_pic'])) ? sanitize($val['user_pic']) : sanitize('img/sample-img.png'); ?>" alt="" class="c-thumbnails__img p-profile__memoThumbnailsImg">
                  </a>
                </div>
                <div class="c-memo__commentInfo p-profile__memoInfo">
                  <div class="c-memo__commentHead p-profile__memoHead">
                    <a href="memoDetail.php?u_id=<?php echo sanitize($dbProfData['id']); ?>&b_id=<?php echo sanitize($val['board_id']); ?>" class="c-memo__link c-memo__link--underline p-profile__memoLink--shortStr">
                      <span class="c-memo__nickname p-profile__memoName"><?php echo (empty(sanitize($val['name']))) ? '名無し' : sanitize($val['name']); ?></span>
                    </a>
                    <a href="memoDetail.php?u_id=<?php echo sanitize($dbProfData['id']); ?>&b_id=<?php echo sanitize($val['board_id']); ?>" class="c-memo__link c-memo__link--underline p-profile__memoLink">
                      <span class="c-memo__sendDate p-profile__memoDate"><?php echo sanitize(date('y年n月d日 G:i', strtotime($val['update_at']))); ?></span>
                    </a>
                  </div>
                  <div class="c-memo__commentBody p-profile__memoBody">
                    <p class="c-memo__comment p-profile__memoComment"><?php echo nl2br(sanitize($val['message'])); ?></p>
                  </div>
                  <?php if (!empty($val['board_pic'])) : ?>
                    <div class="c-memo__imgWrap p-profile__memoImgWrap">
                      <img src="<?php echo sanitize($val['board_pic']); ?>" alt="" class="c-memo__img p-profile__memoImg">
                    </div>
                  <?php endif; ?>

                  <div class="c-memo__iconWrap p-profile__iconWrap">

                    <!-- 自分のページだった場合のFont Awesome -->
                    <div class="c-memo__icon p-profile__icon">
                      <i class="fa-regular fa-heart c-memo__iconFont1 js-click-good1 <?php if (isGood(sanitize($val['board_id']), sanitize($val['user_id']), $_SESSION['user_id'])) echo 'fas'; ?>" aria-hidden="true" data-boardid="<?php echo sanitize($val['board_id']) ?>" data-userid="<?php echo sanitize($val['user_id']); ?>"></i>
                    </div>
                    <div class="c-memo__icon p-profile__icon <?php if ($user_id !== $dbBoardsUserId) echo 'c-memo__icon--erase'; ?>">
                      <a href="myMemo.php?u_id=<?php echo sanitize($val['user_id']); ?>&b_id=<?php echo sanitize($val['board_id']); ?>" class="c-memo__iconLink">
                        <i class="far fa-edit" aria-hidden="true"></i>
                      </a>
                    </div>
                    <div class="c-memo__icon p-profile__icon <?php if ($user_id !== $dbBoardsUserId) echo 'c-memo__icon--erase'; ?> js-showModal">
                      <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            <?php
            endforeach;
            ?>

            <!-- modal -->
            <div class="c-modal js-showModalTarget">
              <div class="c-modal__icon js-hideModal">
                <i class="fa fa-close c-modal__icon--close"></i>
              </div>
              <p class="c-modal__msg">メモを削除しますか</p>
              <div class="c-modal__btnWrap">
                <button class="c-modal__btn">はい</button>
                <button class="c-modal__btn js-hideModal">いいえ</button>
              </div>
            </div>

            <!-- modal cover -->
            <div class="c-modal__cover js-showModalCover"></div>

          </div>

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

    <!-- pagination -->
    <?php echo pagination($currentPageNum, $dbBoardsData['total_page'], '&u_id=' . $u_id); ?>

  </div>

  <!-- footer -->
  <?php require('footer.php'); ?>