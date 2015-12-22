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
 * Version:     1.0.0
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
        public $version = '1.0.0';

        
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

            // Actions since 1.0.0

            if ( $this->is_request( 'frontend' ) ) {
                
                // Filters since 1.0.0

                // Actions since 1.0.0
                
            }
            
            if ( $this->is_request( 'admin' ) ) {
                
                // Filters since 1.0.0
                add_filter( 'super_settings_after_smtp_server_filter', array( $this, 'add_settings' ), 10, 2 );

                // Actions since 1.0.0
                add_action( 'super_before_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms' ) );
                add_action( 'super_after_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms_json' ) );

            }
            
            if ( $this->is_request( 'ajax' ) ) {

                // Filters since 1.0.0

                // Actions since 1.0.0
                add_action( 'super_before_sending_email_hook', array( $this, 'before_sending_email' ) );

            }
            
        }

        
        /**
         * Hook into the load form dropdown and add some ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms() {
            $html = '<option value="register-login-email">Register & Login - Subscribe email address only</option>';
            $html .= '<option value="register-login-name">Register & Login - Subscribe with first and last name</option>';
            $html .= '<option value="register-login-interests">Register & Login - Subscribe with interests</option>';
            echo $html;
        }


        /**
         * Hook into the after load form dropdown and add the json of the ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms_json() {
            $html  = '<textarea hidden name="register-login-email">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"register_login","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="register-login-name">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"register_login","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="register-login-interests">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"register_login","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';
            echo $html;
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
                '' => __( 'All user roles', 'super' )
            );
            foreach( $editable_roles as $k => $v ) {
                $roles[$k] = $v['name'];
            }
            $reg_roles = $roles;
            unset($reg_roles['']);
            $array['register_login'] = array(        
                'name' => __( 'Register & Login', 'super' ),
                'label' => __( 'Register & Login Settings', 'super' ),
                'fields' => array(
                    'register_login_action' => array(
                        'name' => __( 'Actions', 'super' ),
                        'desc' => __( 'Select what this form should do (register or login)?', 'super' ),
                        'default' => SUPER_Settings::get_value( 0, 'login_user_role', $settings['settings'], '' ),
                        'filter' => true,
                        'type' => 'select',
                        'values' => array(
                            'none' => __( 'None (do nothing)', 'super' ),
                            'register' => __( 'Register a new user', 'super' ),
                            'login' => __( 'Login (user will be logged in)', 'super' ),
                        ),
                    ),
                    'login_user_role' => array(
                        'name' => __( 'Allowed user role(s)', 'super' ),
                        'desc' => __( 'Which user roles are allowed to login?', 'super' ),
                        'type' => 'select',
                        'multiple' => true,
                        'default' => SUPER_Settings::get_value( 0, 'login_user_role', $settings['settings'], '' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'login',
                        'values' => $roles,
                    ),
                    'register_user_role' => array(
                        'name' => __( 'User role', 'super' ),
                        'desc' => __( 'What user role should this user get?', 'super' ),
                        'type' => 'select',
                        'default' => SUPER_Settings::get_value( 0, 'register_user_role', $settings['settings'], '' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                        'values' => $reg_roles,
                    ),
                    'register_login_activation' => array(
                        'name' => __( 'Send activation link', 'super' ),
                        'desc' => __( 'Optionally let users activate their account or let them instantly login without verification', 'super' ),
                        'type' => 'select',
                        'default' => SUPER_Settings::get_value( 0, 'register_login_activation', $settings['settings'], 'verify' ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
                        'values' => array(
                            'verify' => __( 'Send activation link', ' super' ),
                            'auto' => __( 'Auto activate and login', 'super' ),
                        ),
                    ),
                    'register_login_user_meta' => array(
                        'name' => __( 'Save custom user meta', 'super' ),
                        'desc' => __( 'Usefull for external plugins such as WooCommerce. Example: "field_name|meta_key" (each on a new line)', 'super' ),
                        'type' => 'textarea',
                        'default' => SUPER_Settings::get_value( 0, 'register_login_user_meta', $settings['settings'], "first_name|billing_first_name\nlast_name|billing_last_name\naddress|billing_address" ),
                        'filter' => true,
                        'parent' => 'register_login_action',
                        'filter_value' => 'register',
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
                    $msg = __( 'We couldn\'t find the <strong>user_login</strong> and <strong>user_email</strong> fields which are required in order to register a new user. Please <a href="' . get_admin_url() . '/admin.php?page=super_create_form&id=' . absint( $atts['post']['form_id'] ) . '">edit</a> your form and try again', 'super' );
                    $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }
                /*
                var_dump('register new user');
                

                /*
                $username = sanitize_user( $data['username'] );
                $email = sanitize_email( $data['email'] );
                $user_id = username_exists( $username );
                if ( !$user_id and email_exists( $email ) == false ) {
                    
                    // Lets find out if a field could be found with the name "role".
                    // This allows users to to select their user role by their own.
                    // Usefull for forms where users can select to be a company or consumer.

                    $role = '';
                    if( isset( $settings['register_login_action'] ) ) {
                        $role = $settings['register_login_action'];
                    }
                    if( isset( $data['role'] ) ) {
                        $role = sanitize_text_field( $data['role'] );
                    }


                    register_login_user_meta


                    $role = absint( $data['role'] );
                    $password = $data['password'];
                    $first_name = sanitize_text_field( $data['first_name'] );
                    $last_name = sanitize_text_field( $data['last_name'] );
                    $website =  sanitize_text_field( $data['website'] );
                    
                    //$website =  sanitize_text_field( $data['website'] );
                    //$company_name =  sanitize_text_field( $data['company_name'] );
                    //$title =  sanitize_text_field( $data['title'] );
                    //$first_name =  sanitize_text_field( $data['first_name'] );
                    //$last_name =  sanitize_text_field( $data['last_name'] );
                    //$phone =  sanitize_text_field( $data['phone'] );
                    //$mobile =  sanitize_text_field( $data['mobile'] );
                    //$address =  sanitize_text_field( $data['address'] );
                    //$zipcode =  sanitize_text_field( $data['zipcode'] );
                    //$city =  sanitize_text_field( $data['city'] );
                    //$state =  absint( $data['state'] );
                    if( $account_type==0 ) {
                        $role = 'vg_consumer';    
                    }else{
                        $role = 'vg_company';
                    }
                    $code = wp_generate_password( 8, false );
                    $user_id = wp_insert_user( 
                        array(
                            'user_login' => $username,
                            'user_email' => $email,
                            'user_pass' => $password,
                            'role' => $role,
                            'show_admin_bar_front' => 'false'
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'user_url' => $website,
                        )
                    );
                    if ( ! is_wp_error( $user_id ) ) {
                        
                        // Save user meta
                        $meta_data = array(
                            
                            // VeilGarant details
                            //'vg_title' => $title,
                            'vg_agreed_to' => 'v'.VG_TERMS_VERSION,
                            'vg_status' => 0, // 0 = inactive, 1 = active
                            'vg_activation' => $code,
                            'vg_reg_completed' => 0, // 0 = incomplete, 1 = completed
                            
                            // Billing details
                            'billing_email' => $email

                            /*
                            'billing_first_name' => $first_name,
                            'billing_last_name' => $last_name,
                            'billing_company' => $company_name,
                            'billing_address_1' => $address,
                            'billing_address_2' => '',
                            'billing_city' => $city,
                            'billing_postcode' => $zipcode,
                            'billing_country' => 'NL',
                            'billing_state' => $state,
                            'billing_phone' => $phone,
                            'billing_mobile' => $mobile,
                            'billing_email' => $email,
                            
                            // Shipping details
                            'shipping_first_name' => $first_name,
                            'shipping_last_name' => $last_name,
                            'shipping_company' => $company_name,
                            'shipping_address_1' => $address,
                            'shipping_address_2' => '',
                            'shipping_city' => $city,
                            'shipping_postcode' => $zipcode,
                            'shipping_country' => 'NL',
                            'shipping_state' => $state
                            */

                        /*
                        );
                        foreach($meta_data as $k => $v){
                            update_user_meta( $user_id, $k, $v ); 
                        }                        
                        
                        $subject = 'Activeer uw account';
                        $message  = '<body style="margin: 0; padding: 0;">';
                        $message .= 'Beste ' . $username . ',<br /><br />';
                        $message .= 'Bedankt voor het registreren op VeilGarant. Voordat u kunt inloggen moet u uw account activeren.<br />';
                        $message .= 'Hieronder ziet u uw activatie code, deze heeft u nodig om uw account te activeren:<br /><br />';
                        $message .= 'Activatie Code: <strong>' . $code . '</strong><br /><br />';
                        $url = get_site_url() . '/inloggen/?code=' . $code;
                        $message .= 'Klik <a href="' . $url . '">hier</a> om uw account te activeren met de bovenstaande code.<br /><br /><br />';
                        $message .= 'Indien de link niet werkt kunt u de onderstaande URL kopiëren en in de adres balk van uw browser plakken:<br />';
                        $message .= '<pre>' . $url . '</pre><br /><br />';
                        $message .= 'We wensen u veel plezier met uw account op veilgarant.nl!<br /><br /><br />';
                        $message .= 'Met vriendelijke groet,<br /><br />VeilGarant BV';
                        $message .= '</body>';
                        $attachements = array('/htdocs/veilgarant.f4d.nl/public_html/algemene-voorwaarden.pdf' => 'Algemene Voorwaarden');
                        $send = VG_Common::email( $email, $username, $subject, $message, $attachements );
                        if (!$send) {    
                            $msg = "Mailer Error: " . $mail->ErrorInfo;
                        } else {
                            $wp_session = WP_Session::get_instance();
                            $msg = 'Bedankt voor uw registratie, controleer uw email om uw account te activeren. Controleer eventueel ook uw spam.';
                            $wp_session['veilgarant_msg'] = array( 'msg'=>$msg, 'type'=>'success' );
                            self::output_error(
                                $error = false,
                                $msg = $msg,
                                $redirect = get_site_url()
                            );
                        }
                    }
                } else {
                    self::output_error(
                        $error = true,
                        $msg = 'De opgegeven gebruikersnaam of email adres is reeds in gebruik.',
                        $redirect = null,
                        $fields = array(
                            'username' => 'input',
                            'email' => 'input'
                        )
                    );
                }
                die();


                */

































            }
            if( $settings['register_login_action']=='login' ) {
                var_dump('login user');
            }
            //die();
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
