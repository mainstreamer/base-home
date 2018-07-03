const browserSync = require('browser-sync').create();
const del = require('del');
const gulp = require('gulp');
const files = require('./files');
require('./tasks/twig')(browserSync, files);
require('./tasks/php')(browserSync, files);

gulp.task('clean', (cb) => {
    del(files.destRoot).then(() => cb());
});

gulp.task('watch', [], () => {
    browserSync.init({
    proxy: `${process.env.HOST || 'localhost'}:${process.env.PORT || '8000'}`,
});

gulp.watch(files.twig.watch, ['watch:twig']);
gulp.watch(files.php.watch, ['watch:php']);
});