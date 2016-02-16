var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var runSequence = require('gulp-sequence');
var bower = require('gulp-bower');
var sass = require('gulp-sass');
var rev = require('gulp-rev');
var del = require('del');
var inject = require('gulp-inject');

function callback(error) {
    if (error) console.error('Error:', error);
};

gulp.task('fetch', bower);

gulp.task('styles:sass', function () {
    return gulp.src('sass/*.sass')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(concat('styles.css'))
        .pipe(rev())
        .pipe(gulp.dest('css'));
});

gulp.task('styles:vendor', function () {
    return gulp
        .src([
            'bower_components/font-awesome/css/font-awesome.css'
        ])
        .pipe(concat('vendor.css'))
        .pipe(rev())
        .pipe(gulp.dest('css'));
});

gulp.task('styles:watch', function () {
    gulp.watch('./sass/*.sass', [
        'styles:sass',
        'inject'
    ]);
});

gulp.task('styles', function () {
    return runSequence('styles:vendor', 'styles:sass', callback);
});

gulp.task('scripts:vendor', function () {
    return gulp.src([
            'bower_components/jquery/dist/jquery.js',
            'bower_components/jquery-ui/jquery-ui.js',
            'bower_components/jquery-fittext.js/jquery.fittext.js',
            'bower_components/letteringjs/jquery.lettering.js',
            'bower_components/lodash/lodash.js'
        ])
        .pipe(uglify())
        .pipe(concat('vendor.js'))
        .pipe(rev())
        .pipe(gulp.dest('scripts'));
});

gulp.task('scripts:custom',  function () {
    return gulp.src(['js/*.js', 'js1/*.js'])
    //        .pipe(uglify())
        .pipe(concat('scripts.js'))
        .pipe(rev())
        .pipe(gulp.dest('scripts'));
});

gulp.task('scripts', function () {
    return runSequence('scripts:vendor', 'scripts:custom', callback);
});

gulp.task('cleanup', function () {
    return del(['scripts/**/*', 'css/styles-*.css']);
});

gulp.task('inject', function () {
    return gulp.src([
        'index.php'
    ])
        .pipe(inject(gulp.src([
            'css/styles-*.css',
            'css/vendor-*.css',
            'scripts/**/*'
        ])))
        .pipe(gulp.dest('.'));;
});

gulp.task('build', function () {
    return runSequence(
//        'cleanup',
        'scripts',
        'styles',
        'inject',
        callback
    )
});

gulp.task('bower', function () {
    return runSequence(
        'fetch',
        'scripts',
        callback
    );
});
