/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');

module.exports = (browserSync) => {
  gulp.task('watch:twig', (cb) => {
    browserSync.reload();
    cb();
  });
};
