const path = require('path');
const fs = require('fs');

const projectRoot = path.resolve(__dirname, '..');
const sourceRoot = path.resolve(projectRoot, 'assets');
const twigRoot = path.resolve(projectRoot, 'templates');
const phpRoot = path.resolve(projectRoot, 'src');
const destRoot = path.resolve(projectRoot, 'public', 'dist');
// const jsBaseDir = path.resolve(sourceRoot, 'js');

// const css = {
//     src: path.join(sourceRoot, 'scss', '*.scss'),
//     watch: path.join(sourceRoot, 'scss', '**', '*.scss'),
//     dest: path.join(destRoot, 'css'),
// };

const twig = {
    watch: path.join(twigRoot, '**', '**.twig'),
};

const php = {
    watch: path.join(phpRoot, '**', '**.php'),
};


// const js = {
//     basedir: jsBaseDir,
//     src: fs.readdirSync(jsBaseDir).filter(file => /\.js$/.test(file)).map(entry => path.join(jsBaseDir, entry)),
// watch: path.join(jsBaseDir, '**', '*.js'),
//     dest: path.join(destRoot, 'js'),
// };

// const images = {
//     src: path.join(sourceRoot, 'images', '**', '*.{svg,png,jpg,jpeg}'),
//     watch: path.join(sourceRoot, 'images', '**', '*.{svg,png,jpg,jpeg}'),
//     dest: path.join(destRoot, 'images'),
// };
//
// const sprites = {
//     src: path.join(sourceRoot, 'sprites', '*.svg'),
//     watch: path.join(sourceRoot, 'sprites', '*.svg'),
//     dest: path.join(destRoot, 'sprites'),
// };
//
// const copy = {
//     icons: {
//         src: path.join(sourceRoot, 'icons', '*'),
//         dest: path.join(destRoot, 'icons'),
//     },
//     fonts: {
//         src: path.join(sourceRoot, 'fonts', '**'),
//         dest: path.join(destRoot, 'fonts'),
//     },
// };

module.exports = {
    projectRoot,
    sourceRoot,
    destRoot,
    twig,
    php,
};

//
// module.exports = {
//     projectRoot,
//     sourceRoot,
//     destRoot,
//     twig,
//     php,
//     css,
//     js,
//     images,
//     copy,
//     sprites,
// };