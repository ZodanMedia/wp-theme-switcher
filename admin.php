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
            wp_enqueue_style( 'z-theme-switcher-admin-styles', esc_url($admin_css), array(), '1.0' );
        }
    }


}