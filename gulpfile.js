const gulp = require('gulp');
const autoprefixer = require('autoprefixer');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const cssnano = require('cssnano');

function watch() {
	gulp.watch('./assets/src/scss/*.scss', style);
}

function style() {
	let plugins = [
        cssnano()
    ];

	// location of style
	return gulp.src('./assets/src/scss/*.scss')
	// Compile file
	.pipe(sass())
	// Use postcss
	.pipe(postcss(plugins))
	// Push build
	.pipe(gulp.dest('./assets/build/css'))
}

exports.style = style;
exports.watch = watch;