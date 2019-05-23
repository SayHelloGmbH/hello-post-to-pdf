import {
	src,
	dest,
	watch,
	series,
	parallel
} from 'gulp';
import yargs from 'yargs';
import sass from 'gulp-sass';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import del from 'del';
const PRODUCTION = yargs.argv.prod;

export const styles = () => {
	return src('src/styles/plugin.scss')
		.pipe(gulpif(!PRODUCTION, sourcemaps.init()))
		.pipe(sass().on('error', sass.logError))
		.pipe(gulpif(PRODUCTION, postcss([autoprefixer])))
		.pipe(gulpif(PRODUCTION, cleanCss({
			compatibility: '*' // IE10+
		})))
		.pipe(gulpif(!PRODUCTION, sourcemaps.write()))
		.pipe(dest('dist/styles'));
}

export const clean = () => {
	return del(['dist']);
}

export const watchForChanges = () => {
	watch('src/styles/**/*.scss', { usePolling: true }, series(styles));
}

export const dev = series(clean, parallel(styles), watchForChanges)
export const build = series(clean, parallel(styles))
export default dev;
