<?php
/**
 * template-parts/parts_business-calendar.php
 * 営業カレンダー機能（サイト内で複数配置される可能性があるパーツ）
 * CPT登録・休業日／作業対応不可メタボックス・Ajax・フロント表示
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
        'supports' => array('title'),
        'show_in_rest' => false,
    ));
}
add_action('init', 'shinsei_kouki_register_business_calendar_post_type');

// =============================================================================
// Ajax・メタボックス・表示
// =============================================================================

/** 営業カレンダー表示：最古月（この月以降のみ表示。2025年12月などは表示しない） */
define('SHINSEI_KOUKI_CALENDAR_MIN_YEAR', 2026);
define('SHINSEI_KOUKI_CALENDAR_MIN_MONTH', 1);

/**
 * 表示可能な範囲を取得する。
 * min: 2026年1月固定。max: 登録がある月のうち 2026年1月以降で最も未来の月。
 *
 * @return array { 'min' => ['year'=>int,'month'=>int], 'max' => ['year'=>int,'month'=>int] }
 */
function shinsei_kouki_get_calendar_display_range() {
    $min_year  = (int) SHINSEI_KOUKI_CALENDAR_MIN_YEAR;
    $min_month = (int) SHINSEI_KOUKI_CALENDAR_MIN_MONTH;

    $posts = get_posts(array(
        'post_type'      => 'business_calendar',
        'post_status'     => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));
    $max_year  = $min_year;
    $max_month = $min_month;
    foreach ($posts as $post_id) {
        $y = (int) get_post_meta($post_id, '_calendar_year', true);
        $m = (int) get_post_meta($post_id, '_calendar_month', true);
        if ($m < 1 || $m > 12) {
            continue;
        }
        if ($y < $min_year) {
            continue;
        }
        if ($y === $min_year && $m < $min_month) {
            continue;
        }
        if ($y > $max_year || ($y === $max_year && $m > $max_month)) {
            $max_year  = $y;
            $max_month = $m;
        }
    }
    return array(
        'min' => array('year' => $min_year, 'month' => $min_month),
        'max' => array('year' => $max_year, 'month' => $max_month),
    );
}

function shinsei_kouki_ajax_get_calendar() {
    $year  = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('m');
    $range = shinsei_kouki_get_calendar_display_range();
    $min   = $range['min'];
    $max   = $range['max'];
    if ($year < $min['year'] || ($year === $min['year'] && $month < $min['month'])) {
        $year  = $min['year'];
        $month = $min['month'];
    } elseif ($year > $max['year'] || ($year === $max['year'] && $month > $max['month'])) {
        $year  = $max['year'];
        $month = $max['month'];
    }
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
        '休業日・作業対応不可',
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
    $unavailable = array();
    if ($post->ID > 0 && $post->post_status !== 'auto-draft') {
        $saved_holidays = get_post_meta($post->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays) && !empty($saved_holidays)) {
            $holidays = $saved_holidays;
        }
        $saved_unavailable = get_post_meta($post->ID, '_calendar_unavailable', true);
        if (is_array($saved_unavailable) && !empty($saved_unavailable)) {
            $unavailable = $saved_unavailable;
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
        <p class="description">各日で「休業日」または「作業対応不可」を選べます。未選択の日は通常営業です。日曜は未選択でも休業日として表示されます。</p>
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
                    $is_unavailable = in_array($day, $unavailable);
                    $is_holiday = !$is_unavailable && in_array($day, $holidays);
                    $is_sunday = ($day_of_week == 0);
                    $is_saturday = ($day_of_week == 6);
                    $class = 'calendarDay';
                    if ($is_sunday || $is_saturday) {
                        $class .= ' weekend';
                    }
                    if ($is_unavailable) {
                        $class .= ' unavailable';
                    } elseif ($is_holiday) {
                        $class .= ' holiday';
                    }
                    $status = $is_unavailable ? 'unavailable' : ($is_holiday ? 'holiday' : '');
                    echo '<div class="' . esc_attr($class) . '">';
                    echo '<span class="dayNum">' . esc_html($day) . '</span>';
                    echo '<select name="calendar_day_status[' . esc_attr($day) . ']">';
                    echo '<option value=""' . selected($status, '', false) . '>通常</option>';
                    echo '<option value="holiday"' . selected($status, 'holiday', false) . '>休業日</option>';
                    echo '<option value="unavailable"' . selected($status, 'unavailable', false) . '>作業対応不可</option>';
                    echo '</select>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <script>
        (function($) {
            var savedStatuses = {};
            var originalYear = parseInt($('#calendar_year_input').val());
            var originalMonth = parseInt($('#calendar_month_input').val());
            function readStatusesFromDom() {
                var statuses = {};
                $('#calendar_body select[name^="calendar_day_status"]').each(function() {
                    var match = $(this).attr('name').match(/\[(\d+)\]/);
                    var status = $(this).val();
                    if (match && status) {
                        statuses[parseInt(match[1], 10)] = status;
                    }
                });
                return statuses;
            }
            $(document).ready(function() {
                savedStatuses = readStatusesFromDom();
            });
            function saveStatusStates() {
                var currentYear = parseInt($('#calendar_year_input').val());
                var currentMonth = parseInt($('#calendar_month_input').val());
                if (currentYear === originalYear && currentMonth === originalMonth) {
                    savedStatuses = readStatusesFromDom();
                } else {
                    savedStatuses = {};
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
                    savedStatuses = {};
                } else {
                    saveStatusStates();
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
                    var status = savedStatuses[day] || '';
                    var classNames = 'calendarDay';
                    if (isSunday || isSaturday) classNames += ' weekend';
                    if (status) classNames += ' ' + status;
                    var html = '<div class="' + classNames + '"><span class="dayNum">' + day + '</span><select name="calendar_day_status[' + day + ']">'
                        + '<option value=""' + (status === '' ? ' selected' : '') + '>通常</option>'
                        + '<option value="holiday"' + (status === 'holiday' ? ' selected' : '') + '>休業日</option>'
                        + '<option value="unavailable"' + (status === 'unavailable' ? ' selected' : '') + '>作業対応不可</option>'
                        + '</select></div>';
                    $calendarBody.append(html);
                }
            }
            $('#calendar_body').on('change', 'select', function() {
                var $cell = $(this).closest('.calendarDay');
                $cell.removeClass('holiday unavailable');
                var status = $(this).val();
                if (status) {
                    $cell.addClass(status);
                }
            });
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
        .calendarAdmin .calendarDay { background: #fff; border: 1px solid #ddd; padding: 6px 4px; text-align: center; min-height: 72px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; }
        .calendarAdmin .calendarDay.empty { background: #f9f9f9; border: none; }
        .calendarAdmin .calendarDay.weekend { background: #e8f4f8; }
        .calendarAdmin .calendarDay.holiday { background: #ffebee; }
        .calendarAdmin .calendarDay.unavailable { background: #ffd700; }
        .calendarAdmin .calendarDay .dayNum { font-weight: bold; line-height: 1.2; }
        .calendarAdmin .calendarDay select { width: 100%; max-width: 108px; font-size: 11px; }
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
    $holidays = array();
    $unavailable = array();
    if (isset($_POST['calendar_day_status']) && is_array($_POST['calendar_day_status'])) {
        foreach ($_POST['calendar_day_status'] as $day => $status) {
            $day = intval($day);
            if ($day < 1 || $day > 31) {
                continue;
            }
            $status = sanitize_text_field($status);
            if ($status === 'holiday') {
                $holidays[] = $day;
            } elseif ($status === 'unavailable') {
                $unavailable[] = $day;
            }
        }
    }
    $holidays = array_values(array_unique($holidays));
    $unavailable = array_values(array_unique($unavailable));
    sort($holidays);
    sort($unavailable);
    update_post_meta($post_id, '_calendar_holidays', $holidays);
    update_post_meta($post_id, '_calendar_unavailable', $unavailable);
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
    $year  = intval($year);
    $month = intval($month);

    $range = shinsei_kouki_get_calendar_display_range();
    $min   = $range['min'];
    $max   = $range['max'];
    if ($year < $min['year'] || ($year === $min['year'] && $month < $min['month'])) {
        $year  = $min['year'];
        $month = $min['month'];
    } elseif ($year > $max['year'] || ($year === $max['year'] && $month > $max['month'])) {
        $year  = $max['year'];
        $month = $max['month'];
    }

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
    $unavailable = array();
    if (!empty($calendar_posts)) {
        $saved_holidays = get_post_meta($calendar_posts[0]->ID, '_calendar_holidays', true);
        if (is_array($saved_holidays)) {
            $holidays = $saved_holidays;
        }
        $saved_unavailable = get_post_meta($calendar_posts[0]->ID, '_calendar_unavailable', true);
        if (is_array($saved_unavailable)) {
            $unavailable = $saved_unavailable;
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

    $prev_disabled = ($year === $min['year'] && $month === $min['month']);
    $next_disabled = ($year === $max['year'] && $month === $max['month']);
    ?>
    <div class="businessCalendar">
        <h3><?php echo esc_html($year); ?><small>年</small> <?php echo esc_html($month); ?><small>月</small></h3>
        <button type="button" class="btnPrev" data-year="<?php echo esc_attr($prev_year); ?>" data-month="<?php echo esc_attr($prev_month); ?>"<?php echo $prev_disabled ? ' disabled' : ''; ?>></button>
        <button type="button" class="btnNext" data-year="<?php echo esc_attr($next_year); ?>" data-month="<?php echo esc_attr($next_month); ?>"<?php echo $next_disabled ? ' disabled' : ''; ?>></button>
        <p class="calendarLegend">
            <span class="legendItem"><span class="legendColor holiday"></span>休業日</span>
            <span class="legendItem"><span class="legendColor unavailable"></span>作業対応不可</span>
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
                            $is_unavailable = in_array($day, $unavailable);
                            $is_holiday = !$is_unavailable && ($is_sunday || in_array($day, $holidays));
                            $is_today = ($year == date('Y') && $month == date('m') && $day == date('d'));
                            $class = '';
                            if ($is_sunday) $class .= ' sunday';
                            if ($is_saturday) $class .= ' saturday';
                            if ($is_holiday) $class .= ' holiday';
                            if ($is_unavailable) $class .= ' unavailable';
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
