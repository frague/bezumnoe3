<?php
    require "menu_base.php";

    if ($user->IsEmpty()) {
        exit();
    }

?>
<h4>Заданные теги:</h4>
<div id="TagsContainer">не указаны</div>

<h4>Добавить тег (метку):</h4>
<ul id="Errors"></ul>
<table>
    <tr>
        <td>
            <input name="SEARCH_TAG" id="SEARCH_TAG" maxlength="100" size="40" />
            <div class="Options">
                <ul id="FoundTags"></ul>
            </div>
        </td><td valign="top">
            <input type="image" src="/img/icons/add_to.gif" id="AddTag" onclick="if (!this.obj.Tab.Validators.AreValid()) {return false;};this.obj.AddNewTag(this)" /></td></tr></table>
