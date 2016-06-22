var db = require('orm').db,
  Forum = db.models.forums;

exports.index = function(req, res){
  Forum.find(function(err, articles){
    if(err) throw new Error(err);
    res.render('index/index', {
      title: 'Generator-Express MVC',
      articles: articles
    });
  });
};
