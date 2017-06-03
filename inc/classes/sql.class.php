<?php

class Query {

    var $Q;

    var $hasQueryResult = FALSE;
    var $Record;
    var $RecordIndex = 0;

    var $Debugging = FALSE;         // Debugging mode

    var $link = 0;
    
    function Query($query, $link = 0) {
        if (!$link && !$this->link) {
            error("Нет соединения с MySQL сервером!");
            return;
        } else {
            if ($link) {
                $this->link = $link;
            }
        }
        
        $this->queryResult = mysqli_query($this->link, $query);

        if ($this->Debugging) {
            error("Query: <strong>".$query."</strong>");
        }

        if ($this->queryResult !== FALSE) {
            $this->hasQueryResult = TRUE;
        } else if ($this->Debugging) {
            error("Ошибка в запросе: <strong>".$query."</strong>: <span class='Red'>".mysqli_errno($this->link)."</span>!");
        }
    }

    function NumRows() {
        if (!$this->hasQueryResult) {
            error("Не определены результаты запроса1!");
            return 0;
        }
        return mysqli_num_rows($this->queryResult);
    }

    function Seek($row = 0) {
        if (!$this->hasQueryResult) {
            error("Не определены результаты запроса2!");
            return;
        }
        
        mysqli_data_seek($this->queryResult, $row);
        $this->RecordIndex = $row;
    }

    function NextResult() {
        if (!$this->hasQueryResult) {
            error("Не определены результаты запроса3!");
            return;
        }
        
        $this->Record = mysqli_fetch_array($this->queryResult);
        $this->RecordIndex++;
        if ($this->RecordIndex - 1 >= $this->NumRows() || !$this->NumRows()) {
            $this->Seek(0);
            return FALSE;
        }
        return TRUE;
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
        return mysqli_insert_id($this->link);
    }

    function AffectedRows() {
        return mysqli_affected_rows($this->link);
    }

    function Release() {
        if (!$this->hasQueryResult) {
            error("Не определены результаты запроса!");
            return FALSE;
        }
        return mysqli_free_result($this->queryResult);
    }
}

class SQL {
    var $link;
    var $Connected = FALSE;

    function SQL($dbName, $host, $user, $password) {
        $this->link = mysqli_connect($host, $user, $password);
        if (!$this->link) {
            error("Ошибка подключения к MySQL серверу!");
        }

        mysqli_query($this->link, "set SESSION sql_mode='TRADITIONAL'");
        mysqli_query($this->link, "set names utf8");
        mysqli_set_charset($this->link, "utf8");

        if (mysqli_select_db($this->link, $dbName)) {
            $this->Connected = TRUE;
        } else {
            error("Невозможно выбрать базу данных <b>$db_name</b>!");
        }
    }

    function Query($query) {
        if (!$query || !$this->Connected) return 0;
        return new Query($query, $this->link);
    }
}

?>