<?php
	
	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("Фотки чатлан", "photos.css");
	require_once $root."references.php";

	$search = LookInRequest("search");


	$u = new User();
	$p = new Profile();
	$q = $p->GetPhotos($search);

	$result = "";
	$photos = "";

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$p->FillFromResult($q);
		$login = $q->Get(User::LOGIN);

		$link = $u->InfoLink($p->UserId, $login);

		$photo = ProfilePhoto($p);
		if ($photo) {
			$photos .= $photo."<div>".$link."</div>";
			$link = "<b>".$link."</b>";
		}
		$result .= MakeListItem()." ".$link;
	}

?>

<table width="100%">
	<tr>
		<td width="30%" valign="top" class='UserList'>
			<form>
			<h4>Поиск:</h4>
			<input name="search" id="search" value="<?php echo $search; ?>" maxlength="20" /> <input type="submit" value="!" />

			<h4>По первому символу:</h4>

			<h4>Результаты поиска:</h4>
			<ul>
				<?php echo $result ?>
			</ul>
			</form>
		</td>
		<td valign="top" class="Centered">
			<div class="Divider Vertical">
				<?php echo $photos; ?>
			</div>
		</td>
	</tr>
</table>

<?php


	Foot();
?>