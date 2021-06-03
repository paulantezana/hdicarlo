import { src, dest, task, watch, series, parallel } from 'gulp';
import sass from 'gulp-sass';
import minify from 'gulp-minify';

function scss(done) {
    src(['./src/scss/*.scss'])
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'compressed'
        }))
        .on('error', console.error.bind(console))
        .pipe(dest('./build/css'));
    done();
}

function script(done) {
    src(['./src/script/**/*.js'])
        .pipe(minify({
            ext: {
                min: '.js'
            },
            noSource: true,
        }))
        .pipe(dest('./build/script'));
    done();
}

function watch_files() {
    watch('./src/scss/**/*.scss', series(scss));
    watch('./src/script/**/*.js', series(script));
}

task("default", parallel(watch_files));