'use strict';

var express = require('express');
var orm = require('orm');
var fs = require('fs');
var config = require('./config/config');

orm.db = orm.connect(config.db, function (err, db) {
  if (err) {
    console.error("Failed to connect to db", err);
    return;
  }
});

var modelsPath = __dirname + '/models';
fs.readdirSync(modelsPath).forEach(function (file) {
  if (file.indexOf('.js') >= 0) {
    require(modelsPath + '/' + file);
  }
});

var app = express();

require('./config/express')(app, config);
require('./config/routes')(app);

app.listen(config.port);