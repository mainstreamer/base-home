/* eslint-disable import/no-extraneous-dependencies */
const browserSync = require('browser-sync').create();
const del = require('del');
const gulp = require('gulp');

const files = require('./files');
// require('./tasks/copy')(browserSync, files);
// require('./tasks/css')(browserSync, files);
require('./tasks/twig')(browserSync, files);

require('./tasks/php')(browserSync, files);

// require('./tasks/images')(browserSync, files);
// require('./tasks/js')(browserSync, files);
// require('./tasks/icons')(browserSync, files);

gulp.task('clean', (cb) => {
    del(files.destRoot).then(() => cb());
});

const buildTasks = [
    // 'build:css',
    // 'build:js',
    // 'build:images',
    // 'build:sprites',
    // 'copy:icons',
    // 'copy:fonts',
];

gulp.task('default', ['clean'], () => {
    gulp.start(buildTasks);
});

gulp.task('watch', buildTasks, () => {
    browserSync.init({
    proxy: `${process.env.HOST || 'localhost'}:${process.env.PORT || '8000'}`,
});
gulp.watch(files.twig.watch, ['watch:twig']);
// gulp.watch(files.css.watch, ['build:css']);
gulp.watch(files.php.watch, ['watch:php']);
// gulp.watch(files.js.watch, ['watch:js']);
// gulp.watch(files.images.watch, ['watch:images']);
// gulp.watch(files.sprites.watch, ['watch:sprites']);
// gulp.watch(files.copy.icons.src, ['watch:icons']);
// gulp.watch(files.copy.fonts.src, ['watch:fonts']);
});