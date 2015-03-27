<?php
/**
 * Plugin Name: WP Foundation Walker
 * Plugin URI:  https://github.com/PeterBooker/wp-foundation-walker
 * Description: A custom WordPress Menu Walker compatible with Foundation's Top Bar. Can be used as a Must Use (MU) plugin or included in a Theme directly.
 * Version:     1.0
 * Author:      Peter Booker
 * Author URI:  http://www.peterbooker.com
 * License:     GPLv2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Menu Walker for Foundation's Top Bar menu.
 * Foundation Docs - http://foundation.zurb.com/docs/components/topbar.html
 */
if ( ! class_exists( 'WP_Foundation_TopBar' ) ) {

    class WP_Foundation_TopBar extends Walker_Nav_Menu {

        /*
         * Default height of Foundation TopBar
         * Change if you customise the height
         */
        private static $height = '40px';

        /*
         * Add Top Bar specific CSS classes to menu items
         */
        function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

            $element->has_children = ! empty( $children_elements[ $element->ID ] );

            if ( ! empty( $element->classes ) ) {

                $element->classes[] = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
                $element->classes[] = ( $element->has_children ) ? 'has-dropdown' : '';

            }

            parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

        }

        /*
         * The start of the menu
         */
        function start_lvl( &$output, $depth = 0, $args = array() ) {

            $indent = str_repeat("\t", $depth);
            $output .= "\n" . $indent . "<ul class=\"sub-menu dropdown\">\n";

        }

        /*
         * The end of the menu
         */
        function end_lvl( &$output, $depth = 0, $args = array() ) {

            $indent = str_repeat( "\t", $depth );
            $output .= $indent . "</ul>\n";

        }

        /*
         * The start of each menu item
         */
        function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

            $item_output = '';
            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
            $output .= ( $depth == 0 ) ? '<li class="divider"></li>' : '';

            $classes = empty( $item->classes ) ? array() : ( array )$item->classes;
            $classes[] = 'menu-item-' . $item->ID;
            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
            $class_names = ' class="' . esc_attr( $class_names ) . '"';
            $output .= $indent . '<li id="menu-item-' . $item->ID . '" ' . $class_names . '>';

            if ( empty( $item->title ) && empty( $item->url ) ) {

                $item->url = get_permalink( $item->ID );
                $item->title = $item->post_title;
                $attributes = $this->build_attributes( $item );
                $item_output .= '<a' . $attributes . '>';
                $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
                $item_output .= '</a>';

            } else {

                $attributes = $this->build_attributes( $item );
                $item_output = $args->before;
                $item_output .= '<a' . $attributes . '>';
                $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                $item_output .= '</a>';
                $item_output .= $args->after;

            }

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id );

        }

        /*
         * The end of each menu item
         */
        function end_el( &$output, $item, $depth = 0, $args = array() ) {

            $output .= "</li>\n";

        }

        /*
         * Build attributes string
         */
        private function build_attributes( $item ) {

            $attributes = '';
            $attributes .= ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
            $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
            $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
            $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

            return $attributes;

        }

        /*
         * Provide fallback output incase no Menu is selected.
         */
        public static function fallback( $args = array() ) {

            $home_url = site_url( '/' );

            $admin_menu_url = admin_url( '/nav-menus.php' );

            $output = "<ul class=\"right\">\n";

            $output .= "<li class=\"menu-item\">\n";
            $output .= "<a href=\"{$home_url}\">Home</a>\n";
            $output .= "</li>\n";

            if ( current_user_can( 'manage_options' ) ) {

                $output .= "<li class=\"menu-item\">\n";
                $output .= "<a href=\"{$admin_menu_url}\">Customise Menu</a>\n";
                $output .= "</li>\n";

            }

            $output .= "</ul>";

            echo $output;

        }

        /*
         * Filter Menu Args relevant for this Walker
         */
        public static function menu_args( $args ) {

            $walker = new WP_Foundation_TopBar();

            if ( $walker == $args['walker'] ) {
                $args['container'] = false;
                $args['fallback_cb'] = 'WP_Foundation_TopBar::fallback';
            }

            unset( $walker );

            return $args;

        }

        /*
         * Sticky TopBar + WP Admin Bar Fix
         */
        public static function sticky_fix() {

            if ( ! is_admin() && is_admin_bar_showing() ) {

                remove_action( 'wp_head', '_admin_bar_bump_cb' );

                $output = '<style type="text/css">' . "\n\t";
                $output .= 'body.admin-bar #wpadminbar { position: fixed; }' . "\n\t";
                $output .= 'body.admin-bar { padding-top: 46px; }' . "\n\t";
                $output .= 'body.admin-bar .sticky.fixed { margin-top: 46px; }' . "\n\t";
                $output .= '@media ( min-width: 780px ) { body.admin-bar .sticky.fixed { margin-top: 32px; } }' . "\n\t";
                $output .= '@media ( min-width: 780px ) { body.admin-bar { padding-top: 32px; } }' . "\n\t";
                $output .= '</style>' . "\n";

                echo $output;

            }

        }

        /*
         * Fixed TopBar + WP Admin Bar Fix
         */
        public static function fixed_fix() {

            if ( ! is_admin() && is_admin_bar_showing() ) {

                remove_action( 'wp_head', '_admin_bar_bump_cb' );

                $height = WP_Foundation_TopBar::$height;

                $output = '<style type="text/css">' . "\n\t";
                $output .= 'body.admin-bar #wpadminbar { position: fixed; }' . "\n\t";
                $output .= 'body.admin-bar .fixed { margin-top: 46px; } body.admin-bar .fixed + div { margin-top: ' . $height . '; } body.admin-bar .fixed.expanded { margin-top: 0; }' . "\n\t";
                $output .= '@media ( min-width: 780px ) { body.admin-bar .fixed { margin-top: 32px; } body.admin-bar .fixed + div { margin-top: ' . $height . '; } body.admin-bar .fixed.expanded { margin-top: 0; } }' . "\n\t";
                $output .= '</style>' . "\n";

                echo $output;

            }

        }

    }
    // Force Certain Args for Compatibility
    add_filter( 'wp_nav_menu_args', array( 'WP_Foundation_TopBar', 'menu_args' ) );


    /*
     * Uncomment the relevant fix depending on your TopBar use, or add the CSS to your theme manually.
     */
    // Sticky TopBar + WP Admin Bar Fix
    add_action( 'wp_head', array( 'WP_Foundation_TopBar', 'sticky_fix' ), 5, 0 );
    // Fixed TopBar + WP Admin Bar Fix
    //add_action( 'wp_head', array( 'WP_Foundation_TopBar', 'fixed_fix' ), 5, 0 );

}