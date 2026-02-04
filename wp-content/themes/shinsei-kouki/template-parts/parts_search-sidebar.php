<?php
/**
 * template-parts/parts_search-sidebar.php
 * サイト内検索サイドバー
 */
?>
<aside class="searchTopics">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-ci.svg" alt="" width="26" height="24" class="sidebarLogo" decoding="async" aria-hidden="true">
    <h2>search</h2>
    <form role="search" method="get" class="searchForm" action="<?php echo esc_url(home_url('/topics/')); ?>">
        <div class="searchFormInner">
            <input 
                type="search" 
                id="searchInput" 
                class="searchInput" 
                name="s" 
                placeholder="お知らせを検索" 
                value="<?php echo esc_attr(get_search_query()); ?>" 
                required
            />
            <button type="submit" class="searchSubmit" aria-label="お知らせを検索">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_search.svg" alt="" width="16" height="16" class="searchSubmitIcon" decoding="async" aria-hidden="true">
            </button>
        </div>
    </form>
</aside>
