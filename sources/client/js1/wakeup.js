var replyFormHeight = 75;
var offset = 0;

function ReplyForm() {
    $('#WakeupReply').toggle(!offset);
    offset = replyFormHeight - offset;
    _.result(window, 'onResize');
    $('#reply').focus();
}

function SendWakeup(message_id) {
    var s = new ParamsBuiler();
    s.add('message', $('#reply').val());
    s.add('reply_to', message_id);
    $.post('../services/wakeup.service.php', s.build)
        .done(MessageAdded);

    $('#status')
        .removeClass().addClass('RoundedCorners')
        .css('background-color', '#404040')
        .html('Отправка сообщения...');
    $('#reply').val('');
}

function MessageAdded(responseText) {
    if (responseText) {
        $('#status')
            .css('background-color', responseText.charAt(0) == '-' ? '#983418' : '#728000')
            .html(responseText.substring(1));
    }
    setTimeout(self.close(), 2000);
}
