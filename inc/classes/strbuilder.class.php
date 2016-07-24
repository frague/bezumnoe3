<?php

class StrBuilder {
    var $Text;

    function StrBuilder() {
        $this->Clear();
    }

    function Clear() {
        $this->Text = "";
    }

    function Append($line) {
        $this->Text .= $line;
    }
}

?>