<?php

class Query {

    var $Q;

    var $Queried = false;
    var $Record;
    var $RecordIndex = 0;

    var $Debugging = false;         // Debugging mode

    var $DB_stored = 0;

    function Query($query, $DB = 0) {
        if (!$DB && !$this->DB_stored) {
            error("Нет соединения с MySQL сервером!");
            return false;
        } else {
            if ($DB) {
                $this->DB_stored = $DB;
            }
        }

        if (!$DB) {
            $DB = $this->DB_stored;
        }

        $this->Q = $DB->query($query);

        if ($this->Debugging) {
            error("Query: <strong>".$query."</strong>");
        }

        if ($this->Q) {
            $this->Queried = true;
        } else if ($this->Debugging) {
            error("Ошибка в запросе: <strong>".$query."</strong>: <span class='Red'>".mysql_error()."</span>!");
        }
    }

    function NumRows() {
        if (!$this->Queried) {
            error("Не определены результаты запроса!");
            return false;
        }
        return $this->Q->num_rows;
    }

    function Seek($row = 0) {
        if (!$this->Queried) {
            error("Не определены результаты запроса!");
            return false;
        }

        $this->Q->data_seek($row);
        $this->RecordIndex = $row;
    }

    function NextResult() {
        if (!$this->Queried) {
            error("Не определены результаты запроса!");
            return false;
        }

        $this->Record = $this->Q->fetch_array();
        $this->RecordIndex++;
        if ($this->RecordIndex - 1 >= $this->NumRows() || !$this->NumRows()) {
            $this->Seek(0);
            return false;
        } else {
            return true;
        }
    }

    function Get($name) {
        return $this->Record[$name];
    }

    function GetNullableId($name) {
        $val = round($this->Get($name));
        return $val > 0 ? $val : -1;
    }

    function GetNullableIdExt($name) {
        $val = round($this->Get($name));
        return $val != 0 ? $val : -1;
    }

    function GetLastId() {
        return $this->DB_stored->insert_id;
    }

    function AffectedRows() {
        return $this->DB_stored->affected_rows;
    }

    function Release() {
        if (!$this->Queried) {
            error("Не определены результаты запроса!");
            return false;
        }
        return $this->Q->free();
    }
}




class SQL {
    var $DB;

    var $DB_name;
    var $Host;
    var $User;
    var $UserPassword;

    var $Connected=0;

    var $Errors=array();
    var $ErrorsIndex = 0;


    function SQL($db_name, $host = "", $user, $pass) {
        $this->DB_name=$db_name;
        $this->Host=$host;
        $this->User=$user;
        $this->UserPassword=$pass;

        $this->DB = new mysqli($this->Host, $this->User, $this->UserPassword, $this->DB_name);
        if ($this->DB->connect_errno) {
            error("Ошибка подключения к MySQL серверу ".$this->Host." as ".$this->User.": ".$this->DB->connect_error);
        }

        $this->DB->query("set names utf8");
        $this->Connected = 1;
    }


    function Query($query) {
        if (!$query) return 0;
//      if (!$this->Connected) return 0;

        $a = new Query($query, $this->DB);
        return $a;
    }
}


?>
