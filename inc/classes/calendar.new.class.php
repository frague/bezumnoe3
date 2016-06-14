<?php
require_once "basic.class.php";

class Calendar extends Basic {
    var $Days = array();

    var $Month;
    var $Year;

    var $MonthDays;
    var $FirstDay;

    var $ShowMonthName = 1;
    var $ShowDayNames = 1;

    var $Months = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
    var $DayNames = array("","Пн.","Вт.","Ср.","Чт.","Пт.","Сб.","Вс.");

    var $Out = "";

    var $PrevMonth, $NextMonth;


    function Calendar($m = 0, $y = 0) {

        $this->Month = $m ? $m : date("n");
        $this->Year = $y ? $y : date("Y");

        $dat = strtotime($this->Year."-".$this->Month."-01");

        $this->MonthDays = date("t", $dat);
        $this->FirstDay = date("w", $dat) - 1;
        if ($this->FirstDay < 0) {
            $this->FirstDay += 7;
        }

        for ($i = 1; $i <= $this->MonthDays; $i++) {
            $this->Days[$i] = $i;
        }
    }

    function CalendarPrint($do_print = 1) {
        $this->Out = "";

        $this->Out .= "<table class='Calendar'>\n";
        if ($this->ShowMonthName) {
            $this->Out .= "<tr class='Header'><th colspan=7>".$this->Months[round($this->Month)].", ".$this->Year."</th></tr>\n";
        }
        if ($this->ShowDayNames) {
            $perc = sprintf("%.02f", 100 / 7);

            $this->Out .= "<tr class='DayNames'>";
            for ($i = 1; $i <= 7; $i++) {
                $this->Out.="<td width='".$perc."%'".($i==6 || $i==7 ? " class='Weekend'" : "").">".$this->DayNames[$i]."</td>";
            }
            $this->Out .= "</tr>\n";

            $flag = 2;
            $day = 1;
            while ($flag) {
                $this->Out .= "<tr class='Week'>\n  ";
                for ($i = 0; $i < 7; $i++) {
                    $this->Out .= "<td".($i==5 || $i==6 ? " class='Weekend'" : "").">";

                    if ($flag == 2 && $i == $this->FirstDay) {
                        $flag--;
                    }
                    if ($flag == 1) {
                        if ($day <= $this->MonthDays) {
                            $this->Out .= $this->Days[$day++];
                        } else {
                            $flag--;
                        }
                    }
                    $this->Out .= "</td>";
                }
                $this->Out .= "</tr>\n";
            }
        }
        if ($this->PrevMonth || $this->NextMonth) {
            $this->Out .= "<tr class='Navigation'><td colspan=7>".$this->PrevMonth." | ".$this->NextMonth."</td></tr>";
        }

        $this->Out .= "</table>";
        if ($do_print) {
            echo $this->Out;
        }
    }

    function SetHeader($hdr) {
        $this->Header = $hdr;
    }

    function SetDay($day, $value) {
        $this->Days[$day] = $value;
    }

    function SetPrevNext($prev, $next) {
        $this->PrevMonth = $prev;
        $this->NextMonth = $next;
    }
}

?>
