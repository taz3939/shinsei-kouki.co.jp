/**
 * top.js
 * トップページ用のJavaScript
 */

jQuery(function() {
  function toggleAccordion() {
    if ($(window).width() < 768) {
      $('#areaSearch .inner > ul > li > h3').off('click').on('click', function() {
        var parentLi = $(this).parent();
        var subMenu = parentLi.children('ul');

        if (subMenu.is(':visible')) {
          subMenu.stop(true, true).slideUp(200);  // スライドで非表示
          parentLi.removeClass('active');
        } else {
          $('#areaSearch .inner > ul > li ul').stop(true, true).slideUp(200);  // 他のメニューを閉じる
          $('#areaSearch .inner > ul > li').removeClass('active');
          subMenu.stop(true, true).slideDown(200).css('display', 'flex'); // スライドで表示後にflex指定
          parentLi.addClass('active');
        }
      });
    } else {
      $('#areaSearch .inner > ul > li > h3').off('click');
      $('#areaSearch .inner > ul > li ul').show().css('display', 'flex'); // Desktopではflexに設定
    }
  }

  toggleAccordion(); // 初期化
  $(window).on('resize', toggleAccordion); // 画面サイズ変更時も対応
});

// カレンダーをAjaxで更新
jQuery(document).ready(function($) {
    function updateCalendar(year, month) {
        var $calendar = $('.business-calendar');
        
        if ($calendar.length === 0) {
            console.error('Calendar element not found');
            return;
        }
        
        console.log('Updating calendar:', year, month);
        
        // ローディング表示
        $calendar.css('opacity', '0.5');
        
        var ajaxUrl = (typeof ajax_object !== 'undefined' && ajax_object.ajax_url) ? ajax_object.ajax_url : '/wp-admin/admin-ajax.php';
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'shinsei_kouki_get_calendar',
                year: year,
                month: month
            },
            success: function(response) {
                console.log('Ajax response:', response);
                if (response && response.success && response.data) {
                    $calendar.replaceWith(response.data);
                } else {
                    console.error('Invalid response:', response);
                    alert('カレンダーの読み込みに失敗しました。');
                    $calendar.css('opacity', '1');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('カレンダーの読み込みに失敗しました。\nエラー: ' + error);
                $calendar.css('opacity', '1');
            },
            complete: function() {
                $calendar.css('opacity', '1');
            }
        });
    }
    
    // 前の月・次の月ボタンのクリックイベント
    $(document).on('click', '.business-calendar .calendar-nav button', function(e) {
        e.preventDefault();
        var year = $(this).data('year');
        var month = $(this).data('month');
        console.log('Button clicked:', year, month);
        if (year && month) {
            updateCalendar(year, month);
        } else {
            console.error('Invalid year or month:', year, month);
        }
    });
});
