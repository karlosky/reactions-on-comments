<?php
/*
  Plugin Name: Reactions on Comments
  Description: 
  Version: 0.0.1
  Author: Karol Sawka
  Author URI: http://karlosky.pl
*/

define( 'ROC_VERSION', '0.0.1' );

if ( !class_exists( 'ROC_Plugin') ) {

    class ROC_Plugin {
        
        public function __construct() {
            //hooks
            register_activation_hook( __FILE__, array( $this, 'set_defaults' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );

            // ajax actions
            add_action( 'wp_ajax_roc_reaction', array( $this, 'ajax' ) );
            add_action( 'wp_ajax_nopriv_roc_reaction', array( $this, 'ajax' ) );
        }
        
        
        /*
        * Set the default settings
        * 
        * @since 0.0.1 
        */        
        public function set_defaults() {
            $data = array(
                'enabled' => true,
                'count_enabled' => true,
                'enabled_for_anonyms' => true,
            );
            update_options( 'roc_settings', $data );
        }
        
        
        /*
        * Add CSS and JS scripts on the front-end
        *
        * @since 0.0.1
        */
        public function add_scripts() {
            wp_enqueue_style( 'roc-style-css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), ROC_VERSION );
            wp_enqueue_script( 'roc-script-js', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array( 'jquery' ), ROC_VERSION );
        }
        
        
        /*
        * Ajax action
        *
        * @since 0.0.1
        */
        public function ajax() {
        
        }
        
    }
    
    /*
    * Create plugin instance
    */
    $roc = new ROC_Plugin();

}
