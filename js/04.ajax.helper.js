//2.0

function sendRequest(url, callback, postData, obj) {
    $.ajax({
        url: url,
        data: postData,
        type: postData ? "POST" : "GET"
    })
    .done(function(data) {
        if (callback) {
            // Callback passed as parameter
            callback(data, obj);
        } else if (obj && obj.TemplateLoaded) {
            // Template render callback
            obj.TemplateLoaded(data);
        }
    });
    return;
};

function handleRequest(responseText) {
    try {
        eval(responseText);
        return;
    } catch(e) {
        return;
    }
    eval(responseText);
};
