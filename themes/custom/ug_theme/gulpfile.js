"use strict";

let gulp = require('gulp-help') (require('gulp'));
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let browserSync = require('browser-sync').create();
let yaml = require('js-yaml');
let fs = require('fs');
let sassGlob = require('gulp-sass-glob');
let assign = require('lodash.assign');

// load default gulp configs settings.
let config = yaml.safeLoad(fs.readFileSync('gulpfile.yml', 'utf8'), {json: true});
try {
	// override default config settings.
	let customConfig = yaml.safeLoad(fs.readFileSync('local.gulpfile.yml', 'utf8'), {json: true});
	config = assign(config, customConfig);
}
catch (e) {
	console.log('No custom config found! Proceeding with default config only.');
}

// specific sass options
let sassOptions = {
	errLogToConsole: true,
	outputStyle: 'expanded',
	precision: 10
};

// browser sync watch files
let watchedFiles = [
	'assets/css/styles.css',
	'templates/**/*.twig'
];

// sass task
gulp.task('sass', function () {
	return gulp.src('assets/sass/styles.scss')
		.pipe(sassGlob())
		.pipe(sourcemaps.init({loadMaps: true}))
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('assets/css'))
		.pipe(browserSync.reload({ stream: true }));
});

// BrowserSync
gulp.task('browser-sync', function () {
	browserSync.init(watchedFiles, {
		proxy: config.browsersync.proxy,
		notify: config.browsersync.notify
	})
});

// Default task to be run with `gulp`
gulp.task('default', ['sass', 'browser-sync'], function () {
	gulp.watch("assets/sass/**/*.scss", ['sass']);
	gulp.watch(["assets/**/*.css", "assets/**/*.js", "templates/**/*.twig"])
		.on('change', browserSync.reload);
});
