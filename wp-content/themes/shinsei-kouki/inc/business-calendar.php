<?php
/**
 * business-calendar.php
 * 営業カレンダー機能
 */

// Ajaxハンドラー：カレンダーHTMLを返す
function shinsei_kouki_ajax_get_calendar() {
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('m');
    
    ob_start();
    shinsei_kouki_display_calendar($year, $month);
    $calendar_html = ob_get_clean();
    
    wp_send_json_success($calendar_html);
}
add_action('wp_ajax_shinsei_kouki_get_calendar', 'shinsei_kouki_ajax_get_calendar');
add_action('wp_ajax_nopriv_shinsei_kouki_get_calendar', 'shinsei_kouki_ajax_get_calendar');


// 営業カレンダーのメタボックスを追加
function shinsei_kouki_add_calendar_metabox() {
    add_meta_box(
        'business_calendar_metabox',
        '休業日設定',
        'shinsei_kouki_calendar_metabox_callback',
        'business_calendar',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'shinsei_kouki_add_calendar_metabox');

// メタボックスのコールバック関数
function shinsei_kouki_calendar_metabox_callback($post) {
    wp_nonce_field('shinsei_kouki_calendar_metabox', 'shinsei_kouki_calendar_nonce');
    
    // 年月を取得（タイトルから抽出、またはメタデータから）
    $calendar_year = get_post_meta($post->ID, '_calendar_year', true);
    $calendar_month = get_post_meta($post->ID, '_calendar_month', true);
    
    // タイトルから年月を抽出（例：「2025年11月」）
    if (empty($calendar_year) || empty($calendar_month)) {
        if (preg_match('/(\d{4})年(\d{1,2})月/', $post->post_title, $matches)) {
            $calendar_year = $matches[1];
            $calendar_month = sprintf('%02d', $matches[2]);
        } else {
            $calendar_year = date('Y');
            $calendar_month = date('m');
        }
    }
    
    // 休業日データを取得
    // 新規投稿（IDが0またはauto-draft）の場合は空配列
    // 既存投稿でメタデータが存在しない場合も空配列
    $holidays = array();
    if ($post->ID > 0 && $post->post_status !== 'auto-draft') {
        $saved_holidays = get_post_meta($post->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays) && !empty($saved_holidays)) {
            $holidays = $saved_holidays;
        }
    }
    
    // カレンダーを生成
    $days_in_month = date('t', strtotime($calendar_year . '-' . $calendar_month . '-01'));
    $first_day = date('w', strtotime($calendar_year . '-' . $calendar_month . '-01'));
    
    ?>
    <div class="calendar-admin">
        <p>
            <label>年：<input type="number" id="calendar_year_input" name="calendar_year" value="<?php echo esc_attr($calendar_year); ?>" min="2020" max="2100" required></label>
            <label>月：<input type="number" id="calendar_month_input" name="calendar_month" value="<?php echo esc_attr($calendar_month); ?>" min="1" max="12" required></label>
        </p>
        <p class="description">タイトルに「<span id="calendar_title_hint"><?php echo esc_html($calendar_year); ?>年<?php echo esc_html(intval($calendar_month)); ?>月</span>」と入力してください。</p>
        
        <div class="calendar-grid">
            <div class="calendar-header">
                <div class="calendar-day-header">日</div>
                <div class="calendar-day-header">月</div>
                <div class="calendar-day-header">火</div>
                <div class="calendar-day-header">水</div>
                <div class="calendar-day-header">木</div>
                <div class="calendar-day-header">金</div>
                <div class="calendar-day-header">土</div>
            </div>
            <div class="calendar-body" id="calendar_body">
                <?php
                // 最初の週の空白セル
                for ($i = 0; $i < $first_day; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }
                
                // 日付セル
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $day_of_week = date('w', strtotime($calendar_year . '-' . $calendar_month . '-' . sprintf('%02d', $day)));
                    $is_holiday = in_array($day, $holidays);
                    $is_sunday = ($day_of_week == 0);
                    $is_saturday = ($day_of_week == 6);
                    
                    $class = 'calendar-day';
                    if ($is_sunday || $is_saturday) {
                        $class .= ' weekend';
                    }
                    if ($is_holiday) {
                        $class .= ' holiday';
                    }
                    
                    echo '<div class="' . esc_attr($class) . '">';
                    echo '<label>';
                    echo '<input type="checkbox" name="calendar_holidays[]" value="' . esc_attr($day) . '"' . ($is_holiday ? ' checked' : '') . '>';
                    echo '<span>' . esc_html($day) . '</span>';
                    echo '</label>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <script>
        (function($) {
            var savedHolidays = [];
            var originalYear = parseInt($('#calendar_year_input').val());
            var originalMonth = parseInt($('#calendar_month_input').val());
            
            // ページ読み込み時に既存のチェック状態を保存（初期状態）
            $(document).ready(function() {
                savedHolidays = [];
                $('#calendar_body input[type="checkbox"]').each(function() {
                    if ($(this).is(':checked')) {
                        savedHolidays.push(parseInt($(this).val()));
                    }
                });
            });
            
            // 既存のチェック状態を保存（年・月変更時のみ）
            function saveHolidayStates() {
                // 年・月が変更されていない場合は保存しない（初期状態を保持）
                var currentYear = parseInt($('#calendar_year_input').val());
                var currentMonth = parseInt($('#calendar_month_input').val());
                
                if (currentYear === originalYear && currentMonth === originalMonth) {
                    // 同じ月の場合は、現在のチェック状態を保存
                    savedHolidays = [];
                    $('#calendar_body input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            savedHolidays.push(parseInt($(this).val()));
                        }
                    });
                } else {
                    // 年・月が変更された場合は、保存済みの休業日をクリア（新規月なので）
                    savedHolidays = [];
                }
            }
            
            // タイトルフィールドを自動入力
            function updateTitle() {
                var year = parseInt($('#calendar_year_input').val());
                var month = parseInt($('#calendar_month_input').val());
                
                if (year && month && month >= 1 && month <= 12) {
                    var titleText = year + '年' + month + '月';
                    var $titleField = $('#title');
                    
                    // タイトルフィールドが空、または既存の年月形式の場合は更新
                    var currentTitle = $titleField.val();
                    if (!currentTitle || /^\d{4}年\d{1,2}月/.test(currentTitle)) {
                        $titleField.val(titleText);
                    }
                }
            }
            
            // カレンダーを再生成
            function regenerateCalendar() {
                var year = parseInt($('#calendar_year_input').val());
                var month = parseInt($('#calendar_month_input').val());
                
                if (!year || !month || month < 1 || month > 12) {
                    return;
                }
                
                // タイトルヒントを更新
                $('#calendar_title_hint').text(year + '年' + month + '月');
                
                // タイトルフィールドを自動入力
                updateTitle();
                
                // 年・月が変更された場合は、保存済みの休業日をクリア
                if (year !== originalYear || month !== originalMonth) {
                    savedHolidays = [];
                } else {
                    // 同じ月の場合は、現在のチェック状態を保存
                    saveHolidayStates();
                }
                
                // 月の日数を計算
                var daysInMonth = new Date(year, month, 0).getDate();
                var firstDay = new Date(year, month - 1, 1).getDay();
                
                // カレンダーボディをクリア
                var $calendarBody = $('#calendar_body');
                $calendarBody.empty();
                
                // 最初の週の空白セル
                for (var i = 0; i < firstDay; i++) {
                    $calendarBody.append('<div class="calendar-day empty"></div>');
                }
                
                // 日付セルを生成
                for (var day = 1; day <= daysInMonth; day++) {
                    var date = new Date(year, month - 1, day);
                    var dayOfWeek = date.getDay();
                    var isSunday = (dayOfWeek === 0);
                    var isSaturday = (dayOfWeek === 6);
                    // 保存済みの休業日のみをチェック（デフォルトルールは適用しない）
                    var isHoliday = savedHolidays.indexOf(day) !== -1;
                    
                    var classNames = 'calendar-day';
                    if (isSunday || isSaturday) {
                        classNames += ' weekend';
                    }
                    if (isHoliday) {
                        classNames += ' holiday';
                    }
                    
                    var checked = isHoliday ? ' checked' : '';
                    var html = '<div class="' + classNames + '">' +
                               '<label>' +
                               '<input type="checkbox" name="calendar_holidays[]" value="' + day + '"' + checked + '>' +
                               '<span>' + day + '</span>' +
                               '</label>' +
                               '</div>';
                    $calendarBody.append(html);
                }
            }
            
            // 年・月の入力フィールドにイベントリスナーを追加
            $('#calendar_year_input, #calendar_month_input').on('change', function() {
                regenerateCalendar();
            });
            
            // ページ読み込み時にもタイトルを更新（新規投稿の場合）
            $(document).ready(function() {
                var $titleField = $('#title');
                if (!$titleField.val()) {
                    updateTitle();
                }
            });
        })(jQuery);
        </script>
        
        <style>
        .calendar-admin .calendar-grid {
            max-width: 700px;
            margin: 20px 0;
        }
        .calendar-admin .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            margin-bottom: 2px;
        }
        .calendar-admin .calendar-day-header {
            background: #f0f0f0;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        .calendar-admin .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }
        .calendar-admin .calendar-day {
            background: #fff;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-admin .calendar-day.empty {
            background: #f9f9f9;
            border: none;
        }
        .calendar-admin .calendar-day.weekend {
            background: #e8f4f8;
        }
        .calendar-admin .calendar-day.holiday {
            background: #ffebee;
        }
        .calendar-admin .calendar-day label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            width: 100%;
        }
        .calendar-admin .calendar-day input[type="checkbox"] {
            margin-bottom: 4px;
        }
        </style>
    </div>
    <?php
}

// メタデータを保存
function shinsei_kouki_save_calendar_metabox($post_id) {
    // 非チェック
    if (!isset($_POST['shinsei_kouki_calendar_nonce']) || 
        !wp_verify_nonce($_POST['shinsei_kouki_calendar_nonce'], 'shinsei_kouki_calendar_metabox')) {
        return;
    }
    
    // 自動保存チェック
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 営業カレンダーの投稿タイプチェック
    if (get_post_type($post_id) !== 'business_calendar') {
        return;
    }
    
    // 年月を保存
    if (isset($_POST['calendar_year'])) {
        $calendar_year = sanitize_text_field($_POST['calendar_year']);
        update_post_meta($post_id, '_calendar_year', $calendar_year);
    }
    if (isset($_POST['calendar_month'])) {
        $calendar_month = sanitize_text_field($_POST['calendar_month']);
        // 月を2桁のゼロパディング形式で保存（例：1 → '01', 2 → '02'）
        $calendar_month = sprintf('%02d', intval($calendar_month));
        update_post_meta($post_id, '_calendar_month', $calendar_month);
    }
    
    // 休業日を保存
    if (isset($_POST['calendar_holidays']) && is_array($_POST['calendar_holidays']) && !empty($_POST['calendar_holidays'])) {
        $holidays = array_map('intval', $_POST['calendar_holidays']);
        $holidays = array_unique($holidays);
        sort($holidays);
        update_post_meta($post_id, '_calendar_holidays', $holidays);
    } else {
        // 休業日が選択されていない場合は空配列を保存
        update_post_meta($post_id, '_calendar_holidays', array());
    }
}
add_action('save_post', 'shinsei_kouki_save_calendar_metabox');

// デフォルトの休業日を計算（第2・3・4土曜日、日曜日）
function shinsei_kouki_get_default_holidays($year, $month) {
    $holidays = array();
    $days_in_month = date('t', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
    
    // 第2・3・4土曜日を計算
    $first_saturday = null;
    for ($day = 1; $day <= 7; $day++) {
        $timestamp = strtotime($year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day));
        if (date('w', $timestamp) == 6) { // 6 = 土曜日
            $first_saturday = $day;
            break;
        }
    }
    
    if ($first_saturday !== null) {
        // 第2土曜日（最初の土曜日 + 7日）
        $second_saturday = $first_saturday + 7;
        if ($second_saturday <= $days_in_month) {
            $holidays[] = $second_saturday;
        }
        
        // 第3土曜日（最初の土曜日 + 14日）
        $third_saturday = $first_saturday + 14;
        if ($third_saturday <= $days_in_month) {
            $holidays[] = $third_saturday;
        }
        
        // 第4土曜日（最初の土曜日 + 21日）
        $fourth_saturday = $first_saturday + 21;
        if ($fourth_saturday <= $days_in_month) {
            $holidays[] = $fourth_saturday;
        }
    }
    
    // すべての日曜日を追加
    for ($day = 1; $day <= $days_in_month; $day++) {
        $timestamp = strtotime($year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day));
        if (date('w', $timestamp) == 0) { // 0 = 日曜日
            $holidays[] = $day;
        }
    }
    
    // 重複を削除してソート
    $holidays = array_unique($holidays);
    sort($holidays);
    
    return $holidays;
}

// カレンダー表示関数
function shinsei_kouki_display_calendar($year = null, $month = null) {
    if (empty($year)) {
        $year = date('Y');
    }
    if (empty($month)) {
        $month = date('m');
    }
    
    $year = intval($year);
    $month = intval($month);
    
    // 該当月の営業カレンダー投稿を取得
    $calendar_posts = get_posts(array(
        'post_type' => 'business_calendar',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_calendar_year',
                'value' => $year,
                'compare' => '=',
            ),
            array(
                'key' => '_calendar_month',
                'value' => sprintf('%02d', $month),
                'compare' => '=',
            ),
        ),
        'posts_per_page' => 1,
        'post_status' => 'publish',
    ));
    
    // 休業日データを取得
    // 投稿が存在する場合はその設定を使用、存在しない場合はデフォルトルールを使用
    $holidays = array();
    if (!empty($calendar_posts)) {
        // 投稿が存在する場合：投稿の設定を使用（空配列でもOK）
        $saved_holidays = get_post_meta($calendar_posts[0]->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays)) {
            $holidays = $saved_holidays;
        }
        // 投稿が存在する場合は、デフォルトルールは適用しない（空配列のまま）
    } else {
        // 投稿が存在しない場合：デフォルトルール（第2・3・4土曜日、日曜日）を使用
        $holidays = shinsei_kouki_get_default_holidays($year, $month);
    }
    
    // カレンダーを生成
    $days_in_month = date('t', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
    $first_day = date('w', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
    
    // 前の月・次の月を計算
    $prev_month = $month - 1;
    $prev_year = $year;
    if ($prev_month < 1) {
        $prev_month = 12;
        $prev_year--;
    }
    
    $next_month = $month + 1;
    $next_year = $year;
    if ($next_month > 12) {
        $next_month = 1;
        $next_year++;
    }
    
    ?>
    
    <div class="businessCalendar">
        <h3><?php echo esc_html($year); ?><small>年</small> <?php echo esc_html($month); ?><small>月</small></h3>
        <button type="button" class="btnPrev" data-year="<?php echo esc_attr($prev_year); ?>" data-month="<?php echo esc_attr($prev_month); ?>"></button>
        <button type="button" class="btnNext" data-year="<?php echo esc_attr($next_year); ?>" data-month="<?php echo esc_attr($next_month); ?>"></button>
        <p class="calendarLegend">
            <span class="legendItem"><span class="legendColor holiday"></span>休業日</span>
        </p>
        <table class="calendarTable">
            <thead>
                <tr>
                    <th class="sunday">日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th class="saturday">土</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $day = 1;
                $current_date = date('Y-m-d');
                
                for ($week = 0; $week < 6; $week++) {
                    echo '<tr>';
                    for ($dow = 0; $dow < 7; $dow++) {
                        if (($week == 0 && $dow < $first_day) || $day > $days_in_month) {
                            echo '<td class="empty"></td>';
                        } else {
                            $is_sunday = ($dow == 0);
                            $is_saturday = ($dow == 6);
                            
                            // 休業日判定：投稿の設定がある場合はそれを使用、ない場合はデフォルトルール
                            // 日曜日は常に休業日として扱う
                            $is_holiday = $is_sunday || in_array($day, $holidays);
                            
                            $is_today = ($year == date('Y') && $month == date('m') && $day == date('d'));
                            
                            $class = '';
                            if ($is_sunday) {
                                $class .= ' sunday';
                            }
                            if ($is_saturday) {
                                $class .= ' saturday';
                            }
                            if ($is_holiday) {
                                $class .= ' holiday';
                            }
                            if ($is_today) {
                                $class .= ' today';
                            }
                            
                            echo '<td class="' . esc_attr(trim($class)) . '">';
                            if ($is_today) {
                                echo '<em>' . esc_html($day) . '</em>';
                            } else {
                                echo esc_html($day);
                            }
                            echo '</td>';
                            $day++;
                        }
                    }
                    echo '</tr>';
                    if ($day > $days_in_month) {
                        break;
                    }
                }
                ?>
            </tbody>
        </table>
        
    </div>
    <div class="calendarInfo">
        <div class="infoBox">
            <h4>休業日</h4>
            <p>土（第2･3･4）・日・祝・他</p>
        </div>
        <div class="infoBox">
            <h4>営業時間</h4>
            <p>8:00〜17:00</p>
        </div>
    </div>
    <p class="calendarNote">休日・時間外作業もお気軽にご相談ください</p>
    <?php
}
