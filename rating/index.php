<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";
	require $root."inc/base_template.php";

	Head("Рейтинг", "rating.css");
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

			echo MakeListItem($i < 10 ? "Leading" : "").$u->ToInfoLink()." &rarr; <b>".$p->Rating."</b> <sup>".$p->GetRatingDelta()."</sup>";
		}
	}

	function GetJournalsRating($condition) {
		$j = new Journal();
		$u = new User();

		$q = $j->GetByCondition(
			$condition,
			$j->RatingExpression(" ORDER BY ".ForumBase::RATING."-".ForumBase::LAST_RATING." DESC LIMIT 20"));

		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$j->FillFromResult($q);
			$alias = $q->Get(JournalSettings::ALIAS);

//			echo MakeListItem($i < 10 ? "Leading" : "")." &laquo;".JournalSettings::MakeLink($alias, $j->Title)."&raquo; &rarr; <b>".$j->Rating."</b> <sup>".$j->GetRatingDelta()."</sup>";
			echo MakeListItem($i < 10 ? "Leading" : "")." &laquo;".JournalSettings::MakeLink($alias, $j->Title)."&raquo;";
		}
	}

?>

Обновление рейтинга производится раз в сутки.

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

	Foot();
?>