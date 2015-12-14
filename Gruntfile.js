/*global module:false*/
module.exports = function(grunt) {

    function addSlash() {
        return [].join.call(arguments, '/');
    }

    var Path = {
        dev: 'dev',
        dist: 'dist',
        test: 'test',
        name: {
            js: 'js',
            scss: 'scss',
            css: 'css'
        }
    };

    Path.js = {
        dev: addSlash(Path.dev , Path.name.js, 'Elementalist'),
        dist: addSlash(Path.dist , Path.name.js, 'Elementalist'),
        test: addSlash(Path.dev , Path.name.js , Path.test)
    };

    Path.scss = {
        src: addSlash(Path.dev, Path.name.scss, 'Elementalist'),
        dev: addSlash(Path.dev, Path.name.css, 'Elementalist'),
        dist: addSlash(Path.dist, Path.name.css, 'Elementalist')
    };

    Path.css = {
        dev: Path.scss.dev,
        dist: Path.scss.dist
    };

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
      '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
      '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
      '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author %>;' +
      ' Licensed <%= pkg.license %> */\n',
    Path: Path,
    // Task configuration.
    phpunit: {
        classes: {
            dir: 'tests'
        },
        options: {
            bin: 'vendor/bin/phpunit',
            bootstrap: 'tests/Bootstrap.php',
            colors: true,
            testSuffix: 'test.php',
            testdoxHtml: 'testRunner.html',
            coverageHtml: 'testCoverage',
            verbose: true,
            debug: true
        }
    },
    watch: {
      options: {
        livereload: true
      },
      htmlPHP: {
        files: '**/*.php',
        tasks: ['phpunit']
      }
    }
  });

  // These plugins provide necessary tasks.
  require('load-grunt-tasks')(grunt);

  // Default task.
  grunt.registerTask('default', ['jshint', 'concat', 'uglify']);

};
