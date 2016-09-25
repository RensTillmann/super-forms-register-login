<?php
/**
 * Super Forms Register & Login
 *
 * @package   Super Forms Register & Login
 * @author    feeling4design
 * @link      http://codecanyon.net/item/super-forms-drag-drop-form-builder/13979866
 * @copyright 2015 by feeling4design
 *
 * @wordpress-plugin
 * Plugin Name: Super Forms Register & Login
 * Plugin URI:  http://codecanyon.net/item/super-forms-drag-drop-form-builder/13979866
 * Description: Makes it possible to let users register and login from the front-end
 * Version:     1.0.3
 * Author:      feeling4design
 * Author URI:  http://codecanyon.net/user/feeling4design
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('SUPER_Register_Login')) :


    /**
     * Main SUPER_Register_Login Class
     *
     * @class SUPER_Register_Login
     * @version	1.0.0
     */
    final class SUPER_Register_Login {
    
        
        /**
         * @var string
         *
         *	@since		1.0.0
        */
        public $version = '1.0.3';

        
        /**
         * @var SUPER_Register_Login The single instance of the class
         *
         *	@since		1.0.0
        */
        protected static $_instance = null;

        
        /**
         * Contains an array of registered script handles
         *
         * @var array
         *
         *	@since		1.0.0
        */
        private static $scripts = array();
        
        
        /**
         * Contains an array of localized script handles
         *
         * @var array
         *
         *	@since		1.0.0
        */
        private static $wp_localize_scripts = array();
        
        
        /**
         * Main SUPER_Register_Login Instance
         *
         * Ensures only one instance of SUPER_Register_Login is loaded or can be loaded.
         *
         * @static
         * @see SUPER_Register_Login()
         * @return SUPER_Register_Login - Main instance
         *
         *	@since		1.0.0
        */
        public static function instance() {
            if(is_null( self::$_instance)){
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        
        /**
         * SUPER_Register_Login Constructor.
         *
         *	@since		1.0.0
        */
        public function __construct(){
            $this->init_hooks();
            do_action('super_register_login_loaded');
        }

        
        /**
         * Define constant if not already set
         *
         * @param  string $name
         * @param  string|bool $value
         *
         *	@since		1.0.0
        */
        private function define($name, $value){
            if(!defined($name)){
                define($name, $value);
            }
        }

        
        /**
         * What type of request is this?
         *
         * string $type ajax, frontend or admin
         * @return bool
         *
         *	@since		1.0.0
        */
        private function is_request($type){
            switch ($type){
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return (!is_admin() || defined('DOING_AJAX')) && ! defined('DOING_CRON');
            }
        }

        
        /**
         * Hook into actions and filters
         *
         *	@since		1.0.0
        */
        private function init_hooks() {

            // Filters since 1.0.0
            add_filter( 'super_shortcodes_after_form_elements_filter', array( $this, 'add_activation_code_element' ), 10, 2 );

            // Filters since 1.0.3
            add_filter( 'wp_authenticate_user', array( $this, 'check_user_login_status' ), 10, 2 );

            // Actions since 1.0.0
            add_action( 'wp_ajax_super_resend_activation', array( $this, 'resend_activation' ) );
            add_action( 'wp_ajax_nopriv_super_resend_activation', array( $this, 'resend_activation' ) );


            if ( $this->is_request( 'frontend' ) ) {
                
                // Filters since 1.0.0

                // Actions since 1.0.0
                add_action( 'super_before_printing_message', array( $this, 'resend_activation_code_script' ) );

            }
            
            if ( $this->is_request( 'admin' ) ) {
                
                // Filters since 1.0.0
                add_filter( 'super_settings_after_smtp_server_filter', array( $this, 'add_settings' ), 10, 2 );
                add_filter( 'super_email_tags_filter', array( $this, 'add_email_tags' ), 10, 1 );

                // Actions since 1.0.0
                add_action( 'super_before_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms' ) );
                add_action( 'super_after_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms_json' ) );

                // Actions since 1.0.3
                add_action( 'show_user_profile', array( $this, 'add_customer_meta_fields' ) );
                add_action( 'edit_user_profile', array( $this, 'add_customer_meta_fields' ) );
                add_action( 'personal_options_update', array( $this, 'save_customer_meta_fields' ) );
                add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) );


            }
            
            if ( $this->is_request( 'ajax' ) ) {

                // Filters since 1.0.0

                // Actions since 1.0.0
                add_action( 'super_before_sending_email_hook', array( $this, 'before_sending_email' ) );

            }
            
        }

        /**
         * Add extra auth login check based on user login status
         *
         * @since      1.0.3
         */
        public function check_user_login_status( $user, $password ) {
            
            // Now check if the login status of the user is pending or blocked
            $user_login_status = get_user_meta( $user->ID, 'super_user_login_status', true );
            if( ($user_login_status=='pending') || ($user_login_status=='blocked') ) {
                remove_action('authenticate', 'wp_authenticate_username_password', 20);
                $user = new WP_Error( 'account_not_active', __( '<strong>ERROR</strong>: You are not allowed to login.', 'super-forms' ) );
            }
            return $user;
        }


        /**
         * Get Status Field for the edit user pages.
         *
         * @since      1.0.3
         */
        public function get_customer_meta_fields() {
            $fields = array(
                'super_user_login_status' => array(
                    'title' => __( 'Super Forms - User Status', 'super-forms' ),
                    'fields' => array(
                        'super_user_login_status' => array(
                            'label' => __( 'User Status', 'super-forms' ),
                            'description' => __( 'When set to pending/blocked user won\'t be able to login', 'super-forms' ),
                            'type' => 'select',
                            'options' => array(
                                'active' => __( 'Active', 'super-forms' ),
                                'pending' => __( 'Pending', 'super-forms' ),
                                'blocked' => __( 'Blocked', 'super-forms' ),
                            )
                        ),
                    )
                ),
            );
            return $fields;
        }


        /**
         * Show Status Field on edit user pages.
         *
         * @param WP_User $user
         * @since      1.0.3
         */
        public function add_customer_meta_fields( $user ) {
            $show_fields = $this->get_customer_meta_fields();
            foreach( $show_fields as $fieldset ) {
                echo '<h3>' . $fieldset['title'] . '</h3>';
                echo '<table class="form-table">';
                foreach( $fieldset['fields'] as $key => $field ) {
                    echo '<tr>';
                        echo '<th>';
                            echo '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label>';
                        echo '</th>';
                        echo '<td>';
                            if ( ! empty( $field['type'] ) && 'select' == $field['type'] ) {
                                echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="' . ( ! empty( $field['class'] ) ? $field['class'] : '' ) . '" style="width: 25em;">';
                                $selected = esc_attr( get_user_meta( $user->ID, $key, true ) );
                                foreach( $field['options'] as $option_key => $option_value ) {
                                    echo '<option value="' . esc_attr( $option_key ) . '" ' . selected( $selected, $option_key, true ) . '>' . esc_attr( $option_value ) . '</option>';
                                }
                                echo '</select>';
                            }else{
                                echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( get_user_meta( $user->ID, $key, true ) ) . '" class="' . ( ! empty( $field['class'] ) ? $field['class'] : 'regular-text' ) . '" />';
                            }
                            echo '<br/>';
                            echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
                        echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }


        /**
         * Save Address Fields on edit user pages.
         *
         * @param int $user_id User ID of the user being saved
         * @since      1.0.3
         */
        public function save_customer_meta_fields( $user_id ) {
            $save_fields = $this->get_customer_meta_fields();
            foreach ( $save_fields as $fieldset ) {
                foreach ( $fieldset['fields'] as $key => $field ) {
                    if ( isset( $_POST[ $key ] ) ) {
                        update_user_meta( $user_id, $key, wc_clean( $_POST[ $key ] ) );
                    }
                }
            }
        }

        
        /**
         * Hook into outputting the message and make sure to add the resend activation javascript
         *
         *  @since      1.0.0
        */
        public static function resend_activation_code_script( $data ) {
            $settings = get_option( 'super_settings' );
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $handle = 'super-register-common';
            $name = str_replace( '-', '_', $handle ) . '_i18n';
            wp_register_script( $handle, plugin_dir_url( __FILE__ ) . 'assets/js/frontend/common' . $suffix . '.js', array( 'jquery' ), SUPER_Register_Login()->version, false );  
            wp_localize_script( $handle, $name, array( 'ajaxurl'=>SUPER_Forms()->ajax_url(), 'duration'=>absint( $settings['form_duration'] ) ) );
            wp_enqueue_script( $handle );
        }


        /**
         * Hook into the load form dropdown and add some ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms() {
            $html  = '<option value="register-login-register">Register & Login - Registration form</option>';
            $html .= '<option value="register-login-login">Register & Login - Login form</option>';
            $html .= '<option value="register-login-password">Register & Login - Lost password form</option>';
            echo $html;
        }


        /**
         * Hook into the after load form dropdown and add the json of the ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms_json() {
            $html  = '<textarea hidden name="register-login-register">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"user_login","email":"Username","label":"","description":"","placeholder":"Username","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"username","logic":"contains","value":""}]}},{"tag":"text","group":"form_elements","inner":"","data":{"name":"user_email","email":"Email","label":"","description":"","placeholder":"Email","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"user_login","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="register-login-login">';
            $html .= '[{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"user_login","email":"Username","label":"","description":"","placeholder":"Username","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}}],"data":{"size":"1/1","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"password","group":"form_elements","inner":"","data":{"name":"user_pass","email":"Password","label":"","description":"","placeholder":"Password","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"lock","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"user_login","logic":"contains","value":""}]}}],"data":{"size":"1/1","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"activation_code","group":"form_elements","inner":"","data":{"label":"","description":"","placeholder":"[-CODE-]","tooltip":"","grouped":"0","width":"150","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"code","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"user_login","logic":"contains","value":""}]}}],"data":{"size":"1/1","margin":"","conditional_action":"disabled"}},{"tag":"html","group":"form_elements","inner":"","data":{"title":"","subtitle":"","html":"<a style=\"display:block;float:right;\" href=\"http://f4d.nl/dev/lost-password/\">Lost Password?</a>","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"user_login","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="register-login-password">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"user_email","email":"Email","label":"","description":"","placeholder":"Email address","tooltip":"","validation":"email","error":"Please enter a valid email address!","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"login_email","logic":"contains","value":""}]}},{"tag":"html","group":"form_elements","inner":"","data":{"title":"","subtitle":"","html":"<a style=\"display:block;float:right;\" href=\"http://f4d.nl/dev/login/\">Return to login page</a>","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"user_email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';
            echo $html;
        }


        /**
         * Hook into the default email tags and add extra tags that can be used in our Activation email
         *
         *  @since      1.0.0
        */
        public static function add_email_tags( $tags ) {
            $tags['register_login_url'] = array(
                __( 'Retrieves the login page URL', 'super-forms' ),
                ''
            );
            $tags['register_activation_code'] = array(
                __( 'Retrieves the activation code', 'super-forms' ),
                ''
            );
            $tags['register_generated_password'] = array(
                __( 'Retrieves the generated password', 'super-forms' ),
                ''
            );
            return $tags;
        }


        /**
         * Handle the Activation Code element output
         *
         *  @since      1.0.0
        */
        public static function activation_code( $tag, $atts, $inner, $shortcodes=null, $settings=null ) {
            
            $return = false;
            if( ( SUPER_Forms::is_request( 'frontend' ) ) && ( isset( $_GET['code'] ) ) ) {
                $code = sanitize_text_field( $_GET['code'] );
                $return = true;
            }
            if ( SUPER_Forms::is_request( 'admin' ) ) {
                $code = '';
                $return = true;
            }
            if( $return==true ) {
                $atts['name'] = 'activation_code';
                $result = SUPER_Shortcodes::opening_tag( $tag, $atts );
                $result .= SUPER_Shortcodes::opening_wrapper( $atts, $inner, $shortcodes, $settings );
                $result .= '<input class="super-shortcode-field" type="text"';
                $result .= ' name="' . $atts['name'] . '" value="' . $code . '"';
                $result .= SUPER_Shortcodes::common_attributes( $atts, $tag );
                $result .= ' />';
                $result .= '</div>';
                $result .= SUPER_Shortcodes::loop_conditions( $atts );
                $result .= '</div>';
                return $result;
            }

        }


        /**
         * Hook into elements and add Activation Code element
         * This element will show the activation code input field when it has been set in the URL parameter
         *
         *  @since      1.0.0
        */
        public static function add_activation_code_element( $array, $attributes ) {

            // Include the predefined arrays
            require(SUPER_PLUGIN_DIR.'/includes/shortcodes/predefined-arrays.php' );

            $array['form_elements']['shortcodes']['activation_code'] = array(
                'callback' => 'SUPER_Register_Login::activation_code',
                'name' => __( 'Activation Code', 'super-forms' ),
                'icon' => 'code',
                'atts' => array(
                    'general' => array(
                        'name' => __( 'General', 'super-forms' ),
                        'fields' => array(
                            'label' => $label,
                            'description'=> $description,
                            'placeholder' => SUPER_Shortcodes::placeholder( $attributes, '[-CODE-]' ),
                            'tooltip' => $tooltip,
                        )
                    ),
                    'advanced' => array(
                        'name' => __( 'Advanced', 'super-forms' ),
                        'fields' => array(
                            'grouped' => $grouped,                    
                            'width' => $width,
                            'exclude' => $exclude, 
                            'error_position' => $error_position_left_only,
                        ),
                    ),
                    'icon' => array(
                        'name' => __( 'Icon', 'super-forms' ),
                        'fields' => array(
                            'icon_position' => $icon_position,
                            'icon_align' => $icon_align,
                            'icon' => SUPER_Shortcodes::icon( $attributes, 'code' ),
                        ),
                    ),
                    'conditional_logic' => $conditional_logic_array
                ),
            );
            return $array;
        }


        /**
         * Hook into settings and add Register & Login settings
         *
         *  @since      1.0.0
        */
        public static function add_settings( $array, $settings ) {
            global $wp_roles;
            $all_roles = $wp_roles->roles;
            $editable_roles = apply_filters( 'editable_roles', $all_roles );
            $roles = array(
                '' => __( 'All user roles', 'super-forms' )
            );
            foreach( $editable_roles as $k => $v ) {
                $roles[$k] = $v['name'];
            }
            $reg_roles = $roles;
            unset($reg_roles['']);
            $array['register_login'] = array(        
                'name' => __( 'Register & Login', 'super-forms' ),
                'label' => __( 'Register & Login Settings', 'super-forms' ),
                'fields' => array(
                    'register_login_action' => array(
                        'name' => __( 'Actions', 'super-forms' ),
                        'desc' => __( 'Select what this form should do (register or login)?', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_login_action', $settings['settings'], 'none' ),
                        'filter' => true,
                        'type' => 'select',
                        'values' => array(
                            'none' => __( 'None (do nothing)', 'super-forms' ),
                            'register' => __( 'Register a new user', 'super-forms' ),
                            'login' => __( 'Login (user will be logged in)', 'super-forms' ),
                            'reset_password' => __( 'Reset password (lost password)', 'super-forms' ),
                        ),
                    ),
                    'login_user_role' => array(
                        'name' => __( 'Allowed user role(s)', 'super-forms' ),
                        'desc' => __( 'Which user roles are allowed to login?', 'super-forms' ),
                        'type' => 'select',
                        'multiple' => true,
                        'default' => SUPER_Settings::get_value( 0, 'login_user_role', $settings['settings'], '' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'login',
                        'values' => $roles,
                    ),
                    'register_user_role' => array(
                        'name' => __( 'User role', 'super-forms' ),
                        'desc' => __( 'What user role should this user get?', 'super-forms' ),
                        'type' => 'select',
                        'default' => SUPER_Settings::get_value( 0, 'register_user_role', $settings['settings'], '' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                        'values' => $reg_roles,
                    ),
                    'register_user_signup_status' => array(
                        'name' => __( 'User login status after registration', 'super-forms' ),
                        'desc' => __( 'The login status the user should get after completed registration.', 'super-forms' ),
                        'type' => 'select',
                        'default' => SUPER_Settings::get_value( 0, 'register_user_signup_status', $settings['settings'], 'active' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                        'values' => array(
                            'active' => __( 'Active (default)', 'super-forms' ),
                            'pending' => __( 'Pending', 'super-forms' ),
                            'blocked' => __( 'Blocked', 'super-forms' ),
                        ),
                    ),
                    'register_login_activation' => array(
                        'name' => __( 'Send activation email', 'super-forms' ),
                        'desc' => __( 'Optionally let users activate their account or let them instantly login without verification', 'super-forms' ),
                        'type' => 'select',
                        'default' => SUPER_Settings::get_value( 0, 'register_login_activation', $settings['settings'], 'verify' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                        'values' => array(
                            'verify' => __( 'Send activation email', ' super' ),
                            'auto' => __( 'Auto activate and login (login status will also be updated to: active)', 'super-forms' ),
                            'activate' => __( 'Auto activate but don\'t login automatically', 'super-forms' ),
                            'none' => __( 'Do nothing (don\'t send email and don\'t activate)', 'super-forms' ),
                        ),
                    ),
                    'register_login_url' => array(
                        'name' => __( 'Login page URL', 'super-forms' ),
                        'desc' => __( 'URL of your login page where you placed the login form, here users can activate their account', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_login_url', $settings['settings'], get_site_url() . '/login/' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register,login,reset_password',
                    ),
                    'register_welcome_back_msg' => array(
                        'name' => __( 'Welcome back message', 'super-forms' ),
                        'desc' => __( 'Display a welcome message after user has logged in (leave blank for no message)', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_welcome_back_msg', $settings['settings'], __( 'Welcome back {field_user_login}!', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'login',
                    ),
                    'register_incorrect_code_msg' => array(
                        'name' => __( 'Incorrect activation code message', 'super-forms' ),
                        'desc' => __( 'Display a message when the activation code is incorrect', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_incorrect_code_msg', $settings['settings'], __( 'The combination username, password and activation code is incorrect!', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'login',
                    ),
                    'register_account_activated_msg' => array(
                        'name' => __( 'Account activated message', 'super-forms' ),
                        'desc' => __( 'Display a message when account has been activated', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_account_activated_msg', $settings['settings'], __( 'Hello {field_user_login}, your account has been activated!', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'login',
                    ),
                    'register_activation_subject' => array(
                        'name' => __( 'Activation Email Subject', 'super-forms' ),
                        'desc' => __( 'Example: Activate your account', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_activation_subject', $settings['settings'], __( 'Activate your account', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_activation',
                        'filter_value' => 'verify',
                    ),
                    'register_activation_email' => array(
                        'name' => __( 'Activation Email Body', 'super-forms' ),
                        'desc' => __( 'The email message. You can use {activation_code} and {register_login_url}', 'super-forms' ),
                        'type' => 'textarea',
                        'default' => SUPER_Settings::get_value( 0, 'register_activation_email', $settings['settings'], "Dear {field_user_login},\n\nThank you for registering! Before you can login you will need to activate your account.\nBelow you will find your activation code. You need this code to activate your account:\n\nActivation Code: <strong>{register_activation_code}</strong>\n\nClick <a href=\"{register_login_url}?code={register_activation_code}\">here</a> to activate your account with the provided code.\n\n\nBest regards,\n\n{option_blogname}" ),
                        'filter' => true,
                        'parent' => 'register_login_activation',
                        'filter_value' => 'verify',
                    ),                                      
                    'register_login_user_meta' => array(
                        'name' => __( 'Save custom user meta', 'super-forms' ),
                        'desc' => __( 'Usefull for external plugins such as WooCommerce. Example: "field_name|meta_key" (each on a new line)', 'super-forms' ),
                        'type' => 'textarea',
                        'default' => SUPER_Settings::get_value( 0, 'register_login_user_meta', $settings['settings'], "first_name|billing_first_name\nlast_name|billing_last_name\naddress|billing_address" ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                    ),
                    'register_reset_password_success_msg' => array(
                        'name' => __( 'Success message', 'super-forms' ),
                        'desc' => __( 'Display a message after user has reset their password (leave blank for no message)', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_reset_password_success_msg', $settings['settings'], __( 'Your password has been reset. We have just send you a new password to your email address.', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'reset_password',
                    ),
                    'register_reset_password_not_exists_msg' => array(
                        'name' => __( 'Not found message', 'super-forms' ),
                        'desc' => __( 'Display a message when no user was found (leave blank for no message)', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_reset_password_not_exists_msg', $settings['settings'], __( 'We couldn\'t find a user with the given email address!', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'reset_password',
                    ),
                    'register_reset_password_subject' => array(
                        'name' => __( 'Lost Password Email Subject', 'super-forms' ),
                        'desc' => __( 'Example: Your new password. You can use {user_login}', 'super-forms' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_reset_password_subject', $settings['settings'], __( 'Your new password', 'super-forms' ) ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'reset_password',
                    ),
                    'register_reset_password_email' => array(
                        'name' => __( 'Lost Password Email Body', 'super-forms' ),
                        'desc' => __( 'The email message. You can use {user_login}, {register_generated_password} and {register_login_url}', 'super-forms' ),
                        'type' => 'textarea',
                        'default' => SUPER_Settings::get_value( 0, 'register_reset_password_email', $settings['settings'], "Dear {user_login},\n\nYou just requested to reset your password.\nUsername: <strong>{user_login}</strong>\nPassword: <strong>{register_generated_password}</strong>\n\nClick <a href=\"{register_login_url}\">here</a> to login with your new password.\n\n\nBest regards,\n\n{option_blogname}" ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'reset_password',
                    ),
                )
            );
            return $array;
        }


        /**
         * Hook into before sending email and check if we need to register or login a user
         *
         *  @since      1.0.0
        */
        public static function before_sending_email( $atts ) {
            $data = $atts['post']['data'];
            $settings = $atts['settings'];
            
            if( !isset( $settings['register_login_action'] ) ) return true;
            if( $settings['register_login_action']=='none' ) return true;

            if( $settings['register_login_action']=='register' ) {

                // Before we proceed, lets check if we have at least a user_login and user_email field
                if( ( !isset( $data['user_login'] ) ) && ( !isset( $data['user_email'] ) ) ) {
                    $msg = __( 'We couldn\'t find the <strong>user_login</strong> and <strong>user_email</strong> fields which are required in order to register a new user. Please <a href="' . get_admin_url() . 'admin.php?page=super_create_form&id=' . absint( $atts['post']['form_id'] ) . '">edit</a> your form and try again', 'super-forms' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }

                // Now lets check if a user already exists with the same user_login or user_email
                $user_login = sanitize_user( $data['user_login']['value'] );
                $user_email = sanitize_email( $data['user_email']['value'] );
                
                $username_exists = username_exists($user_login);
                if( $username_exists!=false ) {
                    $user = get_user_by( 'login', $user_login );
                    $user_login_status = get_user_meta( $user->ID, 'super_user_login_status', true );
                    if( ($user_login_status=='active') || ($user_login_status=='') ) {
                        $username_exists = true;
                    }else{
                        wp_delete_user( $user->ID );
                        $username_exists = false;
                    }
                }

                $email_exists = email_exists($user_email);        
                if( $email_exists!=false ) {
                    $user = get_user_by( 'email', $user_email );
                    $user_login_status = get_user_meta( $user->ID, 'super_user_login_status', true );
                    if( ($user_login_status=='active') || ($user_login_status=='') ) {
                        $email_exists = true;
                    }else{
                        wp_delete_user( $user->ID );
                        $email_exists = false;
                    }
                }

                if( ( $username_exists!=false ) || ( $email_exists!=false ) ) {
                    $msg = __( 'Username or Email address already exists, please try again', 'super-forms' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null,
                        $fields = array(
                            'user_login' => 'input',
                            'user_pass' => 'input'
                        )
                    );
                }

                // If user_pass field doesn't exist, we can generate one and send it by email to the registered user
                $send_password = false;
                if( !isset( $data['user_pass'] ) ) {
                    $send_password = true;
                    $password = wp_generate_password();
                }else{
                    $password = $data['user_pass']['value'];
                }

                // Lets gather all data that we need to insert for this user
                $userdata = array();
                $userdata['user_login'] = $user_login;
                $userdata['user_email'] = $user_email;
                $userdata['user_pass'] = $password;
                $userdata['role'] = $settings['register_user_role'];
                $userdata['user_registered'] = date('Y-m-d H:i:s');
                $userdata['show_admin_bar_front'] = 'false';

                // Also loop through some of the other default user data that WordPress provides us with out of the box
                $other_userdata = array(
                    'user_nicename',
                    'user_url',
                    'display_name',
                    'nickname',
                    'first_name',
                    'last_name',
                    'description',
                    'rich_editing',
                    'role', // This is in case we have a custom dropdown with the name "role" which allows users to select their own account type/role
                    'jabber',
                    'aim',
                    'yim'
                );
                foreach( $other_userdata as $k ) {
                    if( isset( $data[$k]['value'] ) ) {
                        $userdata[$k] = $data[$k]['value'];
                    }
                }

                // Insert the user and return the user ID
                $user_id = wp_insert_user( $userdata );
                if( is_wp_error( $user_id ) ) {
                    $msg = __( 'Something went wrong while registering your acount, please try again', 'super-forms' );
                    $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }

                // @since v1.0.3 - currently used by the WooCommerce Checkout Add-on
                do_action( 'super_after_wp_insert_user_action', array( 'user_id'=>$user_id, 'atts'=>$atts ) );

                // Save custom user meta
                $meta_data = array();
                $custom_user_meta = explode( "\n", $settings['register_login_user_meta'] );
                foreach( $custom_user_meta as $k ) {
                    $field = explode( "|", $k );
                    if( isset( $data[$field[0]]['value'] ) ) {
                        $meta_data[$field[1]] = $data[$field[0]]['value'];
                    }
                }
                foreach( $meta_data as $k => $v ) {
                    update_user_meta( $user_id, $k, $v ); 
                }

                // @since 1.0.3
                if( !isset($settings['register_user_signup_status']) ) $settings['register_user_signup_status'] = 'active';
                update_user_meta( $user_id, 'super_user_login_status', $settings['register_user_signup_status'] );

                // Check if we need to send an activation email to this user
                if( $settings['register_login_activation']=='verify' ) {
                    $code = wp_generate_password( 8, false );
                    update_user_meta( $user_id, 'super_account_status', 0 ); // 0 = inactive, 1 = active
                    update_user_meta( $user_id, 'super_account_activation', $code ); 
                    $user = get_user_by( 'id', $user_id );

                    // Replace email tags with correct data
                    $subject = SUPER_Common::email_tags( $settings['register_activation_subject'], $data, $settings, $user );
                    $message = $settings['register_activation_email'];
                    $message = str_replace( '{register_login_url}', $settings['register_login_url'], $message );
                    $message = str_replace( '{register_activation_code}', $code, $message );
                    $message = str_replace( '{register_generated_password}', $password, $message );
                    $message = SUPER_Common::email_tags( $message, $data, $settings, $user );
                    $message = nl2br( $message );
                    $from = SUPER_Common::email_tags( $settings['header_from'], $data, $settings, $user );
                    $from_name = SUPER_Common::email_tags( $settings['header_from_name'], $data, $settings, $user );

                    // Send the email
                    $mail = SUPER_Common::email( $user_email, $from, $from_name, '', '', $subject, $message, $settings );

                    // Return message
                    if( !empty( $mail->ErrorInfo ) ) {
                        SUPER_Common::output_error(
                            $error = true,
                            $msg = $mail->ErrorInfo,
                            $redirect = null
                        );
                    }
                }
                
                // Check if we let users automatically login after registering (instant login)
                if( $settings['register_login_activation']=='auto' ) {
                    wp_set_current_user( $user_id );
                    wp_set_auth_cookie( $user_id );
                    update_user_meta( $user_id, 'super_last_login', time() );
                    update_user_meta( $user_id, 'super_account_status', 1 );
                    update_user_meta( $user_id, 'super_user_login_status', 'active' );
                }

                // Check if automatically activate users
                if( $settings['register_login_activation']=='activate' ) {
                    update_user_meta( $user_id, 'super_account_status', 1 );
                }


            }

            if( $settings['register_login_action']=='login' ) {

                // Before we proceed, lets check if we have at least a user_login or user_email and user_pass field
                if( ( !isset( $data['user_login'] ) ) && ( !isset( $data['user_pass'] ) ) ) {
                    $msg = __( 'We couldn\'t find the <strong>user_login</strong> or <strong>user_pass</strong> fields which are required in order to login a new user. Please <a href="' . get_admin_url() . 'admin.php?page=super_create_form&id=' . absint( $atts['post']['form_id'] ) . '">edit</a> your form and try again', 'super-forms' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }
                $username = sanitize_user( $data['user_login']['value'] );
                $password = $data['user_pass']['value'];
                $creds = array();
                $creds['user_login'] = $username;
                $creds['user_password'] = $password;
                $creds['remember'] = true;
                $user = wp_signon( $creds, false );
                if( !is_wp_error( $user ) ) {
                    $user_id = $user->ID;
                    $user = get_user_by( 'id', $user_id );
                    if( $user ) {

                        // First check if the user role is allowed to login
                        $allowed = false;

                        if( ( !isset( $settings['login_user_role'] ) ) || ( $settings['login_user_role']=='' ) ) {
                            $allowed = true;
                        }else{
                            $allowed = in_array( $user->roles[0], $settings['login_user_role'] );
                            if( in_array( '', $settings['login_user_role'] ) ) {
                                $allowed = true;
                            }
                        }                        

                        if( $allowed != true ) {
                            wp_logout();
                            $msg = __( 'You are not allowed to login!', 'super-forms' );
                            SUPER_Common::output_error(
                                $error = true,
                                $msg = $msg,
                                $redirect = null
                            );
                        }

                        // Check if user has not activated their account yet
                        $activated = null;
                        $status = get_user_meta( $user_id, 'super_account_status', true ); // 0 = inactive, 1 = active
                        // Maybe this user was already registered before Super Forms was used, if so skip the test
                        if( ( !isset( $data['activation_code'] ) ) && ( $status==0 ) && ( $status!='' ) ) {
                            wp_logout();
                            $msg = sprintf( __( 'You haven\'t activated your account yet. Please check your email or click <a href="#" class="resend-code" data-form="' . absint( $atts['post']['form_id'] ) . '" data-user="' . $username . '">here</a> to resend your activation email.', 'super-forms' ), $user->user_login );
                            $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
                            SUPER_Common::output_error(
                                $error = true,
                                $msg = $msg,
                                $redirect = $settings['register_login_url'] . '?code=[%20CODE%20]&user=' . $username
                            );
                        }

                        // Validate the activation code
                        if( isset( $data['activation_code'] ) ) {    
                            if( $status==0 ) {
                                $code = sanitize_text_field( $data['activation_code']['value'] );
                                $activation = get_user_meta( $user_id, 'super_account_activation', true );
                                if( $code==$activation ) {
                                    update_user_meta( $user_id, 'super_account_status', 1 ); // 0 = inactive, 1 = active
                                    delete_user_meta( $user_id, 'super_account_activation' );
                                    $activated = true;
                                }else{
                                    $activated = false;
                                }
                            }
                            if( $status==1 ) {
                                $activated = true;
                            }
                        }
                        $msg = '';
                        if( ( isset( $settings['register_welcome_back_msg'] ) ) && ( $settings['register_welcome_back_msg']!='' ) ) {
                            $msg = SUPER_Common::email_tags( $settings['register_welcome_back_msg'], $data, $settings, $user );
                        }
                        $error = false;

                        $redirect = get_site_url();
                        if( !empty( $settings['form_redirect_option'] ) ) {
                            if( $settings['form_redirect_option']=='page' ) {
                                $redirect = get_permalink( $settings['form_redirect_page'] );
                            }
                            if( $settings['form_redirect_option']=='custom' ) {
                                $redirect = $settings['form_redirect'];
                            }
                        }
                        if( $activated!=false ) {
                            if( $activated==false ) {
                                wp_logout();
                                $msg = SUPER_Common::email_tags( $settings['register_incorrect_code_msg'], $data, $settings, $user );
                                $error = true;
                                $redirect = null;
                            }else{
                                wp_set_current_user($user_id);
                                wp_set_auth_cookie($user_id);
                                $msg = SUPER_Common::email_tags( $settings['register_account_activated_msg'], $data, $settings, $user );
                            }
                        }else{
                            wp_set_current_user($user_id);
                            wp_set_auth_cookie($user_id);
                        }
                        $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'success' );
                        SUPER_Common::output_error(
                            $error = $error,
                            $msg = $msg,
                            $redirect = $redirect
                        );
                    }
                }else{
                    wp_logout();
                    if( count( $user->errors ) > 0 ) {
                        $errors = $user->errors;
                        $errors = array_values( $errors );
                        $errors = array_shift( $errors );
                        $msg = $errors[0];
                    }else{
                        $msg = __( '<strong>Error:</strong> Something went wrong while logging in, please try again', 'super-forms' );
                    }
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }
            }

            if( $settings['register_login_action']=='reset_password' ) {
   
                // Before we proceed, lets check if we have at least a user_email field
                if( !isset( $data['user_email'] ) ) {
                    $msg = __( 'We couldn\'t find the <strong>user_email</strong> field which is required in order to reset passwords. Please <a href="' . get_admin_url() . 'admin.php?page=super_create_form&id=' . absint( $atts['post']['form_id'] ) . '">edit</a> your form and try again', 'super-forms' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }

                // Sanitize the user email address
                $user_email = sanitize_email( $data['user_email']['value'] );
                
                // Try to find a user with this email address
                $user = get_user_by( 'email', $user_email );
                $msg = '';
                if( !$user ) {
                    if( ( isset( $settings['register_reset_password_not_exists_msg'] ) ) && ( $settings['register_reset_password_not_exists_msg']!='' ) ) {
                        $msg = SUPER_Common::email_tags( $settings['register_reset_password_not_exists_msg'], $data, $settings, $user );
                    }
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }

                // Disable the default lost password emails
                add_filter( 'send_password_change_email', '__return_false' );

                // Generate a new password for this user
                $password = wp_generate_password( 8, false );
                
                // Update the new password for this user
                $user_id = wp_update_user( array( 'ID' => $user->ID, 'user_pass' => $password ) );

                // Replace the email subject tags with the correct data
                $subject = SUPER_Common::email_tags( $settings['register_reset_password_subject'], $data, $settings, $user );

                // Replace the email body tags with the correct data
                $message = $settings['register_reset_password_email'];
                $message = str_replace( '{register_login_url}', $settings['register_login_url'], $message );
                $message = str_replace( '{register_generated_password}', $password, $message );
                $message = SUPER_Common::email_tags( $message, $data, $settings, $user );
                $message = nl2br( $message );
                $from = SUPER_Common::email_tags( $settings['header_from'], $data, $settings, $user );
                $from_name = SUPER_Common::email_tags( $settings['header_from_name'], $data, $settings, $user );

                // Send the email
                $mail = SUPER_Common::email( $user_email, $from, $from_name, '', '', $subject, $message, $settings );

                // Return message
                if( !empty( $mail->ErrorInfo ) ) {
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $mail->ErrorInfo,
                        $redirect = null
                    );
                }else{
                    $msg = '';
                    if( ( isset( $settings['register_reset_password_success_msg'] ) ) && ( $settings['register_reset_password_success_msg']!='' ) ) {
                        $msg = SUPER_Common::email_tags( $settings['register_reset_password_success_msg'], $data, $settings );
                    }
                    SUPER_Common::output_error(
                        $error = false,
                        $msg = $msg,
                        $redirect = null
                    );                    
                }
            }
        }


        /** 
         *  Resend activation code
         *
         *  @since      1.0.0
        */
        public static function resend_activation() {
            
            $data = $_REQUEST['data'];
            $username = sanitize_user( $data['username'] );
            $form = absint( $data['form'] );
            $user = get_user_by( 'login', $username );
            if( $user ) {
                $to = $user->user_email;
                $name = $user->display_name;
                $code = wp_generate_password( 8, false );
                $password = wp_generate_password();
                $user_id = wp_update_user( array( 'ID' => $user->ID, 'user_pass' => 'stropdas' ) );
                update_user_meta( $user->ID, 'super_account_activation', $code );
                
                // Get the form settings, so we can setup the correct email message and subject
                $settings = get_post_meta( $form, '_super_form_settings', true );

                // Replace email tags with correct data
                $subject = SUPER_Common::email_tags( $settings['register_activation_subject'], $data, $settings );
                $message = $settings['register_activation_email'];
                $message = str_replace( '{field_user_login}', $username, $message );
                $message = str_replace( '{register_login_url}', $settings['register_login_url'], $message );
                $message = str_replace( '{register_activation_code}', $code, $message );
                $message = str_replace( '{register_generated_password}', $password, $message );
                $message = SUPER_Common::email_tags( $message, $data, $settings );
                $message = nl2br( $message );
                $from = SUPER_Common::email_tags( $settings['header_from'], $data, $settings );
                $from_name = SUPER_Common::email_tags( $settings['header_from_name'], $data, $settings );

                // Send the email
                $mail = SUPER_Common::email( $to, $from, $from_name, '', '', $subject, $message, $settings );

                // Return message
                if( !empty( $mail->ErrorInfo ) ) {
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $mail->ErrorInfo,
                        $redirect = null
                    );
                }else{
                    $msg = __( 'We have send you a new activation code, check your email to activate your account!', 'super-forms' );
                    SUPER_Common::output_error(
                        $error = false,
                        $msg = $msg,
                        $redirect = null
                    );                    
                }
            }
            die();
        }


        /** 
         *  Send email with activation code
         *
         *  Generates the random activation code and adds it to the users table during user registration.
         *
         *  @since      1.0.0
        */
        public static function new_user_notification( $user_id, $notify='', $data=null, $settings=null, $password=null, $method=null, $send_password=false ) {

            global $wpdb, $wp_hasher;
            
            do_action( 'super_before_new_user_notifcation_hook' );

            // Generate a code and save it for the user
            $code = wp_generate_password( 8 );
            $wpdb->update(
                $wpdb->users,
                array(
                    'super_activation_code' => $code,
                    'super_user_status' => '0'
                ),
                array(
                    'ID' => $user_id
                )
            );
            $user = get_userdata( $user_id );
            $user_login = $user->user_login; 
            $user_email = $user->user_email;

            do_action( 'super_after_new_user_notifcation_hook' );                        
        
        }

    }
        
endif;


/**
 * Returns the main instance of SUPER_Register_Login to prevent the need to use globals.
 *
 * @return SUPER_Register_Login
 */
function SUPER_Register_Login() {
    return SUPER_Register_Login::instance();
}


// Global for backwards compatibility.
$GLOBALS['super_register_login'] = SUPER_Register_Login();
