<div id="Hider">
    <div id="ReplyForm" class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="position: static; font-size: inherit;">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
            <span id="ui-dialog-title-dialog" class="ui-dialog-title">����� �����������</span>
        </div>
        <div  class="ui-dialog-content ui-widget-content">
<?php

    if (!IsPostingAllowed()) {

?>
                <span class="Warning">��� ������ ������ � ���������� ���������.</span>

<?php

        } else {
?>
                <div id="ERROR"></div>
        
                <div>
                    ������������: <strong id="LoggedLogin"><?php echo $user->User->Login ?></strong>
                </div>

                <div>
                    <label for="TITLE">���������</label>
                    <input id="TITLE" name="TITLE" class="Wide" size="30" tabindex="3" />
                </div>

                <div>
                    <label for="CONTENT" class="Mandatory">����� ���������</label>
                    <textarea id="CONTENT" name="CONTENT" class="Wide" cols="30" rows="8" tabindex="4"></textarea>
                </div>

                <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
                    <button onclick="AddMessage(this)" id="SubmitMessageButton" tabindex="5">���������</button>
                    <button onclick="CancelReply()">��������</button>
                    <button onclick="MakeCite()" id="buttonCite">������</button>

                    <input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> ������� ���������
                </div><?php } ?>
        </div>
    </div>
</div>

<style>
    #auth_form {
        display:none
    }
</style>

<div id="auth_form">
    <form method="POST" action="/auth/" name="auth" id="auth">
        <table>
            <tr>
                <td>
                    <input type="radio" name="AUTH" id="AUTH_NOW" value="1" checked /> 
                    <label for="AUTH_NOW">�� ������ � ������</label>
                    <div id="AuthByLogin">
                        <div>
                            <label for="<?php echo LOGIN_KEY ?>">�����</label>
                            <input class="submitter" name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1000" />
                        </div>
                        <div>
                            <label for="<?php echo PASSWORD_KEY ?>">������</label>
                            <input class="submitter" type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="1001" />
                        </div>
                    </div></td>
                <td>
                    <input type="radio" name="AUTH" id="AUTH_OPENID" value="2" /> 
                    <label for="AUTH_OPENID">�� OpenID</label>
                    <div id="AuthByOpenID">
                        <input type="hidden" name="openid_action" value="login" />
                        <input type="hidden" name="<?php echo REFERER_KEY ?>" id="<?php echo REFERER_KEY ?>" />
                        <div>
                            <label for="<?php echo OPENID_LOGIN_KEY ?>">�����</label>
                            <input class="submitter" name="<?php echo OPENID_LOGIN_KEY ?>" id="<?php echo OPENID_LOGIN_KEY ?>" size="20" />
                        </div>
                        <div>
                            ������ <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" /><br />
                            <input type="hidden" id="callback" name="callback" />
                            <span class="OpenID">
<?php

    $op = new OpenIdProvider();
    $q = $op->GetAll();
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $op->FIllFromResult($q);
        echo $op->ToPrint($i, OPENID_KEY);
    }
?>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>