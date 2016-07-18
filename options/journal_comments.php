<?php
    require "menu_base.php";

?>

<h2 id="FORUM"></h2>
<h3>&laquo;<span id="TITLE"></span>&raquo;</h3>

<!--h4>Поиск:</h4>
<table class="Wide">
    <tr>
        <td width="100%">
            <input name="SEARCH" id="SEARCH" class="Wide" />
        </td><td>
            <input type="image" src="/img/search_button.gif" onclick="SwitchPage(this)" id="buttonSearch" />
        </td></tr></table-->

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="CommentsGrid" class="Grid"><tbody><tr><th style="width:99%">Комментарии</th><th>Операции</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">Обновить данные</a>
</ul>
