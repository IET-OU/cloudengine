/*!
  CloudEngine task-runner | © 2016 The Open University (IET-OU).
*/

module.exports = function (grunt) {
	'use strict';

	grunt.log.subhead('Running CloudEngine build and tests...');

	grunt.initConfig({
		exec: {
			phplint: 'vendor/bin/parallel-lint system/application',
		},
		jshint: {
			options: {
				bitwise: true,
				curly: true,
				eqeqeq: true,
				futurehostile: true,
				//laxcomma: true,
				undef: true,
				// https://github.com/jshint/jshint/blob/master/src/messages.js#L80
				//'-W030': true,    // Ignore Expected an assignment or function call and instead saw an expression;
				//'-W069': true,    // Ignore {a} is better written in dot notation;
				//'-W116': true,    // Ignore Expected '{a}' and instead saw '{b}' -- brackets;
				//'-W060': true,    // Ignore document.write can be a form of eval;
				//'-W061': true,    // Ignore eval can be harmful;
				globals: { jQuery: false, window: false, escape: false, ga: false, getOEmbedProvider: false }
			},
			JS: [ '_scripts/*.js', '!_scripts/jquery*', '!_scripts/buildpager.*', '!_scripts/date.*', '!_scripts/tiny_mce/*', '_scripts/*oembed.js' ],
			Grunt: {
				options: { node: true },
				files: { src: 'Gruntfile.js' }
			}
		},
		// 'validate XML' doesn't work with CodeIgniter views :(!
		validate_xml: {
			views: {
				src: [ 'system/application/views/auth/*.php' ]
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	// 'grunt-contrib-validate-xml' gives MUCH better feedback than 'grunt-xml-validator'!
	grunt.loadNpmTasks('grunt-contrib-validate-xml');

	grunt.registerTask('default', [ 'jshint' ]);

};