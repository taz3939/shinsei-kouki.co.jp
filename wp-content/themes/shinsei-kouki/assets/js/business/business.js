/**
 * 事業内容ページ
 * PC: タブ切り替え（上部タブ・下部1エリア）
 * SP: アコーディオン（各タブの直下にそのコンテンツ）
 */
jQuery(function($) {
  var $container = $('.businessTabContainer');
  var $tabsList = $('.businessTabs');
  var $tabs = $('.businessTabs .businessTab');
  var $contentsWrapper = $('.businessTabContents');
  var $panels = $('.businessTabContents .businessTabContent');

  if (!$tabs.length || !$panels.length) {
    return;
  }

  function isAccordion() {
    return window.matchMedia('(max-width: 768px)').matches;
  }

  /** SP: 各パネルを対応するタブの li の直下に移動（タブ→コンテンツの並びにする） */
  function layoutForAccordion() {
    $tabs.each(function(i) {
      var tabId = $(this).data('tab');
      var $panel = $('#'.concat(tabId, '-content'));
      if ($panel.length) {
        $panel.appendTo($tabsList.children('li').eq(i));
      }
    });
  }

  /** PC: 全パネルを .businessTabContents に戻す（元の順） */
  function layoutForTabs() {
    var ids = ['sale', 'repair', 'inspection', 'substitute'];
    ids.forEach(function(id) {
      var $panel = $('#'.concat(id, '-content'));
      if ($panel.length) {
        $panel.appendTo($contentsWrapper);
      }
    });
  }

  function activateTab($tab) {
    var tabId = $tab.data('tab');
    var $panel = $('#'.concat(tabId, '-content'));

    $tabs.removeClass('isActive').attr('aria-selected', 'false');
    $panels.removeClass('isActive');

    $tab.addClass('isActive').attr('aria-selected', 'true');
    if ($panel.length) {
      $panel.addClass('isActive');
    }
  }

  function closeAll() {
    $tabs.removeClass('isActive').attr('aria-selected', 'false');
    $panels.removeClass('isActive');
  }

  var currentIsAccordion = null; /* 前回のモード（null=未設定, true=SP, false=PC） */

  function applyLayout() {
    var nowAccordion = isAccordion();

    if (currentIsAccordion === null || currentIsAccordion !== nowAccordion) {
      /* 初回またはブレークポイントをまたいだときだけ開閉状態をリセット */
      if (nowAccordion) {
        layoutForAccordion();
        closeAll(); /* SP: デフォルトで全て閉じる */
      } else {
        layoutForTabs();
        activateTab($tabs.first()); /* PC: 1つ目のタブを表示 */
      }
      currentIsAccordion = nowAccordion;
    } else {
      /* 同じモードのままの resize（スクロールでアドレスバーが出ると発火する等）では閉じない */
      if (nowAccordion) {
        layoutForAccordion();
      } else {
        layoutForTabs();
        var $active = $tabs.filter('.isActive');
        if ($active.length) {
          activateTab($active);
        } else {
          activateTab($tabs.first());
        }
      }
    }
  }

  applyLayout();
  $(window).on('resize', function() {
    applyLayout();
  });

  $tabs.on('click', function() {
    var $tab = $(this);

    if (isAccordion()) {
      if ($tab.hasClass('isActive')) {
        closeAll();
      } else {
        activateTab($tab);
        /* スライド終了後に businessTabContainer へスクロールし、2〜4つ目なら微調整 */
        var headerHeight = 90; /* #siteHeader の高さに合わせる */
        var index = $tabs.index($tab[0]);
        setTimeout(function() {
          if (!$container.length) return;
          var containerTop = $container.offset().top;
          var rowHeight = 0;
          if (index > 0) {
            var $firstLi = $tabsList.children('li').eq(0);
            rowHeight = ($firstLi.length ? $firstLi.outerHeight() : 60) * index;
          }
          var targetTop = containerTop - headerHeight + rowHeight;
          window.scrollTo({ top: Math.max(0, targetTop), behavior: 'smooth' });
        }, 400); /* 0.35s のスライド transition 完了後に実行 */
      }
    } else {
      activateTab($tab);
    }
  });

  $tabs.on('keydown', function(e) {
    if (isAccordion()) return;
    var index = $tabs.index(this);
    var key = e.which;

    if (key === 37 || key === 38) {
      e.preventDefault();
      index = index <= 0 ? $tabs.length - 1 : index - 1;
      $tabs.eq(index).focus().trigger('click');
    } else if (key === 39 || key === 40) {
      e.preventDefault();
      index = index >= $tabs.length - 1 ? 0 : index + 1;
      $tabs.eq(index).focus().trigger('click');
    }
  });

  /* スマホのみ: テーブルが表示されたタイミングで Scroll ↔ を表示し、一定時間で消す */
  var $weightWrap = $('#weightList .weightsTableWrap');
  var scrollHintClass = 'weightsTableScrollHint';
  var hintShowDuration = 4000;
  var hintFadeDuration = 400;
  var hintTimer = null;
  var hintShownForTable = false;

  function showTableScrollHint() {
    if (!$weightWrap.length || $weightWrap.find('.' + scrollHintClass).length) return;
    var $hint = $('<div class="' + scrollHintClass + '">' +
      '<span class="' + scrollHintClass + '-text">Scroll</span>' +
      '<span class="' + scrollHintClass + '-icon">↔</span></div>');
    $weightWrap.append($hint);

    hintTimer = setTimeout(function() {
      hintTimer = null;
      $hint.addClass('is-fading');
      setTimeout(function() {
        $hint.remove();
      }, hintFadeDuration);
    }, hintShowDuration);
  }

  function clearTableScrollHint() {
    if (hintTimer) clearTimeout(hintTimer);
    hintTimer = null;
    hintShownForTable = false;
    $weightWrap.find('.' + scrollHintClass).remove();
  }

  if ($weightWrap.length) {
    var tableEl = $weightWrap[0];
    var observer = new IntersectionObserver(function(entries) {
      var entry = entries[0];
      if (!entry || !entry.isIntersecting) return;
      if (!window.matchMedia('(max-width: 768px)').matches) return;
      if (hintShownForTable) return;
      hintShownForTable = true;
      showTableScrollHint();
    }, { root: null, rootMargin: '0px', threshold: 0.2 });

    observer.observe(tableEl);

    $(window).on('resize', function() {
      if (!window.matchMedia('(max-width: 768px)').matches) {
        clearTableScrollHint();
      }
    });
  }
});
