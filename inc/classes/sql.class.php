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

        $this->Q=@mysqli_query($DB, $query);

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
            error("Не определены результаты запроса1!");
            return false;
        }
        return @mysqli_num_rows($this->Q);
    }

    function Seek($row = 0) {
        if (!$this->Queried) {
            error("Не определены результаты запроса2!");
            return false;
        }
        
        mysqli_data_seek($this->Q, $row);
        $this->RecordIndex = $row;
    }

    function NextResult() {
        if (!$this->Queried) {
            error("Не определены результаты запроса3!");
            return false;
        }
        
        $this->Record = @mysqli_fetch_array($this->Q);
        $this->RecordIndex++;
        if ($this->RecordIndex - 1 >= $this->NumRows() || !$this->NumRows()) {
            $this->Seek(0);
            return false;
        } else {
            return true;
        }
    }

    function Get($name) {
        if (array_key_exists($name, $this->Record)) return $this->Record[$name];
        return "";
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
        return @mysqli_insert_id($this->DB_stored);
    }

    function AffectedRows() {
        return @mysqli_affected_rows($this->DB_stored);
    }

    function Release() {
        if (!$this->Queried) {
            error("Не определены результаты запроса!");
            return false;
        }
        // return @mysqli_free_result($this->Q);
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
    var $ErrorsIndex=0;


    function SQL($db_name,$host="",$user,$pass) {
        $this->DB_name=$db_name;
        $this->Host=$host;
        $this->User=$user;
        $this->UserPassword=$pass;

        $this->DB = @mysqli_connect($this->Host, $this->User, $this->UserPassword);
        if (!$this->DB) {
            error("Ошибка подключения к MySQL серверу!");
        }

        @mysqli_query($this->DB, "set names utf8");
        @mysqli_set_charset($this->DB, "utf8");


        if (@mysqli_select_db($this->DB, $this->DB_name)) {
            $this->Connected=1;
        } else {
            error("Невозможно выбрать базу данных <b>$db_name</b>!");
        }
    }


    function Query($query) {
        if (!$query) return 0;
//      if (!$this->Connected) return 0;

        $a = new Query($query, $this->DB);
        return $a;
    }
}


?>