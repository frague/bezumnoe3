<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<table cellpadding="2" cellspacing="0" id="OpenIdsGrid" class="Grid"><tbody><tr><th>Провайдер</th><th style="width:100%">Логин</th><th>Операции</th></tr></tbody></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddOpenId(this)" class="Add" id="AddOpenId">Новый OpenID</a>
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshOpenIds">Обновить список</a>
</ul>