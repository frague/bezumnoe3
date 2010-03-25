<?

	function SaveRating($userId, $rating) {
		$rating = new Rating($userId, $rating);
		$rating->Save();
	}



	// User banned
	function BanRating($userId) {
		SaveRating($userId, -100);
	};

	// User kicked
	function KickRating($userId) {
		SaveRating($userId, -50);
	};

	// New topic in forum/journal/gallery
	function TopicRating($userId) {
		SaveRating($userId, 5);
	};

	// New comment in forum/journal/gallery
	function CommentRating($userId) {
		SaveRating($userId, 2);
	};

	// Photo changing
	function PhotoRating($userId) {
		SaveRating($userId, 5);
	};

?>