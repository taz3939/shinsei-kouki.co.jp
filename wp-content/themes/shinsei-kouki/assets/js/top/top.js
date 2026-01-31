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

// メインビジュアルのぼかし＋フェードインアニメーション（順番に表示）
jQuery(document).ready(function($) {
    var $mainVisual = $('#mainVisual');
    if ($mainVisual.length === 0) return;
    
    var $h1 = $mainVisual.find('h1');
    var $p = $mainVisual.find('p');
    var $mainVisualImage = $mainVisual.find('figure.mainVisualImage');
    
    // 画像の読み込み完了を待つ
    function startAnimations() {
        // 1. h1を表示（300ms後）
        setTimeout(function() {
            $h1.addClass('fadeIn');
        }, 300);
        
        // 2. pを表示（800ms後）
        setTimeout(function() {
            $p.addClass('fadeIn');
        }, 800);
        
        // 3. 画像を表示（1300ms後）
        setTimeout(function() {
            $mainVisualImage.addClass('fadeIn');
        }, 1300);
    }
    
    // 画像の読み込み状態を確認
    if ($mainVisualImage.length > 0) {
        var $img = $mainVisualImage.find('img');
        
        if ($img.length > 0 && $img[0].complete) {
            // 既に読み込み済みの場合
            startAnimations();
        } else if ($img.length > 0) {
            // 読み込み待ちの場合
            $img.on('load', function() {
                startAnimations();
            });
            // 読み込みタイムアウト（5秒後）も考慮
            setTimeout(function() {
                if (!$h1.hasClass('fadeIn')) {
                    startAnimations();
                }
            }, 5000);
        } else {
            // 画像がない場合
            startAnimations();
        }
    } else {
        // 画像要素がない場合
        startAnimations();
    }
});

// aboutBusinessセクションのli要素のぼかし＋フェードインアニメーション（順番に表示）
jQuery(document).ready(function($) {
    var $aboutBusiness = $('#aboutBusiness');
    if ($aboutBusiness.length === 0) return;
    
    var $listItems = $aboutBusiness.find('ol > li');
    if ($listItems.length === 0) return;
    
    var hasAnimated = false;
    
    // Intersection Observer APIを使用して各li要素が表示領域に入ったときにアニメーション
    // パフォーマンス最適化: 一度だけ実行し、監視を解除
    if ('IntersectionObserver' in window) {
        var observerOptions = {
            root: null,
            rootMargin: '50px', // 少し早めに発火
            threshold: 0.1 // 要素の10%が表示領域に入ったら発火
        };
        
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !hasAnimated) {
                    hasAnimated = true;
                    
                    // 最初の要素が表示領域に入ったら、すべてのli要素を順番にアニメーション
                    $listItems.each(function(index) {
                        var $item = $(this);
                        setTimeout(function() {
                            $item.addClass('fadeIn');
                        }, index * 200); // 各要素を200ms間隔で表示
                    });
                    
                    // すべての要素の監視を解除
                    $listItems.each(function() {
                        observer.unobserve(this);
                    });
                    observer.disconnect(); // オブザーバーを完全に切断
                }
            });
        }, observerOptions);
        
        // 各li要素を個別に監視対象に追加
        $listItems.each(function() {
            observer.observe(this);
        });
    } else {
        // Intersection Observerがサポートされていない場合のフォールバック
        // スクロールイベントで対応（パフォーマンスは劣る）
        var checkVisibility = function() {
            if (hasAnimated) return;
            
            var windowTop = $(window).scrollTop();
            var windowBottom = windowTop + $(window).height();
            var sectionTop = $aboutBusiness.offset().top;
            
            if (windowBottom > sectionTop + 100) {
                hasAnimated = true;
                $listItems.each(function(index) {
                    var $item = $(this);
                    setTimeout(function() {
                        $item.addClass('fadeIn');
                    }, index * 200);
                });
                $(window).off('scroll', checkVisibility);
            }
        };
        
        checkVisibility(); // 初期チェック
        $(window).on('scroll', checkVisibility);
    }
});

// handledBrandsセクションのロゴのぼかし＋フェードインアニメーション（順番に表示）
jQuery(document).ready(function($) {
    var $handledBrands = $('#handledBrands');
    if ($handledBrands.length === 0) return;
    
    var $logoItems = $handledBrands.find('ul > li');
    if ($logoItems.length === 0) return;
    
    var hasAnimated = false;
    
    // Intersection Observer APIを使用して各li要素が表示領域に入ったときにアニメーション
    if ('IntersectionObserver' in window) {
        var observerOptions = {
            root: null,
            rootMargin: '50px', // 少し早めに発火
            threshold: 0.1 // 要素の10%が表示領域に入ったら発火
        };
        
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !hasAnimated) {
                    hasAnimated = true;
                    
                    // 最初の要素が表示領域に入ったら、すべてのli要素を順番にアニメーション
                    $logoItems.each(function(index) {
                        var $item = $(this);
                        setTimeout(function() {
                            $item.addClass('fadeIn');
                        }, index * 150); // 各要素を150ms間隔で表示
                    });
                    
                    // すべての要素の監視を解除
                    $logoItems.each(function() {
                        observer.unobserve(this);
                    });
                    observer.disconnect(); // オブザーバーを完全に切断
                }
            });
        }, observerOptions);
        
        // 各li要素を個別に監視対象に追加
        $logoItems.each(function() {
            observer.observe(this);
        });
    } else {
        // Intersection Observerがサポートされていない場合のフォールバック
        var checkVisibility = function() {
            if (hasAnimated) return;
            
            var windowTop = $(window).scrollTop();
            var windowBottom = windowTop + $(window).height();
            var sectionTop = $handledBrands.offset().top;
            
            if (windowBottom > sectionTop + 100) {
                hasAnimated = true;
                $logoItems.each(function(index) {
                    var $item = $(this);
                    setTimeout(function() {
                        $item.addClass('fadeIn');
                    }, index * 150);
                });
                $(window).off('scroll', checkVisibility);
            }
        };
        
        checkVisibility(); // 初期チェック
        $(window).on('scroll', checkVisibility);
    }
});

// カレンダーをAjaxで更新
jQuery(document).ready(function($) {
    function updateCalendar(year, month) {
        var $calendar = $('.businessCalendar');
        
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
                    // 返されたHTMLから.businessCalendarだけを抽出
                    var $newData = $(response.data);
                    var $newCalendar = $newData.filter('.businessCalendar');
                    
                    // 既存の.businessCalendarを新しいものに置き換え
                    $calendar.replaceWith($newCalendar);
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
    $(document).on('click', '.businessCalendar .btnPrev, .businessCalendar .btnNext', function(e) {
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
