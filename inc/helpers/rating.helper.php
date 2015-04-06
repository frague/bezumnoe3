<?

    function SaveRating($id, $rating) {
        $rating = new Rating($id, $rating);
        $rating->Save();
    }

    function SaveRatingWithIp($id, $rating, $type = Rating::TYPE_PROFILE) {
        $rating = new Rating($id, $rating);
        $rating->Type = $type;
        $na = new NetAddress();
        $rating->Ip = $na->ShownIp;
        $rating->Save();
    }



    // User banned
    function BanRating($id) {
        SaveRating($id, -100);
    };

    // User kicked
    function KickRating($id) {
        SaveRating($id, -50);
    };

    // New topic in forum/journal/gallery
    function TopicRating($id) {
        SaveRating($id, 5);
    };

    // New comment in forum/journal/gallery
    function CommentRating($id) {
        SaveRating($id, 2);
    };

    // Photo changing
    function PhotoRating($id) {
        SaveRating($id, 5);
    };

    // Info rating
    function InfoRating($id) {
        SaveRatingWithIp($id, 1);
    };

    // Journal rating
    function JournalRating($id) {
        // TODO: Use JournalId instead of User's
        SaveRatingWithIp($id, 1, Rating::TYPE_JOURNAL);
    };

?>