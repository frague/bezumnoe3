<?php     require "menu_base.php";

    if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
        exit();
    }

?>

<table cellpadding="2" cellspacing="0" id="NewsGrid" class="Grid"><tbody><tr><th style="width:100%">Название</th><th>Операции</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="AddNews(this)" class="Add" id="AddNews">Новый новостной раздел</a>
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshNews">Обновить список разделов</a>
</ul>

<div id="NewsItems"></div>
