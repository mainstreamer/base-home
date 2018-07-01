/* eslint-disable import/no-extraneous-dependencies */
// const gulp = require('gulp');
// const sass = require('gulp-sass');
// const postcss = require('gulp-postcss');
// const autoprefixer = require('autoprefixer');
// const cssnano = require('cssnano');
//
// module.exports = (browserSync, files) => {
//   gulp.task('build:css', () => {
//     let stream = gulp.src(files.css.src)
//       .pipe(sass())
//       .on('error', sass.logError);
//     if (process.env.NODE_ENV === 'production') {
//       stream = stream.pipe(postcss([autoprefixer(), cssnano()]));
//     }
//     return stream.pipe(gulp.dest(files.css.dest))
//       .pipe(browserSync.stream());
//   });
// };
