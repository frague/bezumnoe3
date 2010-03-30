<?php
	
	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";
	require $root."inc/base_template.php";

	Head("Рейтинг", "rating.css");
	require_once $root."references.php";


	function GetRatings($condition) {
		$p = new Profile();
		$u = new User();

		$q = $p->GetByCondition(
			$condition,
			$p->RatingExpression());
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$p->FillFromResult($q);
			$u->FillFromResult($q);

			echo MakeListItem().$u->ToInfoLink()." &rarr; <b>".$p->Rating."</b> <sup>".$p->GetRatingDelta()."</sup>";
		}
	}

?>

Обновление рейтинга производится раз в сутки.

<div align="center">
<table width="90%" class="UserList">
	<tr>
		<td valign="top" width="50%">
			<ol>
				<h4>Лучшие за сутки:</h4>
				<?php GetRatings("1=1 ORDER BY ".Profile::RATING."-".Profile::LAST_RATING." DESC LIMIT 20"); ?>
			</ul>
		</td><td valign="top">
			<ol>
				<h4>Топ 20:</h4>
				<?php GetRatings("1=1 ORDER BY ".Profile::RATING." DESC LIMIT 20"); ?>
			</ol>
		</td>
	</tr>
</table>
</div>

<?php


	Foot();
?>