var gulp = require('gulp');
var babel = require('gulp-babel');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var runSequence = require('gulp-sequence');
var sass = require('gulp-sass');
var rev = require('gulp-rev');
var exit = require('gulp-exit');
var del = require('del');
var inject = require('gulp-inject');
var gutil = require('gulp-util');

var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var browserify = require('browserify');
var watchify = require('watchify');
var babelify = require('babelify');

// function callback(error) {
//   if (error) console.error('Error:', error);
// };

// gulp.task('styles:sass', function () {
//   return gulp.src('sass/*.sass')
//     .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
//     .pipe(concat('custom.css'))
//     // .pipe(rev())
//     .pipe(gulp.dest('css'));
// });

// gulp.task('styles:vendor', function () {
//   return gulp
//     .src([
//       'node_modules/font-awesome/css/font-awesome.min.css'
//     ])
//     .pipe(concat('vendor.css'))
//     // .pipe(rev())
//     .pipe(gulp.dest('css'));
// });

// gulp.task('styles:watch', function () {
//   gulp.watch('./sass/*.sass', [
//     'styles:sass',
//     'inject'
//   ]);
// });

// gulp.task('styles', function (callback) {
//   runSequence(['styles:vendor', 'styles:sass'])(callback);
// });

// gulp.task('scripts:vendor', function () {
//   return gulp.src([
//       'node_modules/jquery/dist/jquery.min.js',
//       'node_modules/jqueryui/jquery-ui.min.js',
//       'node_modules/fittext.js/jquery.fittext.js',
//       'node_modules/charming/charming.min.js',
//       'node_modules/lodash/lodash.min.js',
//       'node_modules/es2015-mixin/lib/index.js',
//       'node_modules/tinymce/tinymce.min.js'
//     ])
//     .pipe(concat('vendor.js'))
//     // .pipe(rev())
//     .pipe(gulp.dest('scripts'));
// });

gulp.task('scripts:custom',  function () {
  return gulp.src(['js/*.js', 'js1/*.js'])
    .pipe(sourcemaps.init())
    .pipe(babel({
      presets: ['es2015']
    }).on('error', function(error) {
      gutil.log(error.stack);
      this.end();
    }))
    .pipe(concat('custom.js'))
    // .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('sources'));
});

// gulp.task('scripts', function (callback) {
//   runSequence(['scripts:vendor', 'scripts:custom'])(callback);
// });

gulp.task('cleanup', function () {
  return del(['scripts/**/*.js', 'scripts/**/*.js.map', 'css/custom*.css', 'css/vendor*.css']);
});

// gulp.task('inject', function () {
//   return gulp.src([
//     '**/*.php'
//   ])
//     .pipe(inject(gulp.src([
//       'css/vendor*.css',
//       'css/custom*.css',
//       'scripts/vendor*.js',
//       'scripts/custom*.js'
//     ])))
//     .pipe(gulp.dest('.'));;
// });

// gulp.task('watch:scripts', function () {
//   gulp.watch(['js/*', 'js1/*'], ['scripts:custom']);
// });

// gulp.task('build', runSequence('cleanup', ['scripts', 'styles'], 'inject'));



function compile(watch) {
  var bundler = watchify(
    browserify({
        // Define the entry point for our application
        entries: ['js1/prototype.js'],
        // Debugging is nice
        debug: true,
        // Allow importing from the following extensions
        extensions: [' ', 'js', 'jsx']
    })
      .transform(babelify.configure({
          presets: ["es2015"]
      }))
  );

  function rebundle() {
    return bundler.bundle()
      .on('error', function(err) {
        console.error(err.stack);
        this.emit('end');
      })
      .pipe(source('build.js'))
      .pipe(buffer())
      .pipe(sourcemaps.init({loadMaps: true}))
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest('./scripts'));
  }

  if (watch) {
      bundler.on('update', function () {
        console.log('-> bundling...');
        rebundle();
    });

    rebundle()
  } else {
    rebundle().pipe(exit());
  }
}

function watch() {
  return compile(true);
};

gulp.task('build', function() { return compile(); });
gulp.task('watch', function() { return watch(); });

gulp.task('default', ['watch']);

