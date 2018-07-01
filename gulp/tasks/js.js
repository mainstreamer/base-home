/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');
const buffer = require('vinyl-buffer');
const tap = require('gulp-tap');
const util = require('gulp-util');
const browserify = require('browserify');
const babelify = require('babelify');
const uglify = require('gulp-uglify');

module.exports = (browserSync, files) => {
  gulp.task('build:js', () => {
    let stream = gulp.src(files.js.src)
      .pipe(tap((file) => {
        const config = {
          basedir: files.js.basedir,
          entries: file,
          debug: process.env.NODE_ENV === 'production',
          transform: [babelify],
        };
        const contents = browserify(config)
          .bundle()
          .on('error', (error) => {
            util.log(`Browserify error:\n${error.codeFrame}`);
            browserSync.notify('Browserify error');
            stream.emit('end');
          });
        // eslint-disable-next-line no-param-reassign
        file.contents = contents;
      }))
      .pipe(buffer());
    if (process.env.NODE_ENV === 'production') {
      stream = stream.pipe(uglify());
    }
    return stream.pipe(gulp.dest(files.js.dest));
  });

  gulp.task('watch:js', ['build:js'], () => browserSync.reload());
};
