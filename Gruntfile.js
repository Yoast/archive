let loadGruntConfig = require( "load-grunt-config" );
let timeGrunt = require( "time-grunt" );
let path = require( "path" );
const { flattenVersionForFile } = require( "./webpack/paths" );

module.exports = function( grunt ) {

	timeGrunt( grunt );

	const readPackageJson = grunt.file.readJSON( "package.json" );
	const pluginVersion = readPackageJson.version;

	let project = {
		pluginMainFile: "fact-check-for-yoastseo.php",
		pluginVersion: pluginVersion,
		pluginSlug: "fact-check-for-yoastseo",
		paths: {
			get config() {
				return this.grunt + "config/";
			},
			grunt: "grunt/",
			js: "js/",
			css: "css/",
		},
		files: {
			js: [
				"js/src/**/*.js",
			],
			php: [
				"*.php",
			],
			phptests: "tests/**/*.php",
			get config() {
				return project.paths.config + "*.js";
			},
			grunt: "Gruntfile.js",
		},
	};

	project.pluginVersionSlug = flattenVersionForFile( pluginVersion );

	// Load Grunt configurations and tasks
	loadGruntConfig( grunt, {
		configPath: path.join( process.cwd(), project.paths.config ),
		data: project,
	} );

};
