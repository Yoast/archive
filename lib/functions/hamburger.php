<?
/**
 * Add JavaScripts for hamburgermenu
 */
function enqueue_hamburgermenu_scripts() {
	wp_enqueue_script( 'modernizr', get_stylesheet_directory_uri() . '/lib/js/modernizr.js' );
	wp_enqueue_script( 'hamburgermain', get_stylesheet_directory_uri() . '/lib/js/main.js', false, null, true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_hamburgermenu_scripts' );

/**
 * Add styling for hamburgermenu
 */
function enqueue_styles_hambugermenu() {
	wp_enqueue_style( 'style-name', get_stylesheet_directory_uri() . '/assets/css/ROCM.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_styles_hambugermenu' );

function add_to_genesis_header() {
	echo '<a class="nav-btn" id="nav-open-btn" href="#nav">&nbsp;</a>';
}
add_action( 'genesis_header', 'add_to_genesis_header' );

function add_to_genesis_menu() {
	echo '<a class="close-btn" id="nav-close-btn" href="#top">Close navigation</a>';
}
add_action( 'genesis_after_header', 'add_to_genesis_menu' );

function add_search_to_wp_menu ( $items, $args ) {
	if( 'primary' === $args -> theme_location ) {
		$search = get_search_form(false);
		$items .= '<li class="menu-item menu-item-search">';
		$items .= $search;
		$items .= '</li>';
	}
	return $items;
}
add_filter('wp_nav_menu_items','add_search_to_wp_menu',10,2);

function add_wrappers_open() {
	echo '<div id="outer-wrap"><div id="inner-wrap">';
}
//add_action( 'genesis_before_header', 'add_wrappers_open' );
function add_wrappers_close() {
	echo '</div></div>';
}
//add_action( 'genesis_after_footer', 'add_wrappers_close' );

function add_div_for_taco( $pre, $args ) {

	// Let genesis_markup() handle xhtml.
	if ( ! genesis_html5() )
		return false;

	// Return or echo the changed tag.
	$changed_tag = '<div id="container">';

	if ( $args['echo'] )
		echo $changed_tag;
	else
		return $changed_tag;
}
add_filter('genesis_markup_site-container', 'add_div_for_taco', 10, 2);