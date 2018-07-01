/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');

module.exports = (browserSync, files) => {
  function copyFiles(obj) {
    return gulp.src(obj.src)
      .pipe(gulp.dest(obj.dest));
  }

  gulp.task('copy:icons', () => copyFiles(files.copy.icons));

  gulp.task('copy:fonts', () => copyFiles(files.copy.fonts));

  gulp.task('watch:icons', ['copy:icons'], () => browserSync.reload());

  gulp.task('watch:fonts', ['copy:fonts'], () => browserSync.reload());
};
