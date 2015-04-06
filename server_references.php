<?

    if (!$root) {
        $root = "../";
    }

    /* Entities */
    require_once $root."inc/classes/entities/entitybase.class.php";
    require_once $root."inc/classes/entities/user.class.php";
    require_once $root."inc/classes/entities/settings.class.php";
    require_once $root."inc/classes/entities/profile.class.php";
    require_once $root."inc/classes/entities/message.class.php";
    require_once $root."inc/classes/entities/room.class.php";
    require_once $root."inc/classes/entities/status.class.php";
    require_once $root."inc/classes/entities/nickname.class.php";
    require_once $root."inc/classes/entities/wakeup.class.php";
    require_once $root."inc/classes/entities/user_complete.class.php";
    require_once $root."inc/classes/entities/tree_node.class.php";
    require_once $root."inc/classes/entities/ignore.class.php";
    require_once $root."inc/classes/entities/forumbase.class.php";
    require_once $root."inc/classes/entities/forum_recordbase.class.php";
    require_once $root."inc/classes/entities/forum.class.php";
    require_once $root."inc/classes/entities/forum_record.class.php";
    require_once $root."inc/classes/entities/forum_users.class.php";
    require_once $root."inc/classes/entities/journal_record.class.php";
    require_once $root."inc/classes/entities/journal_comment.class.php";
    require_once $root."inc/classes/entities/room_users.class.php";
    require_once $root."inc/classes/entities/journal.class.php";
    require_once $root."inc/classes/entities/journal_template.class.php";
    require_once $root."inc/classes/entities/journal_skin.class.php";
    require_once $root."inc/classes/entities/journal_settings.class.php";
    require_once $root."inc/classes/entities/journal_friend.class.php";
    require_once $root."inc/classes/entities/admin_comment.class.php";
    require_once $root."inc/classes/entities/banned_address.class.php";
    require_once $root."inc/classes/entities/news.class.php";
    require_once $root."inc/classes/entities/news_record.class.php";
    require_once $root."inc/classes/entities/gallery.class.php";
    require_once $root."inc/classes/entities/gallery_photo.class.php";
    require_once $root."inc/classes/entities/gallery_comment.class.php";
    require_once $root."inc/classes/entities/scheduled_task.class.php";
    require_once $root."inc/classes/entities/ratings.class.php";

    require_once $root."inc/classes/entities/tag.class.php";
    require_once $root."inc/classes/entities/record_tag.class.php";
    require_once $root."inc/classes/entities/tree_node.class.php";
    require_once $root."inc/classes/entities/openid_provider.class.php";
    require_once $root."inc/classes/entities/user_openid.class.php";

    /* Service classes */
    require_once $root."inc/classes/sql.class.php";
    require_once $root."inc/classes/pager.class.php";
    require_once $root."inc/classes/entities/calendar.class.php";
    
    /* Helpers */
    require_once $root."inc/helpers/security.helper.php";
    require_once $root."inc/helpers/string.helper.php";
    require_once $root."inc/helpers/date.helper.php";
    require_once $root."inc/helpers/misc.helper.php";
    require_once $root."inc/helpers/log.helper.php";
    require_once $root."inc/helpers/cache.helper.php";
    require_once $root."inc/helpers/journal.helper.php";
    require_once $root."inc/helpers/email.helper.php";
    require_once $root."inc/helpers/image.helper.php";
    require_once $root."inc/helpers/scheduler.helper.php";
    require_once $root."inc/helpers/rss.helper.php";
    require_once $root."inc/helpers/debug.helper.php";
    require_once $root."inc/helpers/rating.helper.php";

    /* Config & Initialization */
    require_once $root."inc/settings.php";

?>