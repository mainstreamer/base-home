/* eslint-disable import/no-extraneous-dependencies */
const gulp = require('gulp');
const svgSprite = require('gulp-svg-sprites');

module.exports = (browserSync, files) => {
  gulp.task('build:sprites', () => gulp.src(files.sprites.src)
    .pipe(svgSprite({ mode: 'symbols' }))
    .pipe(gulp.dest(files.sprites.dest)));

  gulp.task('watch:sprites', ['build:sprites'], () => browserSync.reload());
};
