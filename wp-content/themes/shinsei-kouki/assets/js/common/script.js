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

  // ページ内アンカースクロール（同一ページの #id のみ。他ページへのリンクは触らない）
  $(document).on('click', 'a[href^="#"]', function(event) {
    var href = $(this).attr('href');
    if (!href || href === '#' || href.length <= 1) {
      return;
    }
    var target = $(this.hash);
    if (!target.length || !document.getElementById(this.hash.slice(1))) {
      return;
    }
    event.preventDefault();
    var headerHeight = $('#siteHeader').outerHeight() || 0;
    var offset = headerHeight + 24;
    $('html, body').animate({
      scrollTop: Math.max(0, target.offset().top - offset)
    }, 800);
  });

  // 他ページへの遷移で「一度上へスクロールしてから切り替わる」を防ぐ（通常クリック時のみ即時遷移）
  $(document).on('click', 'a[href]:not([href^="#"]):not([target="_blank"])', function(event) {
    if (event.ctrlKey || event.metaKey || event.which === 2) {
      return;
    }
    var el = this;
    var href = el.getAttribute('href');
    if (!href || href.indexOf('javascript:') === 0) {
      return;
    }
    var currentPath = window.location.pathname || '';
    var linkPath = (function() {
      try {
        var a = document.createElement('a');
        a.href = href;
        return a.pathname || '';
      } catch (e) {
        return '';
      }
    })();
    if (!linkPath || linkPath === currentPath) {
      return;
    }
    if (el.hostname && el.hostname !== window.location.hostname) {
      return;
    }
    event.preventDefault();
    window.location.href = el.href;
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
  
  // スマホで「エリアから探す」リンクがクリックされた場合にメニューを閉じる（ヘッダー内の .btnArea のみ）
  $(document).on('click', '#siteHeader .btnArea', function() {
    $('body').removeClass('modeNav').css({'top': ''});
    window.scrollTo(0, scroll_top);
    setTimeout(function() {
      $('#siteHeader').css('position', 'fixed');
    }, 50);
  });

  // スクロール時にヘッダーにぼかし効果を追加（デフォルトは英文のみ、スクロールでCI＋日本語会社名がフェードイン）
  $(window).on('scroll', function() {
    var scrollTop = $(window).scrollTop();
    var header = $('#siteHeader');
    var logo = $('.headerLogo');
    
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
    $('.headerLogo').addClass('visible');
  }

  // 共通アコーディオン機能
  // 使用方法: トリガー要素にdata-accordion-target属性で対象要素のセレクタを指定
  // 例: <button data-accordion-target=".targetElement">クリック</button>
  // オプション: data-accordion-sp-only="true" でSPのみ有効化
  function initAccordion() {
    $('[data-accordion-target]').each(function() {
      var $trigger = $(this);
      var targetSelector = $trigger.data('accordion-target');
      var spOnly = $trigger.data('accordion-sp-only') === true;
      var $target = $trigger.siblings(targetSelector).length ? $trigger.siblings(targetSelector) : $trigger.closest('*').find(targetSelector);
      
      // SPのみの場合はPCでは無効化
      if (spOnly && $(window).width() >= 768) {
        return;
      }
      
      $trigger.on('click', function(e) {
        e.preventDefault();
        var isOpen = $trigger.hasClass('isOpen');
        
        if (isOpen) {
          $trigger.removeClass('isOpen').attr('aria-expanded', 'false');
          $target.removeClass('isOpen').slideUp(300);
        } else {
          // 同じ親要素内の他のアコーディオンを閉じる（オプション）
          var $parent = $trigger.closest('[data-accordion-group]');
          if ($parent.length) {
            var groupSelector = $parent.data('accordion-group');
            $parent.find('[data-accordion-target]').not($trigger).removeClass('isOpen').attr('aria-expanded', 'false');
            $parent.find('[data-accordion-target]').not($trigger).each(function() {
              var otherTarget = $(this).data('accordion-target');
              $(this).siblings(otherTarget).add($(this).closest('*').find(otherTarget)).removeClass('isOpen').slideUp(300);
            });
          }
          
          $trigger.addClass('isOpen').attr('aria-expanded', 'true');
          $target.addClass('isOpen').slideDown(300);
        }
      });
    });
  }
  
  // アコーディオン初期化
  initAccordion();
  
  // リサイズ時に再初期化（SPのみのアコーディオン対応）
  $(window).on('resize', function() {
    $('[data-accordion-target]').off('click');
    initAccordion();
  });
  
  // 月別アーカイブのアコーディオン（後方互換性のため残す）
  // 上記の共通機能を使う場合は削除可能
  $('.yearTitle').on('click', function() {
    var $this = $(this);
    var $yearGroup = $this.closest('.yearGroup');
    var $monthList = $yearGroup.find('.monthList');
    var isOpen = $this.hasClass('isOpen');
    
    if (isOpen) {
      $this.removeClass('isOpen').attr('aria-expanded', 'false');
      $monthList.removeClass('isOpen');
    } else {
      $this.addClass('isOpen').attr('aria-expanded', 'true');
      $monthList.addClass('isOpen');
    }
  });


});