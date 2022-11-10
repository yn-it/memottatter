$(function () {

  // =========================================
  //  グローバル変数
  // =========================================
  var nameLen = 15;
  var maxLen = 255;
  var minLen = 8;
  // =========================================


  // =========================================
  //  定数
  // =========================================
  const MSG01 = '入力必須項目です';
  const MSG02 = 'メールアドレスの形式で入力してください';
  const MSG03 = maxLen + '文字以下で入力してください';
  const MSG04 = minLen + '文字以上で入力してください';
  const MSG05 = '半角英数字のみご利用いただけます';
  const MSG06 = 'パスワード (確認) が正しくありません';
  const MSG07 = '現在のパスワードと同じです';
  const MSG08 = minLen + '文字で入力してください';
  const MSG09 = nameLen + '文字以内で入力してください';
  // =========================================


  // =========================================
  //  関数
  // =========================================
  function classAssign(form, areaMsg) {
    form.removeClass('has-success').addClass('has-error');
    areaMsg.removeClass('has-success').addClass('has-error');
  }
  // =========================================


  // =========================================
  //  バリデーションチェック
  // =========================================
  // Emailのバリデーションチェック
  $('.js-formEmail').on('blur', function () {
    var $validEmail = $(this),
      $emailAreaMsg = $('.js-emailAreaMsg') || null;

    // 未入力チェック
    if ($validEmail.val() === '') {
      classAssign($validEmail, $emailAreaMsg);
      $emailAreaMsg.text(MSG01);

      // Email形式チェック
    } else if (!$validEmail.val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)) {
      classAssign($validEmail, $emailAreaMsg);
      $emailAreaMsg.text(MSG02);

      // 最大文字数チェック
    } else if ($validEmail.val().length > maxLen) {
      classAssign($validEmail, $emailAreaMsg);
      $emailAreaMsg.text(MSG03);

      // バリデーションOKの場合
    } else {
      $validEmail.removeClass('has-error').addClass('has-success');
      $emailAreaMsg.removeClass('has-error').addClass('has-success');
      $emailAreaMsg.text('');
    }
  });

  // パスワードのバリデーションチェック
  $('.js-formPass').on('blur', function () {
    var $validPass = $(this),
      $passAreaMsg = $('.js-passAreaMsg') || null;

    // 未入力チェック
    if ($validPass.val() === '') {
      classAssign($validPass, $passAreaMsg);
      $passAreaMsg.text(MSG01);

      // 半角英数字チェック
    } else if (!$validPass.val().match(/^[a-zA-Z0-9]+$/)) {
      classAssign($validPass, $passAreaMsg);
      $passAreaMsg.text(MSG05);

      // 最小文字数チェック
    } else if ($validPass.val().length < minLen) {
      classAssign($validPass, $passAreaMsg);
      $passAreaMsg.text(MSG04);

      // 最大文字数チェック
    } else if ($validPass.val().length > maxLen) {
      classAssign($validPass, $passAreaMsg);
      $passAreaMsg.text(MSG03);

      // バリデーションOKの場合
    } else {
      $validPass.removeClass('has-error').addClass('has-success');
      $passAreaMsg.removeClass('has-error').addClass('has-success');
      $passAreaMsg.text('');
    }
  });

  // パスワード (確認) のバリデーションチェック
  $('.js-formPassRe').on('blur', function () {
    var $validPassRe = $(this),
      $validPass = $('.js-formPass') || null,
      $passReAreaMsg = $('.js-passReAreaMsg') || null;

    // 未入力チェック
    if ($validPassRe.val() === '') {
      classAssign($validPassRe, $passReAreaMsg);
      $passReAreaMsg.text(MSG01);

      // パスワード一致チェック
    } else if ($validPassRe.val() !== $validPass.val()) {
      classAssign($validPassRe, $passReAreaMsg);
      $passReAreaMsg.text(MSG06);

      // バリデーションOKの場合
    } else {
      $validPassRe.removeClass('has-error').addClass('has-success');
      $passReAreaMsg.removeClass('has-error').addClass('has-success');
      $passReAreaMsg.text('');
    }
  });

  // 新しいパスワードのバリデーションチェック
  $('.js-formPassNew').on('blur', function () {
    var $validPassNew = $(this),
      $validPass = $('.js-formPass') || null,
      $passNewAreaMsg = $('.js-passNewAreaMsg') || null;

    // 未入力チェック
    if ($validPassNew.val() === '') {
      classAssign($validPassNew, $passNewAreaMsg);
      $passNewAreaMsg.text(MSG01);

      // パスワード形式チェック
    } else if (!$validPassNew.val().match(/^[a-zA-Z0-9]+$/)) {
      classAssign($validPassNew, $passNewAreaMsg);
      $passNewAreaMsg.text(MSG05);

      // 最小文字数チェック
    } else if ($validPassNew.val().length < minLen) {
      classAssign($validPassNew, $passNewAreaMsg);
      $passNewAreaMsg.text(MSG04);

      // 最大文字数チェック
    } else if ($validPassNew.val().length > maxLen) {
      classAssign($validPassNew, $passNewAreaMsg);
      $passNewAreaMsg.text(MSG03);

      // 新しいパスワードが現在のパスワードと同じ場合
    } else if ($validPassNew.val().length !== 0 && $validPassNew.val() === $validPass.val()) {
      classAssign($validPassNew, $passNewAreaMsg);
      $passNewAreaMsg.text(MSG07);
    }

    // バリデーションOKの場合
    else {
      $validPassNew.removeClass('has-error').addClass('has-success');
      $passNewAreaMsg.removeClass('has-error').addClass('has-success');
      $passNewAreaMsg.text('');
    }
  });

  // 新しいパスワード (確認) のバリデーションチェック
  $('.js-formPassNewRe').on('blur', function () {
    var $vaildPassNewRe = $(this),
      $validPassNew = $('.js-formPassNew') || null,
      $passNewReAreaMsg = $('.js-passNewReAreaMsg') || null;

    // 未入力チェック
    if ($vaildPassNewRe.val() === '') {
      classAssign($vaildPassNewRe, $passNewReAreaMsg);;
      $passNewReAreaMsg.text(MSG01);

      // 新しいパスワードと新しいパスワード (確認) が違う場合
    } else if ($vaildPassNewRe.val() !== $validPassNew.val()) {
      classAssign($vaildPassNewRe, $passNewReAreaMsg);
      $passNewReAreaMsg.text(MSG06);

      // バリデーションOKの場合
    } else {
      $passNewReAreaMsg.removeClass('has-error').addClass('has-success');
      $vaildPassNewRe.removeClass('has-error').addClass('has-success');
      $passNewReAreaMsg.text('');
    }
  });

  // 認証キーのバリデーションチェック formTokenにしよう
  $('.js-validToken').on('blur', function () {
    var $validToken = $(this),
      $tokenAreaMsg = $('.js-tokenAreaMsg') || null;

    // 未入力チェック
    if ($validToken.val() === '') {
      classAssign($validToken, $tokenAreaMsg);
      $tokenAreaMsg.text(MSG01);

      // 半角英数字チェック
    } else if (!$validToken.val().match(/^[a-zA-Z0-9]+$/)) {
      classAssign($validToken, $tokenAreaMsg);
      $tokenAreaMsg.text(MSG05);

      // 固定長チェック
    } else if ($validToken.val().length !== minLen) {
      classAssign($validToken, $tokenAreaMsg);
      $tokenAreaMsg.text(MSG08);

      // バリデーションOKの場合
    } else {
      $validToken.removeClass('has-error').addClass('has-success');
      $tokenAreaMsg.removeClass('has-error').addClass('has-success');
      $tokenAreaMsg.text('');
    }
  });

  // ニックネームのバリデーションチェック
  $('.js-formName').on('blur', function () {
    var $validName = $(this),
      $nameAreaMsg = $('.js-nameAreaMsg') || null;

    // 未入力チェック
    if ($validName.val() === '') {
      classAssign($validName, $nameAreaMsg);
      $nameAreaMsg.text(MSG01);

      // 最大文字数チェック
    } else if ($validName.val().length > nameLen) {
      classAssign($validName, $nameAreaMsg);
      $nameAreaMsg.text(MSG09);

      // バリデーションOKの場合
    } else {
      $validName.removeClass('has-error').addClass('has-success');
      $nameAreaMsg.removeClass('has-error').addClass('has-success');
      $nameAreaMsg.text('');
    }
  });


  // 自己紹介文のバリデーションチェック
  $('.js-formIntro').on('blur', function () {
    var $validIntro = $(this),
      $introAreaMsg = $('.js-introAreaMsg') || null;

    // 未入力チェック
    if ($validIntro.val() === '') {
      classAssign($validIntro, $introAreaMsg);
      $introAreaMsg.text(MSG01);

      // 最大文字数チェック
    } else if ($validIntro.val().length > 120) {
      classAssign($validIntro, $introAreaMsg);
      $introAreaMsg.text('120文字以下で入力してください');

      // バリデーションOKの場合
    } else {
      $validIntro.removeClass('has-error').addClass('has-success');
      $introAreaMsg.removeClass('has-error').addClass('has-success');
      $introAreaMsg.text('');
    }
  });

  // メモ投稿のバリデーションチェック
  $('.js-formMemo').on('blur', function () {
    var $validMemo = $(this),
      $memoAreaMsg = $('.js-memoAreaMsg') || null;

    // 未入力チェック
    if ($validMemo.val() === '') {
      classAssign($validMemo, $memoAreaMsg);
      $memoAreaMsg.text(MSG01);

      // 最大文字数チェック
    } else if ($validMemo.val().length > 250) {
      classAssign($validMemo, $memoAreaMsg);
      $memoAreaMsg.text('250文字以下で入力してください');

      // バリデーションOKの場合
    } else {
      $validMemo.removeClass('has-error').addClass('has-success');
      $memoAreaMsg.removeClass('has-error').addClass('has-success');
      $memoAreaMsg.text('');
    }
  });


  // スマホ用メニューアニメーション
  $('.jsSpMenu').on('click', function () {

    // スマホ用メニュー表示時に背景を固定
    var $jsPositionFixed = $('.jsPositionFixed') || null;
    var state = false;
    var pos;
    if(state == false) {
      pos = $(window).scrollTop();
      $jsPositionFixed.addClass('is-fixed').css({'top': -pos});
      state = true;
    }else {
      $jsPositionFixed.removeClass('is-fixed').css({'top': 0});
      window.scrollTo(0, pos);
      state = false;
    }

    var $jsSpMenuTarget = $('.jsSpMenuTarget') || null;
    $(this).toggleClass('is-active');
    $jsSpMenuTarget.toggleClass('is-active');
  });

  // メッセージスライド
  var $jsShowMsg = $('.js-showMsg'),
    msg = $jsShowMsg.text();

  // 空白を削除
  if (msg.replace(/^[\s　]+|[\s　]+$/g, "")) {
    $jsShowMsg.toggleClass('is-slide');

    // スライドトグル
    $jsShowMsg.slideToggle(2000);
    setTimeout(function () {
      $jsShowMsg.slideToggle(2000);
    }, 5000);
  }

  // 文字数カウント
  var $count = $('.js-count'),
    countNum = $('.js-countNum');

  $count.on('keyup', function () {
    var str = $(this).val();
    str = str.replace(/\n/g, '');
    countNum.text(str.length);
  });


  // 画像ドラッグ&ドロップ
  var $dropArea = $('.js-dropArea'),
    $dropArea2 = $('.js-dropMemoImgArea'),
    $dropInfo = $('.js-dropInfo'),
    $fileInput = $('.js-fileInput');

  // スタイル変更用css
  var fileInputStyle = {
    width: "100%",
    height: "100%",
    opacity: "0",
    position: "absolute",
    top: "0",
    left: "0",
    zIndex: "2",
    display: "inherit",
    cursor: "pointer",
  }

  // 画像がドラッグされた時
  $dropArea.on('dragover', function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px dashed #66cc99');
    $fileInput.css(fileInputStyle);
  });

  $dropArea2.on('dragover', function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px dashed #66cc99');
    $fileInput.css(fileInputStyle);
  });

  // 画像がドロップされた時
  $dropArea.on('dragleave', function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px solid #66cc99');
  });

  // 画像がドロップされた時 (掲示板の画像)
  $dropArea2.on('dragleave', function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });

  // 画像が表示される時
  $fileInput.on('change', function () {
    $dropArea2.css('border', 'none');
    $dropArea.css('border', '3px solid #66cc99');
    $dropInfo.hide();

    // img属性の情報を取得
    var $img = $('.js-prevImg'),

    // file配列を変数に格納
      file = this.files[0],

      // FileReaderオブジェクトを変数に格納
      fileReader = new FileReader();

    // 読み込みが完了した時のイベントハンドラ
    fileReader.onload = function (e) {

      // imgデータをセット
      $img.attr('src', e.target.result).show();
    };

    // 画像を読み込み
    fileReader.readAsDataURL(file);
  });

  // モーダル
  var $jsShowModal = $('.js-showModal'),
    $jsShowModalTarget = $('.js-showModalTarget'),
    $jsShowModalCover = $('.js-showModalCover'),
    $jsHideModal = $('.js-hideModal');

  $jsShowModal.on('click', function () {
    $jsShowModalTarget.fadeIn();
    $jsShowModalCover.fadeIn();
  });

  $jsHideModal.on('click', function () {
    $jsShowModalTarget.fadeOut();
    $jsShowModalCover.fadeOut();
  });

  // ハートアニメーション
  var $clickGood1 = $('.js-click-good1');
  $clickGood1.on('click', function () {
    var $that = $(this);
    $that.toggleClass('far fa-heart');
    $that.toggleClass('fas fa-heart');
    $that.toggleClass('is-active');
    $that.addClass('far');
  });

  // リンクの無効化
  $clickGood1.on('click', function () {
    return false;
  });

  // Ajax
  var $good,
    goodBoardId,
    goodBoardUserId;

  $good = $('.js-click-good1') || null;
  goodBoardId = $good.data('boardid') || null;
  goodBoardUserId = $good.data('userid') || null;

  if (goodBoardId !== undefined && goodBoardId !== null && goodBoardUserId !== undefined && goodBoardUserId !== null) {

    $good.on('click', function () {
      var $that = $(this);
      goodBoardId = $that.attr('data-boardid');
      goodBoardUserId = $that.attr('data-userid');
      var data = { boardid: goodBoardId, userid: goodBoardUserId };

      $.ajax({
        type: 'POST',
        url: 'ajaxGood.php',
        data: data,
      }).done(function () {
        console.log('Ajax Success');
      }).fail(function () {
        console.log('Ajax Error');
      });
    });
  }

  var $yes = $('.js-click-yes') || null,
    deleteBoardId = $yes.data('boardid') || null;

  if (deleteBoardId !== undefined && deleteBoardId !== null) {
    $yes.on('click', function () {
      var $that = $(this);
      deleteBoardId = $that.attr('data-boardid');

      // ブラウザバックを禁止する
      history.pushState(null, null, 'ajaxDelete.php');
      $(window).on('popstate', function(){
        history.go(1);
      });

      $.ajax({
        type: 'POST',
        url: 'ajaxDelete.php',
        data: { boardid: deleteBoardId },
      }).then(function () {
        console.log('Ajax Success');
        window.location.href = 'https://yn-it.com/memottatter/home.php';
      }).fail(function () {
        console.log('Ajax Error');
      });
    });
  }

});