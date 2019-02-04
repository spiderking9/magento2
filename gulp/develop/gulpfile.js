'use strict';
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    browserSync = require('browser-sync').create(),
    sourcemaps = require('gulp-sourcemaps'),
    stripCssComments = require('gulp-strip-css-comments'),
    plumber = require('gulp-plumber'),
    sassLint = require('gulp-sass-lint'),
    sassGlob = require('gulp-sass-glob'),
    multiDest = require('gulp-multi-dest'),
    env = (process.argv.filter(function(val) {return val === '-production'}).length > 0)? 'production' : 'development';

console.log('Using environment configuration file: ' + env);

var config = require('./../gulp.config.'+env+'.js'),
    base_theme = config.base_theme,
    themes = config.themes,
    languages = config.languages,
    scss_excluded = config.scss_excluded,
    host = config.host,
    lint_mode = config.lint_mode,
    themes_dir = './../../app/design/frontend/',
    pub_dir = './../../pub/static/frontend/';

/** Browser Sync **/
/******************/
gulp.task('browsersync', function () {
    browserSync.init({
        open: false,
        host: host,
        proxy: host,
        notify: true,
        ui: false
    });
});


/** TASKS **/
/******************/

// Distribute base theme files
gulp.task('setup', function() {

    var tasks = [];

    // Only if base theme exist and is development mode
    if(base_theme && env === 'development') {

        var baseScss = themes_dir + base_theme + '/web/scss/_modules/*';

        var childThemes = themes.filter(function(theme) {
            return theme !== base_theme;
        });

        // Distribute SCSS base files to child themes
        childThemes.forEach(function(theme) {

            console.log('-- Distribute base Sass files to theme: ' + theme);

            var destination = themes_dir + theme +'/web/scss/_base/';

            var task = gulp.src(baseScss).pipe(gulp.dest(destination));

            tasks.push(task);
        });
    }

    return tasks;
});

// SASS
gulp.task('sass', function () {

    var tasks = [];

    themes.forEach(function(theme) {

        console.log('-- Starting of serve Sass for theme: ' + theme);

        //Scss source files per theme
        var scssSrc = [
            themes_dir + theme + '/web/scss/**/*.scss'
        ];

        //Excluded Scss source files per theme
        scss_excluded.forEach(function(file) {
            scssSrc.push('!'+themes_dir + theme + '/web/scss/' + file);
        });

        //Also exclude Scss base files if theme is child theme
        if(theme !== base_theme) {
            scssSrc.push('!'+themes_dir + theme + '/web/scss/_base/*.scss');
        }

        console.log(scssSrc);

        var destResult = [];

        //CSS dest file per theme
        var destCss = themes_dir + theme + '/web/css';

        destResult.push(destCss);

        //CSS static files destinations for all languages;
        languages.forEach(function(language) {
            destResult.push(pub_dir + theme + '/' + language +'/css');
        });

        var task = gulp.src(scssSrc)
            .pipe(sassGlob())
            .pipe(plumber(function (error) {
                console.log('Plumber sass error compile: ', error);
            }));

            // If lint mode is on
            if(lint_mode) {
                task.pipe(sassLint({
                    configFile: './.sass-lint.yml'
                })).pipe(sassLint.format())
            }

            // Continue tasks
            task.pipe(sourcemaps.init())
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(stripCssComments())
            .pipe(autoprefixer({
                    browsers: ['last 2 versions', 'Explorer >= 10', 'Android >= 4.1', 'Safari >= 7', 'iOS >= 7', 'Firefox >= 20'],
                    cascade: false
                })
            )
            .pipe(sourcemaps.write('.'))
            .pipe(multiDest(destResult))
            .pipe(browserSync.reload({stream: true}));


        tasks.push(task);

    });

    return tasks;
});

// JS
gulp.task('js', function () {
    var tasks = [];

    themes.forEach(function(theme) {
        var jsSrc = themes_dir + theme + '/web/js/**/*.js';

        //CSS static files destinations for all languages;
        var destStaticJs = [];

        themes.forEach(function(theme) {
            languages.forEach(function(language) {
                destStaticJs.push(pub_dir + theme + '/' + language +'/js/');
            });
        });

        var task = gulp.src(jsSrc)
            .pipe(plumber(function (error) {
                console.log('Plumber js error: ', error);
            }))
            .pipe(multiDest(destStaticJs))
            .pipe(browserSync.reload({stream: true}));

        tasks.push(task);
    });

    return tasks;
    // return null;
});

// Watch
gulp.task('watch', ['browsersync', 'setup', 'sass', 'js'], function () {

    var baseScssSrc = [];

    //Watch base scss files if need to be distributed
    baseScssSrc.push(themes_dir + base_theme + '/web/scss/**/*.scss');

    //Exclude base scss files what needs to be excluded from watch
    scss_excluded.forEach(function(file) {
        baseScssSrc.push('!' + themes_dir + base_theme + '/web/scss/' + file);
    });

    var scssSrc = [];

    var jsSrc = [];

    themes.forEach(function(theme) {

        //Watch Scss files per theme
        scssSrc.push(themes_dir + theme + '/web/scss/**/*.scss');

        //Excluded Scss source files per theme
        scss_excluded.forEach(function(file) {
            scssSrc.push('!' + themes_dir + theme + '/web/scss/' + file);
        });

        //Also exclude Scss base files if theme is child theme
        if(theme !== base_theme) {
            scssSrc.push('!' + themes_dir + theme + '/web/scss/_base/*.scss');
        }

        // Main js files per theme
        jsSrc.push(themes_dir + theme + '/web/js/**/*.js');

    });

    //Distribute files first if notify a changes of base files
    gulp.watch(baseScssSrc, ['setup', 'sass']);

    // Watch .scss files
    gulp.watch(scssSrc, ['sass']);

    // Watch .js files
    gulp.watch(jsSrc, ['js']);

});

// define the default task and add the watch task to it
gulp.task('default', ['watch']);
