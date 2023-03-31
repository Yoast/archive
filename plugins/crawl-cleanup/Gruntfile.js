var path = require( "path" );
var loadGruntConfig = require( "load-grunt-config" );
var timeGrunt = require( "time-grunt" );
global.developmentBuild = true;

/* global global, require, process */
module.exports = function( grunt ) {
	timeGrunt( grunt );

	const pkg = grunt.file.readJSON( "package.json" );
	const pluginVersion = pkg.yoast.pluginVersion;

	// Define project configuration
	var project = {
		pluginVersion: pluginVersion,
		pluginSlug: "yoast-crawl-cleanup",
		pluginMainFile: "yoast-crawl-cleanup.php",
		pluginVersionConstant: "YOAST_CRAWL_CLEANUP_PLUGIN_VERSION",
		paths: {
			/**
			 * Get config path.
			 *
			 * @returns {string} config path.
			 */
			get config() {
				return this.grunt + "config/";
			},
			css: "css/dist/",
			grunt: "grunt/",
			images: "images/",
			assets: "svn-assets/",
			js: "js/",
			languages: "languages/",
			logs: "logs/",
			vendor: "vendor/",
			svnCheckoutDir: ".wordpress-svn",
		},
		files: {
			css: [
				"css/dist/*.css",
				"!css/dist/*.min.css",
			],
			sass: [
				"css/src/*.scss",
			],
			images: [
				"images/*",
			],
			js: [
				"js/*.js",
				"!js/*.min.js",
			],
			php: [
				"*.php",
				"src/**/*.php",
			],
			phptests: "tests/**/*.php",
			/**
			 * Gets config path glob.
			 *
			 * @returns {string} Config path glob.
			 */
			get config() {
				return project.paths.config + "*.js";
			},
			/**
			 * Gets changelog path.
			 *
			 * @returns {string} Changelog path.
			 */
			get changelog() {
				return project.paths.theme + "changelog.txt";
			},
			grunt: "Gruntfile.js",
			artifact: "artifact",
			artifactComposer: "artifact-composer",
		},
		sassFiles: {
			"css/dist/admin.css": "css/src/admin.scss",
		},
		pkg: pkg,
		get developmentBuild() {
			return ! ( [ "release", "artifact", "deploy:trunk", "deploy:master" ].includes( process.argv[ 2 ] ) );
		},
	};

	// Used to switch between development and release builds
	if ( [ "release", "artifact", "deploy:trunk", "deploy:master" ].includes( process.argv[ 2 ] ) ) {
		global.developmentBuild = false;
	}

	// Load Grunt configurations and tasks
	loadGruntConfig( grunt, {
		configPath: path.join( process.cwd(), "node_modules/@yoast/grunt-plugin-tasks/config/" ),
		overridePath: path.join( process.cwd(), project.paths.config ),
		data: project,
		jitGrunt: {
			staticMappings: {
				addtextdomain: "grunt-wp-i18n",
				makepot: "grunt-wp-i18n",
				"update-version": "@yoast/grunt-plugin-tasks",
				"set-version": "@yoast/grunt-plugin-tasks",
			},
		},
	} );
};
