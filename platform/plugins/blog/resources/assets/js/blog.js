$(document).ready(() => {
    'use strict';

    BDashboard.loadWidget($('#widget_posts_recent').find('.widget-content'), route('posts.widget.recent-posts'));
});
