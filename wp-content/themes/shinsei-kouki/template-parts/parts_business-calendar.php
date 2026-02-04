<?php
/**
 * template-parts/parts_business-calendar.php
 * 営業カレンダー機能（サイト内で複数配置される可能性があるパーツ）
 * CPT登録・休業日設定メタボックス・Ajax・フロント表示
 * ※ functions.php から require で読み込み（get_template_part では読まない）
 */

// =============================================================================
// 営業カレンダー CPT 登録
// =============================================================================

function shinsei_kouki_register_business_calendar_post_type() {
    register_post_type('business_calendar', array(
        'labels' => array(
            'name' => '営業カレンダー',
            'singular_name' => '営業カレンダー',
            'add_new' => '新規追加',
            'add_new_item' => '営業カレンダーを追加',
            'edit_item' => '営業カレンダーを編集',
            'new_item' => '新規営業カレンダー',
            'view_item' => '営業カレンダーを表示',
            'search_items' => '営業カレンダーを検索',
            'not_found' => '営業カレンダーが見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に営業カレンダーはありません',
            'all_items' => 'すべての営業カレンダー',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'custom-fields'),
        'show_in_rest' => false,
    ));
}
add_action('init', 'shinsei_kouki_register_business_calendar_post_type');

// =============================================================================
// Ajax・メタボックス・表示
// =============================================================================

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

function shinsei_kouki_calendar_metabox_callback($post) {
    wp_nonce_field('shinsei_kouki_calendar_metabox', 'shinsei_kouki_calendar_nonce');
    $calendar_year = get_post_meta($post->ID, '_calendar_year', true);
    $calendar_month = get_post_meta($post->ID, '_calendar_month', true);
    if (empty($calendar_year) || empty($calendar_month)) {
        if (preg_match('/(\d{4})年(\d{1,2})月/', $post->post_title, $matches)) {
            $calendar_year = $matches[1];
            $calendar_month = sprintf('%02d', $matches[2]);
        } else {
            $calendar_year = date('Y');
            $calendar_month = date('m');
        }
    }
    $holidays = array();
    if ($post->ID > 0 && $post->post_status !== 'auto-draft') {
        $saved_holidays = get_post_meta($post->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays) && !empty($saved_holidays)) {
            $holidays = $saved_holidays;
        }
    }
    $days_in_month = date('t', strtotime($calendar_year . '-' . $calendar_month . '-01'));
    $first_day = date('w', strtotime($calendar_year . '-' . $calendar_month . '-01'));
    ?>
    <div class="calendarAdmin">
        <p>
            <label>年：<input type="number" id="calendar_year_input" name="calendar_year" value="<?php echo esc_attr($calendar_year); ?>" min="2020" max="2100" required></label>
            <label>月：<input type="number" id="calendar_month_input" name="calendar_month" value="<?php echo esc_attr($calendar_month); ?>" min="1" max="12" required></label>
        </p>
        <p class="description">タイトルに「<span id="calendar_title_hint"><?php echo esc_html($calendar_year); ?>年<?php echo esc_html(intval($calendar_month)); ?>月</span>」と入力してください。</p>
        <div class="calendarGrid">
            <div class="calendarHeader">
                <div class="calendarDayHeader">日</div>
                <div class="calendarDayHeader">月</div>
                <div class="calendarDayHeader">火</div>
                <div class="calendarDayHeader">水</div>
                <div class="calendarDayHeader">木</div>
                <div class="calendarDayHeader">金</div>
                <div class="calendarDayHeader">土</div>
            </div>
            <div class="calendarBody" id="calendar_body">
                <?php
                for ($i = 0; $i < $first_day; $i++) {
                    echo '<div class="calendarDay empty"></div>';
                }
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $day_of_week = date('w', strtotime($calendar_year . '-' . $calendar_month . '-' . sprintf('%02d', $day)));
                    $is_holiday = in_array($day, $holidays);
                    $is_sunday = ($day_of_week == 0);
                    $is_saturday = ($day_of_week == 6);
                    $class = 'calendarDay';
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
            $(document).ready(function() {
                savedHolidays = [];
                $('#calendar_body input[type="checkbox"]').each(function() {
                    if ($(this).is(':checked')) {
                        savedHolidays.push(parseInt($(this).val()));
                    }
                });
            });
            function saveHolidayStates() {
                var currentYear = parseInt($('#calendar_year_input').val());
                var currentMonth = parseInt($('#calendar_month_input').val());
                if (currentYear === originalYear && currentMonth === originalMonth) {
                    savedHolidays = [];
                    $('#calendar_body input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            savedHolidays.push(parseInt($(this).val()));
                        }
                    });
                } else {
                    savedHolidays = [];
                }
            }
            function updateTitle() {
                var year = parseInt($('#calendar_year_input').val());
                var month = parseInt($('#calendar_month_input').val());
                if (year && month && month >= 1 && month <= 12) {
                    var titleText = year + '年' + month + '月';
                    var $titleField = $('#title');
                    var currentTitle = $titleField.val();
                    if (!currentTitle || /^\d{4}年\d{1,2}月/.test(currentTitle)) {
                        $titleField.val(titleText);
                    }
                }
            }
            function regenerateCalendar() {
                var year = parseInt($('#calendar_year_input').val());
                var month = parseInt($('#calendar_month_input').val());
                if (!year || !month || month < 1 || month > 12) return;
                $('#calendar_title_hint').text(year + '年' + month + '月');
                updateTitle();
                if (year !== originalYear || month !== originalMonth) {
                    savedHolidays = [];
                } else {
                    saveHolidayStates();
                }
                var daysInMonth = new Date(year, month, 0).getDate();
                var firstDay = new Date(year, month - 1, 1).getDay();
                var $calendarBody = $('#calendar_body');
                $calendarBody.empty();
                for (var i = 0; i < firstDay; i++) {
                    $calendarBody.append('<div class="calendarDay empty"></div>');
                }
                for (var day = 1; day <= daysInMonth; day++) {
                    var date = new Date(year, month - 1, day);
                    var dayOfWeek = date.getDay();
                    var isSunday = (dayOfWeek === 0);
                    var isSaturday = (dayOfWeek === 6);
                    var isHoliday = savedHolidays.indexOf(day) !== -1;
                    var classNames = 'calendarDay';
                    if (isSunday || isSaturday) classNames += ' weekend';
                    if (isHoliday) classNames += ' holiday';
                    var checked = isHoliday ? ' checked' : '';
                    var html = '<div class="' + classNames + '"><label><input type="checkbox" name="calendar_holidays[]" value="' + day + '"' + checked + '><span>' + day + '</span></label></div>';
                    $calendarBody.append(html);
                }
            }
            $('#calendar_year_input, #calendar_month_input').on('change', function() {
                regenerateCalendar();
            });
            $(document).ready(function() {
                var $titleField = $('#title');
                if (!$titleField.val()) updateTitle();
            });
        })(jQuery);
        </script>
        <style>
        .calendarAdmin .calendarGrid { max-width: 700px; margin: 20px 0; }
        .calendarAdmin .calendarHeader { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; margin-bottom: 2px; }
        .calendarAdmin .calendarDayHeader { background: #f0f0f0; padding: 8px; text-align: center; font-weight: bold; }
        .calendarAdmin .calendarBody { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
        .calendarAdmin .calendarDay { background: #fff; border: 1px solid #ddd; padding: 8px; text-align: center; min-height: 40px; display: flex; align-items: center; justify-content: center; }
        .calendarAdmin .calendarDay.empty { background: #f9f9f9; border: none; }
        .calendarAdmin .calendarDay.weekend { background: #e8f4f8; }
        .calendarAdmin .calendarDay.holiday { background: #ffebee; }
        .calendarAdmin .calendarDay label { display: flex; flex-direction: column; align-items: center; cursor: pointer; width: 100%; }
        .calendarAdmin .calendarDay input[type="checkbox"] { margin-bottom: 4px; }
        </style>
    </div>
    <?php
}

function shinsei_kouki_save_calendar_metabox($post_id) {
    if (!isset($_POST['shinsei_kouki_calendar_nonce']) || !wp_verify_nonce($_POST['shinsei_kouki_calendar_nonce'], 'shinsei_kouki_calendar_metabox')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'business_calendar') {
        return;
    }
    if (isset($_POST['calendar_year'])) {
        update_post_meta($post_id, '_calendar_year', sanitize_text_field($_POST['calendar_year']));
    }
    if (isset($_POST['calendar_month'])) {
        update_post_meta($post_id, '_calendar_month', sprintf('%02d', intval($_POST['calendar_month'])));
    }
    if (isset($_POST['calendar_holidays']) && is_array($_POST['calendar_holidays']) && !empty($_POST['calendar_holidays'])) {
        $holidays = array_unique(array_map('intval', $_POST['calendar_holidays']));
        sort($holidays);
        update_post_meta($post_id, '_calendar_holidays', $holidays);
    } else {
        update_post_meta($post_id, '_calendar_holidays', array());
    }
}
add_action('save_post', 'shinsei_kouki_save_calendar_metabox');

function shinsei_kouki_get_default_holidays($year, $month) {
    $holidays = array();
    $days_in_month = date('t', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
    $first_saturday = null;
    for ($day = 1; $day <= 7; $day++) {
        $timestamp = strtotime($year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day));
        if (date('w', $timestamp) == 6) {
            $first_saturday = $day;
            break;
        }
    }
    if ($first_saturday !== null) {
        foreach (array($first_saturday + 7, $first_saturday + 14, $first_saturday + 21) as $d) {
            if ($d <= $days_in_month) {
                $holidays[] = $d;
            }
        }
    }
    for ($day = 1; $day <= $days_in_month; $day++) {
        $timestamp = strtotime($year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day));
        if (date('w', $timestamp) == 0) {
            $holidays[] = $day;
        }
    }
    $holidays = array_unique($holidays);
    sort($holidays);
    return $holidays;
}

function shinsei_kouki_display_calendar($year = null, $month = null) {
    if (empty($year)) {
        $year = date('Y');
    }
    if (empty($month)) {
        $month = date('m');
    }
    $year = intval($year);
    $month = intval($month);
    $calendar_posts = get_posts(array(
        'post_type' => 'business_calendar',
        'meta_query' => array(
            'relation' => 'AND',
            array('key' => '_calendar_year', 'value' => $year, 'compare' => '='),
            array('key' => '_calendar_month', 'value' => sprintf('%02d', $month), 'compare' => '='),
        ),
        'posts_per_page' => 1,
        'post_status' => 'publish',
    ));
    $holidays = array();
    if (!empty($calendar_posts)) {
        $saved_holidays = get_post_meta($calendar_posts[0]->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays)) {
            $holidays = $saved_holidays;
        }
    } else {
        $holidays = shinsei_kouki_get_default_holidays($year, $month);
    }
    $days_in_month = date('t', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
    $first_day = date('w', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));
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
                for ($week = 0; $week < 6; $week++) {
                    echo '<tr>';
                    for ($dow = 0; $dow < 7; $dow++) {
                        if (($week == 0 && $dow < $first_day) || $day > $days_in_month) {
                            echo '<td class="empty"></td>';
                        } else {
                            $is_sunday = ($dow == 0);
                            $is_saturday = ($dow == 6);
                            $is_holiday = $is_sunday || in_array($day, $holidays);
                            $is_today = ($year == date('Y') && $month == date('m') && $day == date('d'));
                            $class = '';
                            if ($is_sunday) $class .= ' sunday';
                            if ($is_saturday) $class .= ' saturday';
                            if ($is_holiday) $class .= ' holiday';
                            if ($is_today) $class .= ' today';
                            echo '<td class="' . esc_attr(trim($class)) . '">';
                            echo $is_today ? '<em>' . esc_html($day) . '</em>' : esc_html($day);
                            echo '</td>';
                            $day++;
                        }
                    }
                    echo '</tr>';
                    if ($day > $days_in_month) break;
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
