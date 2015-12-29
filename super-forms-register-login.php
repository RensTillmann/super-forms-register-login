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
                add_filter( 'super_email_tags_filter', array( $this, 'add_email_tags' ), 10, 0 );

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
         * Hook into the default email tags and add extra tags that can be used in our Activation email
         *
         *  @since      1.0.0
        */
        public static function add_email_tags( $tags ) {
            $tags['register_login_url'] = array(
                __( 'Retrieves the login page URL', 'super' ),
                ''
            );
            $tags['register_activation_code'] = array(
                __( 'Retrieves the activation code', 'super' ),
                ''
            );
            return $tags;
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
                        'default' => SUPER_Settings::get_value( 0, 'register_login_action', $settings['settings'], 'none' ),
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
                    'register_login_url' => array(
                        'name' => __( 'Login page URL', 'super' ),
                        'desc' => __( 'URL of your login page where you placed the login form, here users can activate their account', 'super' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_login_url', $settings['settings'], get_site_url() . '/login/' ),
                        'filter' => true,
                        'parent' => 'register_login_activation',
                        'filter_value' => 'verify',
                    ),
                    'register_activation_subject' => array(
                        'name' => __( 'Activation Email Subject', 'super' ),
                        'desc' => __( 'Example: Activate your account', 'super' ),
                        'default' => SUPER_Settings::get_value( 0, 'register_activation_subject', $settings['settings'], 'Activate your account' ),
                        'filter' => true,
                        'parent' => 'register_login_activation',
                        'filter_value' => 'verify',
                    ),
                    'register_activation_email' => array(
                        'name' => __( 'Activation Email Body', 'super' ),
                        'desc' => __( 'The email message. Use the tag {activation_code} to retrieve the code and {login_url} for the login page.', 'super' ),
                        'type' => 'textarea',
                        'default' => SUPER_Settings::get_value( 0, 'register_activation_email', $settings['settings'], "Dear {field_user_login},\n\nThank you for registering! Before you can login you will need to activate your account.\nBelow you will find your activation code. You need this code to activate your account:\n\nActivation Code: <strong>{register_activation_code}</strong>\n\nClick <a href=\"{register_login_url}?code={register_activation_code}\">here</a> to activate your account with the provided code.\n\n\nBest regards,\n\n{option_blogname}" ),
                        'filter' => true,
                        'parent' => 'register_login_activation',
                        'filter_value' => 'verify',
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
                    $msg = __( 'We couldn\'t find the <strong>user_login</strong> and <strong>user_email</strong> fields which are required in order to register a new user. Please <a href="' . get_admin_url() . 'admin.php?page=super_create_form&id=' . absint( $atts['post']['form_id'] ) . '">edit</a> your form and try again', 'super' );
                    $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
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
                $email_exists = email_exists($user_email);        
                if( ( $username_exists!=false ) || ( $email_exists!=false ) ) {
                    $msg = __( 'Username or Email address already exists, please try again', 'super' );
                    $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
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
                    $msg = __( 'Something went wrong while registering your acount, please try again', 'super' );
                    $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'error' );
                    SUPER_Common::output_error(
                        $error = true,
                        $msg = $msg,
                        $redirect = null
                    );
                }

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

                // Check if we need to send a activation email to this user
                if( $settings['register_login_activation']=='verify' ) {
                    $code = wp_generate_password( 8, false );
                    update_user_meta( $user_id, 'super_account_status', 0 ); // 0 = inactive, 1 = active
                    update_user_meta( $user_id, 'super_account_activation', $code ); 

                    $subject = $settings['register_activation_subject'];
                    $message = $settings['register_activation_email'];


                    $subject = __( 'Activate your account', 'super' );
                    $message  = '<body style="margin: 0; padding: 0;">';
                    $message .= 'Dear ' . $user_login . ',<br /><br />';
                    $message .= 'Thank you for registering! Before you can login you will need to activate your account.<br />';
                    $message .= 'Below you will find your activation code. You need this code to activate your account:<br /><br />';
                    $message .= 'Activation Code: <strong>' . $code . '</strong><br /><br />';
                    $url = $settings['register_login_url'] . '?code=' . $code;
                    $message .= 'Click <a href="' . $url . '">here</a> to activate your account with the provided code.<br /><br /><br />';
                    $message .= 'Best regards,<br /><br />' . get_option( 'blogname' );
                    $message .= '</body>';
                    $send = SUPER_Common::email( $user_email, $user_login, $settings['header_from'], $settings['header_from_name'], $subject, $message, $settings );
                    if (!$send) {    
                        $msg = "Mailer Error: " . $mail->ErrorInfo;
                    } else {
                        $msg = __( 'Thank you for registering, please check your email to activate your account.', 'super' );
                        $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'success' );
                        SUPER_Common::output_error(
                            $error = false,
                            $msg = $msg,
                            $redirect = $settings['register_login_url'] . '?code=--CODE--'
                        );
                    }
                }
                
                // Check if we let users automatically login after registering (instant login)
                if( $settings['register_login_activation']=='auto' ) {
                    wp_set_current_user( $user_id );
                    wp_set_auth_cookie( $user_id );
                    update_user_meta( $user_id, 'super_last_login', time() );
                }

                // Return success message
                $msg = __( 'Your account has been created!', 'super' );
                $_SESSION['super_msg'] = array( 'msg'=>$msg, 'type'=>'success' );
                SUPER_Common::output_error(
                    $error = false,
                    $msg = $msg,
                    $redirect = null
                );
            }


            if( $settings['register_login_action']=='login' ) {

                var_dump('login user');

            }

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


            /*
            $message = '';
            if( $method=='resend' ) {
                $code = wp_generate_password( 8 );
                $wpdb->update( $wpdb->users, array( 'super_activation_code' => $code, 'super_user_status' => '0' ), array( 'ID' => $user_id ) );
                $user = get_userdata( $user_id );
                $user_login = stripslashes( $user->user_login ); 
                $user_email = stripslashes( $user->user_email );
                $settings = get_option( 'super_settings' );
                $settings['header_additional'] = '';
                $message = '<body style="margin: 0; padding: 0;">';
                $message .= __( 'Activation Code:', 'super' ) . ' <strong>' . $code . '</strong><br /><br />'; 
                $url = network_site_url( 'wp-login.php?super_code=' . urlencode( $code ), 'login' );
                $message .= __( 'Click', 'super' ) . ' <a href="' . $url . '">' . __( 'here', 'super' ) . '</a> ' . __( 'to activate your account.', 'super' ) . '<br /><br /><br />';
                $message .= __( 'In case the link doesn\'t work, copy the following URL and paste it in your browsers address bar:' ) . '<br />';
                $message .= '<pre>' . ( wp_login_url() . '?super_code=' . urlencode( $code ) ) . '</pre>';
                $message .= '</body>';
                $message = apply_filters( 'super_resend_activation_link_message_filter', $message, $user_login, $user_email, $code );
                $settings['confirm_body'] = $message;
                $settings['confirm_to'] = $user_email;
                $settings['confirm_subject'] = apply_filters( 'super_resend_activation_subject_filter', sprintf( __( '[%s] Verify your account', 'super' ), get_option( 'blogname' ) ), $user_login, $user_email, $code );
                $settings['send'] = 'no';
                $settings['confirm'] = 'yes';
                $settings['save_contact_entry'] = 'no';
                tdfb_send_email( $settings );
            }else{
                $code = wp_generate_password( 8, false, false );
                $wpdb->update( $wpdb->users, array( 'super_activation_code' => $code, 'super_user_status' => '0' ), array( 'ID' => $user_id ) );
                $user = get_userdata( $user_id );
                $obj = SUPER_Register_Login();
                $user_login = stripslashes($user->user_login); 
                $user_email = stripslashes($user->user_email);
                $key = $password;
                do_action( 'retrieve_password_key', $user->user_login, $key );
                if ( empty( $wp_hasher ) ) {
                    require_once ABSPATH . WPINC . '/class-phpass.php';
                    $wp_hasher = new PasswordHash( 8, true );
                }
                $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
                $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
                if($send_password) $message .= __( 'Password:', 'super' ).' <strong>'.$password.'</strong><br /><br />';
                $message .= __( 'Activation Code:', 'super' ).' <strong>'.$code.'</strong><br /><br />'; 
                $url = network_site_url( 'wp-login.php?super_code='.urlencode( $code ), 'login');
                $message .= __( 'Click', 'super' ).' <a href="'.$url.'">'.__( 'here', 'super' ).'</a> '.__( 'to activate your account.', 'super' ).'<br /><br /><br />';
                $message .= __( 'In case the link doesn\'t work, copy the following URL and paste it in your browsers address bar:' ) . '<br />';
                $message .= '<pre>'.(wp_login_url().'?super_code='.urlencode( $code )).'</pre>';
                if ( ( $notify=='both' ) || ( $notify=='user' ) ) {
                    $obj->confirm = 'yes';
                    $obj->confirm_body = $message;
                    $obj->confirm_to = $user->user_email;
                }
                if ( ( $notify=='both' ) || ( $notify=='admin' ) ) {
                    $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
                    $message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "<br /><br />";
                    $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "<br /><br />";
                    $message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "<br /><br />";
                    $obj->send = 'yes';
                    $obj->email_body = $message;
                }
                add_filter( 'tdfb_before_sending_email_settings_filter', array( $obj, 'update_confirm_settings' ) );
            }
            */

                        
        
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
