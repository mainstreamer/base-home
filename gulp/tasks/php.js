/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');

module.exports = (browserSync) => {
  gulp.task('watch:php', (cb) => {
    browserSync.reload();
    cb();
  });
};
