<?
    require "menu_base.php";

?>

<h4>Внешний вид журнала (скин):</h4>
<div id="SKIN_TEMPLATE_ID" name="SKIN_TEMPLATE_ID" class="Radios"><?

        $skin = new JournalSkin();
        echo $skin->ToHtml("/img/journals", "doShow('templates')");

        $q = $skin->GetByCondition("");
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $skin->FillFromResult($q);
            echo $skin->ToHtml("/img/journals", "doHide('templates')");
        }

?></div>

<div id="templates">
    <h2>Собственный шаблон</h2>

    <h4>Шаблон заголовка:</h4>
    <input type="text" id="TITLE" name="TITLE" class="Wide" />

    <h4>Основной шаблон разметки журнала:</h4>
    &lt;body&gt;
    <textarea id="BODY" name="BODY" onclick="Maximize(this)"></textarea>
    &lt;/body&gt;

    <h4>Шаблон отдельного сообщения:</h4>
    <textarea id="MESSAGE" name="MESSAGE" onclick="Maximize(this)"></textarea>

    <h4>Стили отображения (CSS):</h4>
    &lt;style&gt;
    <textarea id="CSS" name="CSS" onclick="Maximize(this)"></textarea>
    &lt;/style&gt;
</div>

<div id="templates1"></div>

<br />
