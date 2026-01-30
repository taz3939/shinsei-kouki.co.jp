/**
 * 事業内容ページ - タブ切り替え
 * .businessTab / .businessTabContent の aria と表示制御
 */
jQuery(function($) {
  var $tabs = $('.businessTabs .businessTab');
  var $panels = $('.businessTabContents .businessTabContent');

  if (!$tabs.length || !$panels.length) {
    return;
  }

  function activateTab($tab) {
    var tabId = $tab.data('tab');
    var $panel = $('#'.concat(tabId, '-content'));

    // すべてのタブ・パネルを非アクティブに
    $tabs.removeClass('isActive').attr('aria-selected', 'false');
    $panels.removeClass('isActive');

    // 選択したタブ・パネルをアクティブに
    $tab.addClass('isActive').attr('aria-selected', 'true');
    if ($panel.length) {
      $panel.addClass('isActive');
    }
  }

  $tabs.on('click', function() {
    activateTab($(this));
  });

  // キーボード操作（矢印キー・Tab）
  $tabs.on('keydown', function(e) {
    var index = $tabs.index(this);
    var key = e.which;

    if (key === 37 || key === 38) {
      // 左 or 上: 前のタブ
      e.preventDefault();
      index = index <= 0 ? $tabs.length - 1 : index - 1;
      $tabs.eq(index).focus().trigger('click');
    } else if (key === 39 || key === 40) {
      // 右 or 下: 次のタブ
      e.preventDefault();
      index = index >= $tabs.length - 1 ? 0 : index + 1;
      $tabs.eq(index).focus().trigger('click');
    }
  });
});
