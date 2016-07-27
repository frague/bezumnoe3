//2.0

function sendRequest(url, callback, postData, obj) {
  $.ajax({
    url: url,
    data: postData,
    type: postData ? "POST" : "GET"
  })
  .then(
    function(data) {
      if (callback) {
        // Callback passed as parameter
        return callback(data, obj);
      }
      if (obj && obj.TemplateLoaded) {
        // Template render callback
        return obj.TemplateLoaded(data);
      }
    }
  );
};

function handleRequest(responseText) {
  try {
    console.log('Evaluation!', responseText);
    eval(responseText);
    return;
  } catch(e) {
    return;
  }
};