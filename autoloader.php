<?php

namespace Yoast\YoastCom\AlgoliaModifications;

spl_autoload_register( function ( $classname ) {
	if ( false !== strpos( $classname, 'Yoast\\YoastCom\\OAuthClientMods\\' ) ) {
		$classname = str_replace( 'Yoast\\YoastCom\\OAuthClientMods\\', '', $classname );

		$classname = strtolower( $classname );
		$classname = str_replace( '_', '-', $classname );

		$filename = dirname( __FILE__ ) . '/classes/class-' . $classname . '.php';

		if ( is_file( $filename ) ) {
			require_once $filename ;
		}
	}
} );
