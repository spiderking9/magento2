'use strict';
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    stripCssComments = require('gulp-strip-css-comments'),
    plumber = require('gulp-plumber'),
    sassGlob = require('gulp-sass-glob'),
    env = (process.argv.filter(function(val) {return val === '-production'}).length > 0)? 'production' : 'development';

console.log('Using environment configuration file: ' + env);

var config = require('./gulp.config.'+env+'.js'),
    base_theme = config.base_theme,
    themes = config.themes,
    scss_excluded = config.scss_excluded,
    themes_dir = './../app/design/frontend/';


/** TASKS **/
/******************/

// SASS (compile only production Css files)
gulp.task('sass-compile', function () {

    var tasks = [];

    themes.forEach(function(theme) {

        console.log('-- Start Sass to Css for theme: ' + theme);

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

        //CSS dest file per theme
        var destCss = themes_dir + theme + '/web/css/';

        var task = gulp.src(scssSrc)
            .pipe(sassGlob())
            .pipe(plumber(function (error) {
                console.log('Plumber sass error compile: ', error);
            }))
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(stripCssComments())
            .pipe(autoprefixer({
                    browsers: ['last 2 versions', 'Explorer >= 10', 'Android >= 4.1', 'Safari >= 7', 'iOS >= 7', 'Firefox >= 20'],
                    cascade: false
                })
            )
            .pipe(gulp.dest(destCss));

        console.log('-- End of Sass to Css for theme: ' + theme);

        tasks.push(task);
    });

    return tasks;
});

// only for a production
gulp.task('compile',['sass-compile']);
