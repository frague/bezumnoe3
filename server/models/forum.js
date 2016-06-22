'use strict';

// Example model

var db = require('orm').db;

var Forum = db.define('forums', {
  FORUM_ID: 'number',
  TYPE: String,
  TITLE: String
}, {
  id: 'FORUM_ID',
  methods: {
    example: function example() {
      // return example;
    }
  }
});