<?php 
class RoomUser extends EntityBase {
    // Constants
    const table = "room_users";

    const USER_ID = "USER_ID";
    const ROOM_ID = "ROOM_ID";

    // Properties
    var $UserId;
    var $RoomId;

    function RoomUser($userId = -1, $roomId = -1) {
        $this->table = self::table;
        $this->Clear();

        $this->UserId = $userId;
        $this->RoomId = $roomId;
    }

    function Clear() {
        $this->UserId = -1;
        $this->RoomId = -1;
    }

    function IsFull() {
        return $this->UserId > 0 && $this->RoomId > 0;
    }

    function FillFromResult($result) {
        $this->UserId = $result->Get(self::USER_ID);
        $this->RoomId = $result->Get(self::ROOM_ID);
    }

    function GetByUserId($userId) {
        return $this->FillByCondition("t1.".self::USER_ID."=".SqlQuote($userId));
    }

    function GetForOnlineUsers($user_id) {
        return $this->GetByCondition("t2.".User::ROOM_ID."<>-1", $this->ReadForOnlineUsersExpression($user_id));
    }

    function Save($by_query = "") {
     global $db;
        if ($this->IsConnected() && $this->IsFull()) {
            // Check duplicates
            $q = $db->Query("SELECT
   ".self::USER_ID."
FROM
  ".$this->table."
WHERE
  ".self::USER_ID." = '".SqlQuote($this->UserId)."' AND
  ".self::ROOM_ID." = '".SqlQuote($this->RoomId)."' LIMIT 1");

            if ($q->NumRows()) {
                return false;
            }

            $q = $db->Query($this->CreateExpression());
            $this->Id = $q->GetLastId();
            return true;
        }
    }

    function Delete() {
        if ($this->IsFull()) {
            $this->GetByCondition("", $this->DeleteExpression());
            $this->Clear();
            return true;
        }
        return false;
    }

    function DeleteForRoom($room_id) {
        return $this->GetByCondition("", $this->DeleteForRoomExpression($room_id));
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::ROOM_ID.": ".$this->RoomId."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>RoomUser is not saved!</b>";
        }
        $s.= "</ul>";
        return $s;
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::USER_ID.",
    t1.".self::ROOM_ID."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::USER_ID.",
".self::ROOM_ID."
)
VALUES
('".SqlQuote($this->UserId)."',
'".SqlQuote($this->RoomId)."'
)";
    }

    function UpdateExpression() {
        return "";
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::USER_ID."=".SqlQuote($this->UserId)." AND
    ".self::ROOM_ID."=".SqlQuote($this->RoomId);
    }

    function DeleteForRoomExpression($room_id) {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::ROOM_ID."=".SqlQuote($room_id);
    }
}
?>
