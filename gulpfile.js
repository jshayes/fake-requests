var gulp = require('gulp');
var run = require('gulp-run');
var notify = require('gulp-notify');

gulp.task('watch', function() {
    gulp.watch(['src/**/*.php', 'tests/**/*.php'], function (event) {
        run('clear').exec();
        run('./vendor/bin/phpunit --colors=always').exec().on('error', notify.onError({
            title: 'Failure',
            message: 'Tests have failed!'
        }));
      });
});

gulp.task('default', ['watch']);
