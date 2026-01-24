jQuery(function($) {
  var scroll_top = 0;

  // メニューの開閉処理
  $("nav > button").on('click', function() {
    if (!$(this).hasClass('active')) {
      $(this).addClass('active');
      fix();
    } else {
      $(this).removeClass('active');
      nofix();
    }
  });

  function fix() {
    scroll_top = $(window).scrollTop();
    $('body').addClass('modeNav').css({'top': -scroll_top + 'px'});
  }

  function nofix() {
    $('body').removeClass('modeNav').css({'top': ''});
    window.scrollTo(0, scroll_top);

    // 遅延をつけて `position: fixed;` を復元
    setTimeout(() => {
        $('#siteHeader').css('position', 'fixed');
    }, 50);
  }
  
  var hoverTimeout;

  // PC専用のホバーイベント
  if ($(window).width() >= 768) {
    $('header nav ul li').hover(
      function() {
        clearTimeout(hoverTimeout);
        $(this).siblings('li').removeClass('active').children('ul').slideUp(200);
        $(this).addClass('active');
        $(this).children('ul').stop(true, true).slideDown(200);
      },
      function() {
        hoverTimeout = setTimeout(() => {
          $(this).removeClass('active');
          $(this).children('ul').stop(true, true).slideUp(200);
        }, 200);
      }
    );
  }

  // スマホ専用のクリックイベント
  if ($(window).width() < 768) {
    $('.btnSubMenu').on('click', function(e) {
      e.preventDefault();
      var parentLi = $(this).closest('li');

      if (parentLi.find('.subMenu').length > 0) {
        if (parentLi.hasClass('active')) {
          parentLi.removeClass('active').children('.subMenu').slideUp(200);
        } else {
          $('nav ul li').removeClass('active').children('.subMenu').slideUp(200);
          parentLi.addClass('active').children('.subMenu').slideDown(200);
        }
      }
    });
  }

  // ページ内アンカースクロール
  $('a[href^="#"]').on('click', function(event) {
    event.preventDefault();
    var target = $(this.hash);
    if (target.length) {
      $('html, body').animate({
        scrollTop: target.offset().top
      }, 800); // スクロール時間を800msに設定
    }
  });

  // スマホのクリックイベントを解除
  $(window).on('resize', function() {
    if ($(window).width() >= 768) {
      $('header nav ul li').off('click');
    }
  });
  
  // 現在地取得ボタンの処理
  document.querySelectorAll('.btnLocate').forEach(function(button) {
    button.addEventListener('click', function() {
      window.location.href = "search_location_results.php"; // 即時遷移
    });
  });

  // プレースホルダーを変更
  function updatePlaceholders() {
    const searchInputs = document.querySelectorAll('.keywordSearch');
    
    searchInputs.forEach(input => {
      if (window.innerWidth <= 768) { // スマートフォンの場合
        input.placeholder = 'キーワードから探す';
      } else { // PCの場合
        input.placeholder = 'キーワード検索（スペースで複数キーワード検索）';
      }
    });
  }

  // 初期表示時にプレースホルダーを設定
  updatePlaceholders();

  // ウィンドウリサイズ時にプレースホルダーを再設定
  window.addEventListener('resize', updatePlaceholders);
  
  // スマホで「エリアから探す」リンクがクリックされた場合にメニューを閉じる
  $('.btnArea').on('click', function() {
    // メニューを閉じる処理
    $('body').removeClass('modeNav').css({'top': ''});
    window.scrollTo(0, scroll_top);
    setTimeout(() => {
        $('#siteHeader').css('position', 'fixed');
    }, 50);
  });

  // スクロール時にヘッダーにぼかし効果を追加
  $(window).on('scroll', function() {
    var scrollTop = $(window).scrollTop();
    var header = $('#siteHeader');
    var logo = $('.header-logo');
    
    if (scrollTop > 100) {
      header.addClass('scrolled');
      logo.addClass('visible');
    } else {
      header.removeClass('scrolled');
      logo.removeClass('visible');
    }
  });

  // 初期表示時にもチェック
  var initialScrollTop = $(window).scrollTop();
  if (initialScrollTop > 100) {
    $('#siteHeader').addClass('scrolled');
    $('.header-logo').addClass('visible');
  }

});