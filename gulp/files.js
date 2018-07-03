const path = require('path');
const projectRoot = path.resolve(__dirname, '..');
const sourceRoot = path.resolve(projectRoot, 'assets');
const twigRoot = path.resolve(projectRoot, 'templates');
const phpRoot = path.resolve(projectRoot, 'src');
const destRoot = path.resolve(projectRoot, 'public', 'dist');

const twig = {
    watch: path.join(twigRoot, '**', '**.twig'),
};

const php = {
    watch: path.join(phpRoot, '**', '**.php'),
};

module.exports = {
    projectRoot,
    sourceRoot,
    destRoot,
    twig,
    php,
};
