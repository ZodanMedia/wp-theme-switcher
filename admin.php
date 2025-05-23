<?php

/**
 * Settings page for WP Theme Switcher
 *
 * Author: Zodan
 * Author URI: https://zodan.nl
 * License: GPL2+
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}





/*
 * 2. Register all settings
 *
 *
 */
if ( !function_exists( 'z_theme_switcher_register_settings' ) ) {

    function z_theme_switcher_register_settings() {
		
		$settings_args = array(
			'type' => 'array',
			'description' => '',
			'sanitize_callback' => 'z_theme_switcher_plugin_options_validate',
			'show_in_rest' => false
		);
        register_setting( 'z_theme_switcher_plugin_options', 'z_theme_switcher_plugin_options', $settings_args);

		// Voeg settings section toe
		add_settings_section(
			'z_theme_switcher_main_section',
			__('Global settings', 'z-theme-switcher'),
			'__return_false',
			'z_theme_switcher_plugin'
		);

        // Veld: Thema-selectie
		add_settings_field(
			'z_theme_switcher_select_theme',
			__('Theme to switch to', 'z-theme-switcher'),
			'z_theme_switcher_render_theme_dropdown',
			'z_theme_switcher_plugin',
			'z_theme_switcher_main_section'
		);

        // Veld: Rollen selectie
		add_settings_field(
			'z_theme_switcher_select_roles',
			__('Roles that can switch theme', 'z-theme-switcher'),
			'z_theme_switcher_render_roles_checkboxes',
			'z_theme_switcher_plugin',
			'z_theme_switcher_main_section'
		);
    }

    add_action( 'admin_init', 'z_theme_switcher_register_settings' );

    function z_theme_switcher_render_theme_dropdown() {
        $options = get_option( 'z_theme_switcher_plugin_options' );
        $selected_theme = isset( $options['theme'] ) ? $options['theme'] : '';
        $themes = wp_get_themes();

        echo '<select name="z_theme_switcher_plugin_options[theme]">';
        echo '<option value="">'. esc_html__('- no theme (disable the theme switcher)', 'z-theme-switcher') . '</option>';
        foreach ( $themes as $slug => $theme ) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $slug ),
                selected( $selected_theme, $slug, false ),
                esc_html( $theme->get( 'Name' ) )
            );
        }
        echo '</select>';
    }

    function z_theme_switcher_render_roles_checkboxes() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $options = get_option( 'z_theme_switcher_plugin_options' );
        $enabled_roles = isset( $options['roles'] ) ? $options['roles'] : array();

        foreach ( $roles as $role_slug => $role_details ) {
            printf(
                '<label><input type="checkbox" name="z_theme_switcher_plugin_options[roles][]" value="%s" %s> %s</label><br>',
                esc_attr( $role_slug ),
                checked( in_array( $role_slug, $enabled_roles ), true, false ),
                esc_html( $role_details['name'] )
            );
        }
    }

    function z_theme_switcher_plugin_options_validate( $input ) {
        $output = array();

        if ( isset( $input['theme'] ) ) {
            $themes = wp_get_themes();
            if ( array_key_exists( $input['theme'], $themes ) ) {
                $output['theme'] = sanitize_text_field( $input['theme'] );
            }
        }

        if ( isset( $input['roles'] ) && is_array( $input['roles'] ) ) {
            global $wp_roles;
            $all_roles = array_keys( $wp_roles->roles );
            $valid_roles = array_intersect( $input['roles'], $all_roles );
            $output['roles'] = array_map( 'sanitize_text_field', $valid_roles );
        }

        return $output;
    }

}


function z_theme_switcher_add_admin_menu() {
    add_options_page(
        __('WP Theme Switcher', 'z-theme-switcher'),
        'WP Theme Switcher',
        'manage_options',
        'z_theme_switcher',
        'z_theme_switcher_options_page'
    );
}
add_action( 'admin_menu', 'z_theme_switcher_add_admin_menu' );

function z_theme_switcher_options_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('WP Theme Switcher', 'z-theme-switcher'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'z_theme_switcher_plugin_options' );
            do_settings_sections( 'z_theme_switcher_plugin' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
