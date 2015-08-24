var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var runSequence = require('gulp-sequence');
var bower = require('gulp-bower2');

gulp.task('fetch', bower);

gulp.task('scripts',  function() {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/jquery-ui/jquery-ui.js',
        'bower_components/jquery-fittext.js/jquery.fittext.js',
        'bower_components/letteringjs/jquery.lettering.js',
        'bower_components/lodash/lodash.js',
        'js/*.js',
        'js1/*.js'
    ])
//        .pipe(uglify())
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest('scripts'));
});

gulp.task('bower', function() {
    runSequence(
        'fetch',
        'scripts',
        function (error) {
            console.log('Done:', error);
        }
    );
});
