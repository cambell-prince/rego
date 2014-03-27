var gulp = require('gulp');
var gutil = require('gulp-util');
var exec = require('gulp-exec');

var less = require('gulp-less');
var minifycss = require('gulp-minify-css');
//var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
//var imagemin = require('gulp-imagemin');
var rename = require('gulp-rename');
var clean = require('gulp-clean');
var concat = require('gulp-concat');
var cache = require('gulp-cache');

var livereload = require('gulp-livereload');
var lr = require('tiny-lr');
var server = lr();

var phpunit = require('gulp-phpunit');
var phpunit_command = 'src/vendor/phpunit/phpunit/phpunit tests';

gulp.task('default', function() {
  // place code for your default task here
});


// PHP unit tests
gulp.task('test-php', function() {
	var options = {
		ignoreError: true,
		debug: false
	}
	return gulp.src('tests/*Test.php')
		.pipe(phpunit(phpunit_command, options));
});


gulp.task('watch-test-php', function() {
	gulp.watch(['tests/**/*.php', 'src/App/**/*.php'], ['test-php']);
});

gulp.task('script-dev', function() {
	return gulp.src('src/client/**/*')
		.pipe(livereload(server));
});

gulp.task('css-dev', function() {
	return gulp.src('src/assets/*.css')
		.pipe(livereload(server));
});

gulp.task('watch', function() {
	server.listen(35729, function(err) {
		if (err) {
			return console.log(err);
		}
		gulp.watch('src/client/**/*', ['script-dev']);
		gulp.watch('src/assets/*.css', ['css-dev']);
	});
});

gulp.task('upload', function() {
	var options = {
		silent: false,
		src: "htdocs",
		dest: "root@new.traintoproclaim.com:/var/www/virtual/new.traintoproclaim.com/htdocs/",
		key: "/home/cambell/dev.key"
	};
	gulp.src('htdocs')
		.pipe(exec('rsync -rzlt --chmod=Dug=rwx,Fug=rw,o-rwx --delete --exclude-from="upload-exclude.txt" --stats --rsync-path="sudo -u vu2003 rsync" --rsh="ssh -i <%= options.key %>" <%= file.path %>/ <%= options.dest %>', options));
	gulp.src('htdocs/images')
		.pipe(exec('rsync -rzlt --chmod=Dug=rwx,Fug=rw,o-rwx --stats --rsync-path="sudo -u vu2003 rsync" --rsh="ssh -i <%= options.key %>" <%= file.path %>/ <%= options.dest %>images/', options));
});


gulp.task('db-backup', function() {
	var options = {
		silent: false,
		dest: "root@new.traintoproclaim.com",
		key: "/home/cambell/dev.key",
		password: gutil.env.password
	};
	gulp.src('')
		.pipe(exec('mysqldump -u cambell --password=<%= options.password %> traintoproclaim | gzip > backup/backup.sql.gz', options));
});

gulp.task('db-copy', ['db-backup'], function() {
	var options = {
		silent: false,
		dest: "root@new.traintoproclaim.com",
		key: "/home/cambell/dev.key",
		password: gutil.env.password
	};
	gulp.src('')
		.pipe(exec('ssh -C -i <%= options.key %> <%= options.dest %> mysqldump -u cambell --password=<%= options.password %> traintoproclaim2 | mysql -u cambell --password=<%= options.password %> -D traintoproclaim', options));
});

