var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var runSequence = require('gulp-sequence');
var sass = require('gulp-sass');
var rev = require('gulp-rev');
var del = require('del');
var inject = require('gulp-inject');
var babel = require('gulp-babel');
 
function callback(error) {
  if (error) console.error('Error:', error);
};

gulp.task('styles:sass', function () {
  return gulp.src('sass/*.sass')
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(concat('styles.css'))
    .pipe(rev())
    .pipe(gulp.dest('server/static/styles'));
});

gulp.task('styles:vendor', function () {
  return gulp
    .src([
      'bower_components/font-awesome/css/font-awesome.css'
    ])
    .pipe(concat('vendor.css'))
    .pipe(rev())
    .pipe(gulp.dest('server/static/styles'));
});

gulp.task('styles:watch', function () {
  gulp.watch('./sass/*.sass', [
    'styles:sass',
    'inject'
  ]);
});

gulp.task('styles', function (callback) {
  runSequence(['styles:vendor', 'styles:sass'])(callback);
});

gulp.task('scripts:vendor', function () {
  return gulp.src([
      'node_modules/jquery/dist/jquery.js',
      'node_modules/jquery-ui/jquery-ui.js',
      'node_modules/jquery-fittext.js/jquery.fittext.js',
      'node_modules/letteringjs/jquery.lettering.js',
      'node_modules/lodash/lodash.js'
    ])
    .pipe(uglify())
    .pipe(concat('vendor.js'))
    .pipe(rev())
    .pipe(gulp.dest('server/static/scripts'));
});

gulp.task('scripts:custom',  function () {
  return gulp.src(['js/*.js', 'js1/*.js'])
  //  .pipe(uglify())
    .pipe(babel({
      presets: ['es2015']
    }))
    .pipe(concat('custom.js'))
    .pipe(rev())
    .pipe(gulp.dest('server/static/scripts'));
});

gulp.task('scripts', function (callback) {
  runSequence(['scripts:vendor', 'scripts:custom'])(callback);
});

gulp.task('cleanup', function () {
  return del(['server/static/scripts/**/*', 'server/static/styles-*.css']);
});

// gulp.task('inject', ['scripts', 'styles'], function () {
gulp.task('inject', [], function () {
  return gulp.src([
    'server/views/layout.jade'
  ])
    .pipe(inject(gulp.src([
        'server/static/styles/vendor-*.css',
        'server/static/styles/styles-*.css',
        'server/static/scripts/vendor-*.js',
        'server/static/scripts/custom-*.js'
      ]))
    )
    .pipe(gulp.dest('./server/views'));;
});

gulp.task('build', runSequence('cleanup', ['scripts', 'styles'], 'inject'));
