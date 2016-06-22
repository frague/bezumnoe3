'use strict';

var express = require('express');

module.exports = function (app, config) {
  app.configure(function () {
    app.use(express.compress());
    app.use(express.static(config.root + '/static'));
    app.set('port', config.port);
    app.set('views', config.root + '/static/views');
    app.set('view engine', 'pug');
    app.use(express.favicon(config.root + '/static/images/favicon.ico'));
    app.use(express.logger('dev'));
    app.use(express.bodyParser());
    app.use(express.methodOverride());
    app.use(app.router);
    app.use(function (req, res) {
      res.status(404).render('404', { title: '404' });
    });
  });
};