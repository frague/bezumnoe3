<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";
	require $root."inc/base_template.php";

	$pg = new Page("Рейтинг");
	$pg->AddCss("rating.css");
	$pg->PrintHeader();

	require_once $root."references.php";


	function GetUsersRatings($condition) {
		$p = new Profile();
		$u = new User();

		$q = $p->GetByCondition(
			$condition,
			$p->RatingExpression());
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$p->FillFromResult($q);
			$u->FillFromResult($q);

			$delta = $p->GetRatingDelta();
			echo MakeListItem($i < 10 ? "Leading" : "").$u->ToInfoLink()." &rarr; <b>".$p->Rating."</b> <sup class=\"".($delta > 0 ? "Positive" : "Negative")."\">".$delta."</sup>";
		}
	}

?>

<p>Обновление рейтинга производится раз в сутки.</p>

<div align="center">
<table width="90%">
	<tr>
		<td valign="top" width="50%">
			<ol>
				<h3>Лучшие за сутки</h3>
				<?php GetUsersRatings("1=1 ORDER BY ".Profile::RATING."-".Profile::LAST_RATING." DESC LIMIT 40"); ?>
			</ol>
		</td><td valign="top">
			<ol>
				<h3>40 лучших</h3>
				<?php GetUsersRatings("1=1 ORDER BY ".Profile::RATING." DESC LIMIT 40"); ?>
			</ol>
		</td>
	</tr>
</table>
</div>

<?php

	$pg->PrintFooter();
?>