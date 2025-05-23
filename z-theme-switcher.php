<?php
/**
 * Plugin Name: WP Theme Switcher
 * Contributors: martenmoolenaar
 * Plugin URI: https://speelwei.zodan.nl/wp-theme-switcher/
 * Tags: switch theme, theme development, development
 * Requires at least: 5.5
 * Tested up to: 6.8
 * Description: Switch temporarily and non-persistent to another active theme
 * Version: 1.0
 * Author: Zodan
 * Author URI: https://zodan.nl
 * Text Domain: z-theme-switcher
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 */




// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * Start: create an instance after the plugins have loaded
 * and call the switch when setting up the theme.
 * 
 */
add_action( 'plugins_loaded', function() {
	$instance = zSwitchTheme::get_instance();
	$instance->plugin_setup();
} );

add_action( 'setup_theme', function() {
	zSwitchTheme::get_instance()->maybe_enable_theme_switch();
} );







class zSwitchTheme {

	protected static $instance = NULL;
	public $plugin_version = '0.0.2';
	public $plugin_url = '';
	public $plugin_path = '';

	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	public function __construct() {}

	public function plugin_setup() {
		$this->plugin_url = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->load_language( 'z-theme-switcher' );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_plugin_settings_link' ] );

		if ( is_admin() ) {
			include( $this->plugin_path . 'admin.php' );
		}

		// Front-end only logic
		if ( ! is_admin() && is_user_logged_in() ) {
			add_action( 'wp_footer', [ $this, 'render_switch_theme_toggle' ] );
			// since the "none" them does not call wp_footer, we made a custom action
			add_action( 'z_theme_switcher_show_toggle', [ $this, 'render_switch_theme_toggle' ] );
			add_action( 'init', [ $this, 'handle_theme_cookie' ] );
		}
	}

	public function maybe_enable_theme_switch() {
		if ( ! is_admin() && is_user_logged_in() ) {
			$options = get_option( 'z_theme_switcher_plugin_options' );
			if ( empty( $options['theme'] ) ) return;
			if ( ! self::user_has_roles( $options['roles'] ) ) return;

			$override = isset( $_COOKIE['z_theme_switcher_override'] ) && $_COOKIE['z_theme_switcher_override'] === '1';

			if ( $override ) {
				add_filter( 'template', [ $this, 'filter_template' ], 99 );
				add_filter( 'stylesheet', [ $this, 'filter_stylesheet' ], 99 );
			}
		}
	}

	public function filter_template( $current ) {
		return $this->get_switched_theme_value( 'template', $current );
	}

	public function filter_stylesheet( $current ) {
		return $this->get_switched_theme_value( 'stylesheet', $current );
	}

	private function get_switched_theme_value( $type, $fallback ) {
		$options = get_option( 'z_theme_switcher_plugin_options' );
		if ( empty( $options['theme'] ) ) return $fallback;

		$theme = wp_get_theme( $options['theme'] );
		if ( ! $theme->exists() ) return $fallback;

		return $type === 'template' ? $theme->get_template() : $theme->get_stylesheet();
	}

	public static function user_has_roles( $roles = [] ) {
		if ( empty( $roles ) ) return false;
		$user = wp_get_current_user();
		return ! empty( array_intersect( $roles, (array) $user->roles ) );
	}

	public static function add_plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=z_theme_switcher">' . __( 'Settings','z-theme-switcher' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function load_language( $text_domain ) {
		load_plugin_textdomain( $text_domain, false, false );
	}


	public function handle_theme_cookie() {
		if ( isset( $_GET['z_theme_switcher'] ) ) {
			if ( $_GET['z_theme_switcher'] === 'default' ) {
				setcookie( 'z_theme_switcher_override', '0', time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
			} elseif ( $_GET['z_theme_switcher'] === 'alt' ) {
				setcookie( 'z_theme_switcher_override', '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
			}
			wp_redirect( remove_query_arg( 'z_theme_switcher' ) );
			exit;
		}
	}

	public function render_switch_theme_toggle() {
		$options = get_option( 'z_theme_switcher_plugin_options' );
		if ( empty( $options['theme'] ) || ! self::user_has_roles( $options['roles'] ) ) return;

		$is_alt = isset( $_COOKIE['z_theme_switcher_override'] ) && $_COOKIE['z_theme_switcher_override'] === '1';
		$url = add_query_arg( 'z_theme_switcher', $is_alt ? 'default' : 'alt' );
		$label = $is_alt ? __('Back to the standard theme', 'z-theme-switcher') : __('Show switched theme', 'z-theme-switcher');

		echo '<div style="position:fixed;bottom:20px;right:20px;z-index:9999;">
			<a href="' . esc_url( $url ) . '" style="background:#0073aa;color:white;padding:10px 14px;border-radius:6px;text-decoration:none;font-size:14px;font-family:sans-serif; &:hover { background:#222; }">
				' . esc_html( $label ) . '
			</a>
		</div>';
	}



}