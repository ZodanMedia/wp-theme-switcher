<?php

/**
 * Settings page for Z WP Theme Switcher
 *
 * Author: Zodan
 * Author URI: https://zodan.nl
 * License: GPL2+
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}


if ( ! defined( 'Z_THEME_SWITCHER_VERSION' ) ) {
	define( 'Z_THEME_SWITCHER_VERSION', '1.3.1' );
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
			 esc_html__('Global settings', 'z-theme-switcher'),
			'z_theme_switcher_main_section_text',
			'z_theme_switcher_plugin'
		);

        // Field: Theme selection
		add_settings_field(
			'z_theme_switcher_select_theme',
			esc_html__('Theme to switch to', 'z-theme-switcher'),    
			'z_theme_switcher_render_theme_dropdown',
			'z_theme_switcher_plugin',
			'z_theme_switcher_main_section'
		);

        // Field: Role selection
		add_settings_field(
			'z_theme_switcher_select_roles',
			 esc_html__('Roles that can switch theme', 'z-theme-switcher') . '<span class="description">' .
                esc_html__( 'These roles will see the selected theme in the front-end.', 'z-theme-switcher' ) . '</span>', 
			'z_theme_switcher_render_roles_checkboxes',
			'z_theme_switcher_plugin',
			'z_theme_switcher_main_section'
		);

        // Field: Roles that can use the toggle
        add_settings_field(
            'z_theme_switcher_toggle_roles',
             esc_html__('Roles with toggle button', 'z-theme-switcher') . '<span class="description">' .
                wp_kses_post( sprintf(
                    /* translators: %s: The term for button, keep the span with btn-switch-faux in the $format. */
                    __( 'These roles will see a switch <span class="btn-switch-faux">%s</span> in the front-end', 'z-theme-switcher' ),
                    __( 'button', 'z-theme-switcher' )
                ) ),
            'z_theme_switcher_render_toggle_roles_checkboxes',
            'z_theme_switcher_plugin',
            'z_theme_switcher_main_section'
        );

        // Field: Role selection
		add_settings_field(
			'z_theme_switcher_permanent_roles',
			 esc_html__('Roles backend', 'z-theme-switcher') . '<span class="description">' .
                esc_html__( 'These roles will also have the selected theme in the back-end.', 'z-theme-switcher' ) . '</span>', 
			'z_theme_switcher_render_permanent_roles_checkboxes',
			'z_theme_switcher_plugin',
			'z_theme_switcher_main_section'
		);

		// Voeg settings section toe
		add_settings_section(
			'z_theme_switcher_faq_section',
			 esc_html__('Frequently asked questions', 'z-theme-switcher'),
			'z_theme_switcher_faq_section_text',
			'z_theme_switcher_plugin'
		);
    }

    add_action( 'admin_init', 'z_theme_switcher_register_settings' );



    function z_theme_switcher_main_section_text() { 
        echo '<p>' . esc_html__('Here you can set all the options for using the WordPress Theme Switcher.', 'z-theme-switcher') . '</p>';
        echo '<ol>';
        echo '<li>' . esc_html__('Select the theme that users will switch to.', 'z-theme-switcher') . '</li>';
        echo '<li>' . esc_html__('Select the user roles that automatically will see the selected theme on the front-end.', 'z-theme-switcher') . '</li>';
        echo '<li>' . esc_html__('Select the user roles that will see a toggle button to switch between the selected theme and the currently active theme.', 'z-theme-switcher') . '</li>';
        echo '</ol>';
        echo '<p>(' . esc_html__('Note that the switch button will override the automatic theme switch when a user role is selectd in both settings.', 'z-theme-switcher') . ')</p>';
        echo '<p>&nbsp;</p>';
    }

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

    function z_theme_switcher_render_toggle_roles_checkboxes() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $options = get_option( 'z_theme_switcher_plugin_options' );
        $enabled_roles = isset( $options['toggle_roles'] ) ? $options['toggle_roles'] : array();

        foreach ( $roles as $role_slug => $role_details ) {
            printf(
                '<label><input type="checkbox" name="z_theme_switcher_plugin_options[toggle_roles][]" value="%s" %s> %s</label><br>',
                esc_attr( $role_slug ),
                checked( in_array( $role_slug, $enabled_roles ), true, false ),
                esc_html( $role_details['name'] )
            );
        }
    }

    function z_theme_switcher_render_permanent_roles_checkboxes() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $options = get_option( 'z_theme_switcher_plugin_options' );
        $enabled_roles = isset( $options['roles_permanent'] ) ? $options['roles_permanent'] : array();

        foreach ( $roles as $role_slug => $role_details ) {
            printf(
                '<label><input type="checkbox" name="z_theme_switcher_plugin_options[roles_permanent][]" value="%s" %s> %s</label><br>',
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

        if ( isset( $input['toggle_roles'] ) && is_array( $input['toggle_roles'] ) ) {
            global $wp_roles;
            $all_roles = array_keys( $wp_roles->roles );
            $valid_toggle_roles = array_intersect( $input['toggle_roles'], $all_roles );
            $output['toggle_roles'] = array_map( 'sanitize_text_field', $valid_toggle_roles );
        }

        if ( isset( $input['roles_permanent'] ) && is_array( $input['roles_permanent'] ) ) {
            global $wp_roles;
            $all_roles = array_keys( $wp_roles->roles );
            $valid_roles = array_intersect( $input['roles_permanent'], $all_roles );
            $output['roles_permanent'] = array_map( 'sanitize_text_field', $valid_roles );
        }

        return $output;
    }


    function z_theme_switcher_faq_section_text() { 
        echo '<details class="z-ts-faq"><summary><h3>';
        esc_html_e('The Switch theme button on the front-end is not showing. What to do?', 'z-theme-switcher');
        echo '</h3></summary><p>';
        esc_html_e('Hm. It could be that you are using a theme that does not call wp_footer() (which is the hook it is linked to).', 'z-theme-switcher');
        echo '<br>';
        esc_html_e('In that case, you can use the custom hook/action for this.', 'z-theme-switcher');
        echo '<br>';
        esc_html_e('Just add the following php code (make sure it is somehow called on every page)', 'z-theme-switcher');
        echo ':</p>';
        echo '<code>&lt;?php do_action("z_theme_switcher_show_toggle"); ?&gt;</code>';
        echo '</details>';
    }
    

    function z_theme_switcher_add_admin_menu() {
        add_options_page(
            __('WP Theme Switcher', 'z-theme-switcher'),
            'Theme Switcher',
            'manage_options',
            'z_theme_switcher',
            'z_theme_switcher_options_page'
        );
    }
    add_action( 'admin_menu', 'z_theme_switcher_add_admin_menu' );


    function z_theme_switcher_options_page() {
        add_filter('admin_footer_text', 'z_theme_switcher_admin_footer_print_thankyou', 900);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Theme Switcher settings', 'z-theme-switcher'); ?></h1>
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


    /*
    * Enqueue scripts and styles
    *
    *
    */
    add_action( 'admin_enqueue_scripts', 'z_theme_switcher_add_admin_scripts' );
    function z_theme_switcher_add_admin_scripts( $hook ) {

        if ( is_admin() ) {

            $plugin_url = plugins_url( '/', __FILE__ );
            $admin_css = $plugin_url . 'assets/admin-styles.css';
            wp_enqueue_style( 'z-theme-switcher-admin-styles', esc_url($admin_css), array(), Z_THEME_SWITCHER_VERSION );

            $admin_js = $plugin_url . 'assets/admin-scripts.js';
            wp_register_script( 'z-theme-switcher-admin-scripts', esc_url( $admin_js ) , array( 'jquery' ), Z_THEME_SWITCHER_VERSION, array( 'in_footer' => true ) );
            wp_localize_script('z-theme-switcher-admin-scripts', 'z_theme_switcher_admin', array(
                    'copiedText' => esc_html__('PHP code copied!', 'z-theme-switcher'),
                )
            );
            wp_enqueue_script( 'z-theme-switcher-admin-scripts' );
        }
    }


    function z_theme_switcher_admin_footer_print_thankyou( $data ) {
            $data = '<p class="zThanks"><a href="https://zodan.nl" target="_blank" rel="noreferrer">' .
                        esc_html__('Made with', 'z-theme-switcher') . 
                        '<svg id="heart" data-name="heart" xmlns="http://www.w3.org/2000/svg" width="745.2" height="657.6" version="1.1" viewBox="0 0 745.2 657.6"><path class="heart" d="M372,655.6c-2.8,0-5.5-1.3-7.2-3.6-.7-.9-71.9-95.4-159.9-157.6-11.7-8.3-23.8-16.3-36.5-24.8-60.7-40.5-123.6-82.3-152-151.2C0,278.9-1.4,217.6,12.6,158.6,28,93.5,59,44.6,97.8,24.5,125.3,10.2,158.1,2.4,190.2,2.4s.3,0,.4,0c34.7,0,66.5,9,92.2,25.8,22.4,14.6,70.3,78,89.2,103.7,18.9-25.7,66.8-89,89.2-103.7,25.7-16.8,57.6-25.7,92.2-25.8,32.3-.1,65.2,7.8,92.8,22.1h0c38.7,20.1,69.8,69,85.2,134.1,14,59.1,12.5,120.3-3.8,159.8-28.5,69-91.3,110.8-152,151.2-12.8,8.5-24.8,16.5-36.5,24.8-88.1,62.1-159.2,156.6-159.9,157.6-1.7,2.3-4.4,3.6-7.2,3.6Z"></path></svg>' .
                        esc_html__('by Zodan', 'z-theme-switcher') .
                    '</a></p>';

            return $data;
        }

}