var path = require('path');
var rootPath = path.normalize(__dirname + '/..');
var env = process.env.NODE_ENV || 'development';

var config = {
  development: {
    root: rootPath,
    app: {
      name: 'express'
    },
    port: 3000,
    db: 'mysql://root:root@localhost/bezumnoe_bezumnoe'
  },

  test: {
    root: rootPath,
    app: {
      name: 'express'
    },
    port: 3000,
    db: 'mysql://root@localhost/express_test'
  },

  production: {
    root: rootPath,
    app: {
      name: 'express'
    },
    port: 3000,
    db: 'mysql://root@localhost/express_production'
  }
};

console.log(config[env]);
module.exports = config[env];
