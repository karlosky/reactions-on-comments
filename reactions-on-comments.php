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
            add_filter( 'comment_text', array( $this, 'buttons' ), 10 );

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
            
            //default settings
            $default_data = array(
                'enabled' => true,
                'count_enabled' => true,
                'enabled_for_anonyms' => true,
            );
            update_option( 'roc_settings', $default_data );
            
            //default reactions
            //@todo: add icons urls
            $default_reactions = array(
                'Like' => ':smile:',
                'Cool' => ':grin:',
                'Lol' => ':lol:',
                'WOW' => ':shock:',
                'Sad' => ':sad:',
                'Cry' => ':cry:',
                'Angry' => ':evil:',
            );
            update_option( 'roc_reactions', $default_reactions );
            
        }
        
        
        /*
        * Add CSS and JS scripts on the front-end
        *
        * @since 0.0.1
        */
        public function add_scripts() {
        
            wp_enqueue_style( 'roc-style-css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), ROC_VERSION );
            wp_enqueue_script( 'roc-script-js', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array( 'jquery' ), ROC_VERSION );
            $localize = array(
            'ajax' => admin_url( 'admin-ajax.php' ),
        );
            wp_localize_script( 
                'roc-admin-ajax', 
                'roc_reaction', 
                array(
                    'ajax' => admin_url( 'admin-ajax.php' ),
                )
            );
            
        }
        
        
        /*
        * Ajax action
        *
        * @since 0.0.1
        */
        public function ajax() {
        
        }
        
        
        /*
        * Print section with the buttons
        * This is displayed on the bottom of each comment
        *
        * @since 0.0.1
        */
        public function buttons( $comment_text ) {
            $voted = 0;
            $reactions = $this->get_reactions();
            $type = $voted ? 'unvote' : 'vote';
            ob_start();
            ?>
                <div class="roc-reactions roc-reactions-post-<?php the_ID(); ?>-comment-<?php comment_ID(); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-nonce="<?php wp_create_nonce( '_roc_reaction_action' ); ?>" data-post="<?php the_ID(); ?>" data-comment="<?php comment_ID(); ?>">
                    <?php if ( $this->is_enabled() ) : ?>
                        <?php if ( is_user_logged_in() || !is_user_logged_in() && $this->is_enabled_for_anonymous() ) : ?>
                            <div class="roc-reactions-button">
                                <span class="roc-reactions-main-button">
                                    <?php echo wp_encode_emoji( $reactions['Like'] ); ?>
                                </span>
                                <div class="roc-reactions-box">
                                    <?php $i = 0; ?>
                                    <?php foreach ( $reactions as $reaction => $image ) : $i++; ?>
                                        <span class="roc-reaction roc-reaction-<?php echo sanitize_title( $reaction ); ?> roc-reaction-number-<?php echo $i; ?>"><?php echo wp_encode_emoji( $image ); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ( $this->is_counter_enabled() ) : ?>
                        <div class="roc-reactions-counter">
                            <?php echo $this->counter(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            $buttons = ob_get_contents();
            ob_get_clean();
            return $comment_text . $buttons;
        }
        
        
        /*
        * Print the counter
        * It's diplayed next to the buttons
        *
        * @since 0.0.1
        */
        public function counter() {
        
            // @todo: counter
            $reactions = $this->get_reactions();
            
            return 'Counter';
            
        }
        
        
        /*
        * Return all the reactions types
        *
        * @since 0.0.1
        */
        public function get_reactions() {
            
            $reactions = get_option( 'roc_reactions', array() );
            
            return $reactions;
        }
        
        /*
        * Check if reactions feature is enabled
        *
        * @since 0.0.1
        */
        public function is_enabled() {
            
            $settings = get_option( 'roc_settings', array() );
            
            return isset( $settings['count_enabled'] ) && $settings['count_enabled'] ? true : false;
        }
        
        
        /*
        * Check if counter is enabled
        *
        * @since 0.0.1
        */
        public function is_counter_enabled() {
        
            $settings = get_option( 'roc_settings', array() );
            
            return isset( $settings['count_enabled'] ) && $settings['count_enabled'] ? true : false;
        
        }
        
        
        /*
        * Check if reactions feature is enabled fot the anonymous
        *
        * @since 0.0.1
        */
        public function is_enabled_for_anonymous() {
        
            $settings = get_option( 'roc_settings', array() );
            
            return isset( $settings['enabled_for_anonyms'] ) && $settings['enabled_for_anonyms'] ? true : false;
        
        }
        
    }
    
    /*
    * Create plugin instance
    */
    $roc = new ROC_Plugin();

}
