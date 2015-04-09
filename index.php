<?php

    $root = "./";
    require_once $root."server_references.php";

    $user = GetAuthorizedUser(true);
    SetUserSessionCookie($user->User);

    require $root."inc/ui_parts/templates.php";
    require $root."inc/ui_parts/news.php";

    #require_once $root."references.php";

?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251" />
    <!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
    <title>������ ����������� ��� &mdash; �������� �������� � ����������� �����</title>
    <meta name="description" content="���������� ��� �������� �������� � ����������� �����. �������, ������� ��� ������� � ��������� ��������. ���������� ����������� ����-������. ���������� � ����������� ����. ����������� ��������, �������, ����������, ������� � ����� ����� �� ���������.">
    <meta name="abstract" content="���������� ��� �������� �������� � ����������� �����. �������, ������� ��� ������� � ��������� ��������. ���������� ����������� ����-������. ���������� � ����������� ����. ����������� ��������, �������, ����������, ������� � ����� ����� �� ���������.">
    <meta name="keywords" content="���, ��������, �������, �����������, � ��������, ����, ����-������, �����, ���������, ������������, �������, ��������, �������, ����������, �������, �����, ����������, ��������������, ����, �������, ��������, saratov, chat, blogs, blog-service">
    <meta name="distribution" content="Global">
    <meta name="rating" content="General">
    <meta name="subject" content="���, �������, �����, ����-������">
    <meta name="page-type" content="���, �������, �����, ����-������">
    <meta name="page-topic" content="���������� ����������� ��� bezumnoe.ru: ������������ ���������� �������, ���������� � ��������">
    <meta name="title" content="������������ ��� � �������� - bezumnoe.ru: ������������ ���������� �������, ���������� � ��������">
    <meta name="site-created" content="17-10-1999">
    <meta name="revisit" content="1 day">
    <meta name="revisit-after" content="1 day">
    <meta name="content-Language" content="russian">
    <meta name="resource-type" content="document">
    <meta name="audience" content="all">
    <meta name="robots" content="index,all">
    <meta name="Address" content="Saratov, Russia">
    <meta name="home_url" content="http://bezumnoe.ru/">
    <!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/template_layout.css" />
    <link rel="stylesheet" href="/css/default3.css" />
    <link rel="stylesheet" href="/css/lettering.css" />
<!--[if IE]>    <link rel="stylesheet" type="text/css" href="/css/ie.css" /><![endif]-->
    <link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
    <?php include $root."/inc/ui_parts/google_analythics.php"; ?>
    <script src="/js1/jquery/jquery.js"></script>
    <script src="/js1/jquery/jquery-ui.js"></script>
    <script src="/js1/jquery/jquery.lettering-0.6.1.min.js"></script>
    <script src="/js1/common.js"></script>
</head>

<body>
    <div class="MainLeft">
        <div class="MainRight" align="center">
            <div class="Main">
                <div>
                    <div class="Column Left">
                        <div class="users_online">
                            <h5>������ � ����</h5>
                            <?php include $root."inc/ui_parts/online_users.php"; ?>
                        </div>

                        <div class="news">
                            <h5>�������</h5>
                            <?php ShowNews(-1, 3); ?>
                        </div>

                        <div class="birthdays">
                            <h5>��� ��������</h5>
                            <?php include $root."inc/ui_parts/birthdays.php"; ?>
                        </div>

                        <div class="photos">
                            <h5>����� ����</h5>
                            <?php include $root."inc/ui_parts/new_photos.php"; ?>
                        </div>
                    </div>

                    <div class="Column Center Left">
                        <div class="Divider Vertical main_links">
                            <div class="welcome">
<p>����� ���������� � ��������� ����������� ��� 
<h1>�������� �������� � ����������� �����</h1>
<p>����� �� ������� ��������� ���������� �����, ���������� �� ������������ ��� ���� � ������ ������� �������� �����. 
<p>���� �� � ��� �������, ������������ � <a href="/rules/" class="Link">���������</a> � <a href="/register" class="Link">�����������������</a>. ���� �� ��� ������ ����� ������ - ������ ������� ���� ����� � ������ � �������!
                            </div>

                            <div class="register">
                                <img alt="����������� � ����" title="����������� � ����" src="/img/t/pict/register.gif" height="69" width="70">
                                <a href="/register" class="NoBorder">
                                    <h4>�����������</h4>
                                </a>
                                <p>�������������������, �� ��������� ������ � ����, ������� � ������������� �������.
                            </div>
                            <div class="newcomers">
                                <h5>����� ������������</h5>
                                <ul class="listed"><?php include $root."inc/ui_parts/newcomers.php"; ?></ul>
                            </div>

                            <div class="forum">
                                <img src="/img/t/pict/forum.gif" height="79" width="95" alt="������" title="������">
                                <a href="/forum/" class="NoBorder"><h4>�����</h4></a>
                                <p>� ������� ���������� ���������� ���� �������� ����, ����������� ���������� � ������������. ������������� ��� ���������� ���������.
                            </div>

                            <div class="gallery">
                                <img src="/img/t/pict/photo.gif" height="66" width="70" alt="�����������" title="�����������">
                                <a href="/gallery/" class="NoBorder"><h4>�����������</h4></a>
                                <p>�� �������� �� ������ � ���������! � ���� ���������� ���� ��������, ���������, ������� �� �������� ������, ��� �� ������ ����������� ��� ������. <a href="/gallery/" class="Link">����������</a> ���������� � ���� �������!
                            </div>
                            <h5>��������� �����������</h5>
                            <?php 
                                $shownComments = 1;
                                include $root."inc/ui_parts/gallery.comments.php"; 
                            ?>

                            <div class="blogs">
                                <img src="/img/t/pict/journals.gif" width="70" height="64" alt="������� (�����)" title="������� (�����)">
                                <a href="/journal/" class="NoBorder">
                                    <h4>������� (�����)</h4></a>
                                <p>����� �� �������� ���������� �������� ���� �������� ������ ������������ ������ (��������). ������ ������� ���������� � �������������� ��� ����� ���.
                            </div>
                            <h5>������ ������</h5>
                            <?php include $root."inc/ui_parts/journal.posts.php"; ?>
                        </div>
                    </div>

                    <div class="Column Right">
                        <img src="/img/t/logo.gif" height="184" width="187" alt="������ ����������� ���" title="������ ����������� ���" >
                        <img alt="����������� ���" title="����������� ���" src="/img/t/title.gif" height="126" width="187">

                        <form method="POST" class="auth_form">
                            <input type="hidden" name="AUTH" id="AUTH" value="1" />
                            <label for="<?php echo LOGIN_KEY ?>">�����</label>
                            <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" type="text" placeholder="�����" />
                            <label for="<?php echo PASSWORD_KEY ?>">������</label>
                            <input name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" value="" size="10" type="password" placeholder="������" />
                            <input src="/img/t/auth.gif" width="90" height="30" alt="�������������� � ����" type="image" />
                        </form>
<?php

    if (!$user->IsEmpty()) {
        $user->User->TouchSession();
        $user->User->Save();

        echo "<div class='authenticated'>�� ������������ ���<br>
            <b>".$user->User->Login."</b><br>
            � ������ <a href='/inside.php'>�����</a> � ���.</div>";
    } else {
        if ($_POST[LOGIN_KEY]) {
            echo "<div class='Error'>������ �����������!</div>";
        }
    }

?>
                        <div class="main_menu">
                            <nav>
                                <ul class="random" role="navigation sitemap">
                                    <li> <a href="/forgot/">����� ������?</a>

                                    <li> <a href="/register">�����������</a>
                                    <li> <a href="/journal/">�������</a>
                                    <li> <a href="/tree/">��������� �����</a>
                                    <li> <a href="/gallery/">�����������</a>
                                    <li> <a href="/forum/">������ ����</a>
                                    <li> <a href="/photos/">����� ������</a>
                                    <li> <a href="/banned/">&laquo;׸���� ������&raquo;</a>
                                    <li> <a href="/rating/">�������</a>
                                </ul>
                            <nav>
                        </div>

                        <div class="counters">
                            <img src="http://s.pr-cy.ru/counters/bezumnoe.ru" alt="������� PR-CY.Rank" style="margin:10px 0;"><br />
                            <!--LiveInternet counter--><script type="text/javascript">document.write("<a href='http://www.liveinternet.ru/click' target=_blank><img src='//counter.yadro.ru/hit?t14.5;r" + escape(document.referrer) + ((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ";u" + escape(document.URL) +";i" + escape("��"+document.title.substring(0,80)) + ";" + Math.random() + "' border=0 width=88 height=31 alt='' title=''><\/a>")</script><!--/LiveInternet-->
                        </div>
                    </div>
                </div>

                <div style="clear: both;" class="Divider Horizontal"><span></span></div>

                <?php include $root."inc/ui_parts/rle_banner.php"; ?>

                <div class="Divider Horizontal Alternative"><span></span></div>
                
                <div class="footer">
                    <p>������ ������ ����� ���� �������� 19 ������� 1999 ����
                    <p>&copy; ������ � ���������� ����� - <a href="mailto:me@bezumnoe.ru">������� ��������</a>
                </div>
            </div>
        </div>
    </div>

<?php

    include $root."/inc/li_spider_check.inc.php";
    include $root."/inc/ui_parts/yandex_metrics.php";

?>
<!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
</body>
</html>