/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');
const imagemin = require('gulp-imagemin');

module.exports = (browserSync, files) => {
  function processImages(images) {
    let stream = gulp.src(images.src);
    if (process.env.NODE_ENV === 'production') {
      stream = stream.pipe(imagemin([
        imagemin.jpegtran(),
        imagemin.optipng(),
        imagemin.svgo({
          plugins: [{
            cleanupIDs: false,
            removeUselessDefs: false,
          }],
        }),
      ]));
    }
    return stream.pipe(gulp.dest(images.dest));
  }

  gulp.task('build:images', () => processImages(files.images));

  gulp.task('watch:images', ['build:images'], () => browserSync.reload());
};
