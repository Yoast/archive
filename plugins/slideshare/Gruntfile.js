module.exports = function(grunt) {

	'use strict';

	require('load-grunt-tasks')(grunt, {
		pattern: ['grunt-*', 'assemble-less']
	});

	require('time-grunt')(grunt);

	// Project configuration.
  grunt.initConfig({
		pkg       : grunt.file.readJSON('package.json'),

		// I18n
		addtextdomain: {
			options: {
				textdomain: '<%= pkg.plugin.textdomain %>'
			},
			php    : {
				files: {
					src: [
						'*php', '**/*.php', '!node_modules/**'
					]
				}
			}
		},

		checktextdomain: {
			options: {
				text_domain: '<%= pkg.plugin.textdomain %>',
				keywords   : [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					'esc_attr__:1,2d',
					'esc_html__:1,2d',
					'esc_attr_e:1,2d',
					'esc_html_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'esc_html_x:1,2c,3d'
				]
			},
			files  : {
				expand: true,
				src   : [
					'**/*.php', '!node_modules/**'
				]
			}
		},

		makepot: {
           target: {
               options: {
                   domainPath: '/languages',
                   potFilename: 'slideshare.pot',
				   potHeaders: {
					   poedit: true,
					   'report-msgid-bugs-to': '<%= pkg.pot.reportmsgidbugsto %>',
					   'language-team': '<%= pkg.pot.languageteam %>',
					   'last-translator': '<%= pkg.pot.lasttranslator %>'
				   },
                   type: 'wp-plugin'
               }
           }
       },
		glotpress_download: {
			plugin: {
				options: {
					url        : '<%= pkg.plugin.glotpress %>',
					domainPath : 'languages/',
					file_format: '%domainPath%/%textdomain%-%wp_locale%.%format%',
					slug       : '<%= pkg.plugin.slug %>',
					textdomain : '<%= pkg.plugin.textdomain %>',
					formats    : ['mo'],
					filter     : {
						translation_sets  : false,
						minimum_percentage: 50,
						waiting_strings   : false
					}
				}
			}
		}
  });
	grunt.loadNpmTasks( 'grunt-wp-i18n' );grunt.loadNpmTasks( 'grunt-glotpress' );

	grunt.registerTask('check', [
		'jshint',
		'jsonlint',
		'jsvalidate',
		'checktextdomain'
	]);

	grunt.registerTask('build', [
		'build:js',
		'build:i18n'
	]);

	grunt.registerTask('build:js', [
		'browserify',
		'uglify'
	]);


	grunt.registerTask('build:i18n', [
		'addtextdomain',
		'makepot',
		'glotpress_download'
	]);

	grunt.registerTask('default', [
		'build'
	]);

};
