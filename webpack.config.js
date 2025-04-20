const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')  // Vérifie que ce fichier existe bien dans assets/
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';  // Assure-toi que cela permet d'inclure les polyfills nécessaires
        config.corejs = '3.38';        // Vérifie que core-js est installé
    })
    .enableSassLoader()   // Assure-toi que les dépendances pour Sass sont bien installées
;

module.exports = Encore.getWebpackConfig();
