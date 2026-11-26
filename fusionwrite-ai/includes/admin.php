<?php
/**
 * The admin workspace: menu, assets and the page shell.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pages of the workspace, in menu order. `group` splits the menu into
 * writing, setup and help.
 *
 * @return array<string,array{label:string,group:int}>
 */
function fswrt_pages() {
	return array(
		'overview'    => array(
			'label' => __( 'Overview', 'fusionwrite-ai' ),
			'group' => 1,
		),
		'write'       => array(
			'label' => __( 'Write', 'fusionwrite-ai' ),
			'group' => 1,
		),
		'bulk'        => array(
			'label' => __( 'Bulk', 'fusionwrite-ai' ),
			'group' => 1,
		),
		'history'     => array(
			'label' => __( 'History', 'fusionwrite-ai' ),
			'group' => 1,
		),
		'providers'   => array(
			'label' => __( 'Providers', 'fusionwrite-ai' ),
			'group' => 2,
		),
		'images'      => array(
			'label' => __( 'Images', 'fusionwrite-ai' ),
			'group' => 2,
		),
		'preferences' => array(
			'label' => __( 'Preferences', 'fusionwrite-ai' ),
			'group' => 2,
		),
		'guide'       => array(
			'label' => __( 'Guide', 'fusionwrite-ai' ),
			'group' => 3,
		),
	);
}

/**
 * Which workspace page is being shown, or an empty string on other screens.
 *
 * @return string
 */
function fswrt_current_page() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading which admin page is open.
	$slug = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( 'fusionwrite-ai' === $slug ) {
		return 'overview';
	}
	if ( 0 === strpos( $slug, 'fusionwrite-ai-' ) ) {
		$page = substr( $slug, strlen( 'fusionwrite-ai-' ) );
		return array_key_exists( $page, fswrt_pages() ) ? $page : '';
	}
	return '';
}

add_action( 'admin_menu', 'fswrt_register_menu' );

/**
 * Register the admin menu. Every page is a real admin page, so each has its
 * own address and works with the browser's back button.
 */
function fswrt_register_menu() {
	$capability = fswrt_capability();

	add_menu_page( 'FusionWrite AI', 'FusionWrite AI', $capability, 'fusionwrite-ai', 'fswrt_render_page', 'dashicons-edit-large', 6 );

	foreach ( fswrt_pages() as $page => $info ) {
		$slug = 'overview' === $page ? 'fusionwrite-ai' : 'fusionwrite-ai-' . $page;
		add_submenu_page( 'fusionwrite-ai', $info['label'] . ' ‹ FusionWrite AI', $info['label'], $capability, $slug, 'fswrt_render_page' );
	}
}

add_action( 'admin_init', 'fswrt_maybe_open_after_activation' );

/**
 * Open the workspace once, right after the plugin is activated.
 */
function fswrt_maybe_open_after_activation() {
	if ( ! get_transient( 'fswrt_just_activated' ) ) {
		return;
	}
	delete_transient( 'fswrt_just_activated' );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checks whether several plugins were activated at once.
	if ( wp_doing_ajax() || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( fswrt_capability() ) ) {
		return;
	}

	wp_safe_redirect( fswrt_page_url( 'overview' ) );
	exit;
}

add_filter( 'plugin_action_links_' . plugin_basename( FSWRT_FILE ), 'fswrt_action_links' );

/**
 * Add a link to the workspace on the Plugins screen.
 *
 * @param string[] $links Existing links.
 * @return string[]
 */
function fswrt_action_links( $links ) {
	array_unshift( $links, '<a href="' . esc_url( fswrt_page_url( 'overview' ) ) . '">' . esc_html__( 'Open workspace', 'fusionwrite-ai' ) . '</a>' );
	return $links;
}

add_filter( 'admin_body_class', 'fswrt_body_class' );

/**
 * Mark workspace screens so the stylesheet can take over the page.
 *
 * @param string $classes Space-separated class names.
 * @return string
 */
function fswrt_body_class( $classes ) {
	if ( '' === fswrt_current_page() ) {
		return $classes;
	}
	$settings = fswrt_get_settings();
	$classes .= ' fswrt-screen';
	if ( $settings['defaults']['fullscreen'] ) {
		$classes .= ' fswrt-fullscreen';
	}
	return $classes;
}

add_action( 'in_admin_header', 'fswrt_quiet_notices', 1 );

/**
 * Keep other plugins' notices out of the workspace. They stay visible on
 * every other admin screen.
 */
function fswrt_quiet_notices() {
	if ( '' === fswrt_current_page() ) {
		return;
	}
	remove_all_actions( 'admin_notices' );
	remove_all_actions( 'all_admin_notices' );
}

add_action( 'admin_enqueue_scripts', 'fswrt_enqueue_assets' );

/**
 * Load the workspace assets on workspace screens only.
 */
function fswrt_enqueue_assets() {
	$page = fswrt_current_page();
	if ( '' === $page ) {
		return;
	}

	wp_enqueue_style( 'fswrt-admin', FSWRT_URL . 'assets/css/admin.css', array(), FSWRT_VERSION );
	wp_enqueue_script( 'fswrt-admin', FSWRT_URL . 'assets/js/admin.js', array(), FSWRT_VERSION, true );
	wp_enqueue_script( 'fswrt-tour', FSWRT_URL . 'assets/js/tour.js', array( 'fswrt-admin' ), FSWRT_VERSION, true );

	$settings = fswrt_get_settings();
	$engines  = fswrt_image_engines();
	$engine   = $settings['images']['engine'];
	$urls     = array();
	foreach ( array_keys( fswrt_pages() ) as $name ) {
		$urls[ $name ] = fswrt_page_url( $name );
	}

	wp_localize_script(
		'fswrt-admin',
		'fswrtData',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'fswrt_nonce' ),
			'page'      => $page,
			'pages'     => $urls,
			'connected' => count( fswrt_connected_providers() ),
			'tourAuto'  => 'overview' === $page && ! get_user_meta( get_current_user_id(), 'fswrt_tour_done', true ),
			'images'    => array(
				'ready' => 'none' !== $engine && '' === fswrt_image_engine_problem( $engine ),
				'wait'  => isset( $engines[ $engine ] ) ? (int) $engines[ $engine ]['wait'] : 0,
				'label' => isset( $engines[ $engine ] ) ? $engines[ $engine ]['label'] : '',
			),
			'strings'   => require FSWRT_DIR . 'includes/strings.php',
			'tour'      => require FSWRT_DIR . 'includes/tour.php',
		)
	);
}

/**
 * Print the workspace: the menu on the left and the current page beside it.
 */
function fswrt_render_page() {
	if ( ! current_user_can( fswrt_capability() ) ) {
		wp_die( esc_html__( 'You are not allowed to open this page.', 'fusionwrite-ai' ) );
	}

	$current = fswrt_current_page();
	if ( '' === $current ) {
		$current = 'overview';
	}
	$pages = fswrt_pages();
	$group = 0;
	?>
	<div class="fw-app" id="fw-app" data-page="<?php echo esc_attr( $current ); ?>">
		<aside class="fw-side">
			<a class="fw-brand" href="<?php echo esc_url( fswrt_page_url( 'overview' ) ); ?>">
				<?php fswrt_mark(); ?>
				<span>FusionWrite</span>
			</a>

			<nav class="fw-nav" aria-label="<?php esc_attr_e( 'FusionWrite pages', 'fusionwrite-ai' ); ?>" data-tour="nav">
				<?php foreach ( $pages as $page => $info ) : ?>
					<?php
					if ( $info['group'] !== $group ) {
						echo 0 === $group ? '<ul>' : '</ul><ul>';
						$group = $info['group'];
					}
					?>
					<li>
						<a class="fw-nav-link" href="<?php echo esc_url( fswrt_page_url( $page ) ); ?>" data-nav="<?php echo esc_attr( $page ); ?>"<?php echo $page === $current ? ' aria-current="page"' : ''; ?>>
							<?php fswrt_icon( $page ); ?>
							<span><?php echo esc_html( $info['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			</nav>

			<div class="fw-side-foot">
				<button type="button" class="fw-nav-link" data-fw-tour-start>
					<?php fswrt_icon( 'tour' ); ?>
					<span><?php esc_html_e( 'Take the tour', 'fusionwrite-ai' ); ?></span>
				</button>
				<a class="fw-nav-link" href="<?php echo esc_url( admin_url() ); ?>">
					<?php fswrt_icon( 'back' ); ?>
					<span><?php esc_html_e( 'Back to WordPress', 'fusionwrite-ai' ); ?></span>
				</a>
			</div>
		</aside>

		<main class="fw-main" id="fw-main">
			<?php require FSWRT_DIR . 'views/' . $current . '.php'; ?>
		</main>

		<div class="fw-toasts" id="fw-toasts" role="status" aria-live="polite"></div>
	</div>
	<?php
}

/**
 * Print a page heading.
 *
 * @param string $title Page title.
 * @param string $lede  One or two sentences under the title.
 */
function fswrt_page_head( $title, $lede = '' ) {
	echo '<header class="fw-head"><h1>' . esc_html( $title ) . '</h1>';
	if ( '' !== $lede ) {
		echo '<p class="fw-lede">' . esc_html( $lede ) . '</p>';
	}
	echo '</header>';
}

/**
 * Print a select box from a plain list of values.
 *
 * @param string   $id       Element id and name.
 * @param string[] $options  Values, used as labels too.
 * @param string   $selected Selected value.
 */
function fswrt_select( $id, $options, $selected ) {
	echo '<select class="fw-input fw-select" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">';
	foreach ( $options as $option ) {
		echo '<option value="' . esc_attr( $option ) . '"' . selected( $option, $selected, false ) . '>' . esc_html( $option ) . '</option>';
	}
	echo '</select>';
}
