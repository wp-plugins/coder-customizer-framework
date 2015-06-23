<?php
/*
Plugin Name: Coder Customizer Framework
Plugin URI: http://codersantosh.com
Description: Use  WordPress Customizer in easy and standard way to your theme.
Version: 1.0
Author: Santosh Kunwar (CoderSantosh)
Author URI: http://codersantosh.com
License: GPL
Copyright: Santosh Kunwar (CoderSantosh)
*/

/*Make sure we don't expose any info if called directly*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

if ( ! class_exists( 'Coder_Customizer_Framework' ) ){
    /**
     * Class for almost all types of customizer fields.
     *
     * @package Coder Customizer Framework
     * @since 1.0
     */
    class Coder_Customizer_Framework{
        /*Basic variables for class*/

        /**
         * Variable to hold this framework version
         *
         * @var string
         * @access protected
         * @since 1.0
         *
         */
        private  $coder_customizer_framework_version = '1.0';

        /**
         * Variable to hold this framework minimum wp version
         *
         * @var string
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_customizer_framework_minimum_wp_version = '3.1';

        /**
         * Coder_Customizer_Framework Plugin instance.
         *
         * @see coder_get_instance()
         * @var object
         * @access protected
         * @since 1.0
         *
         */
        protected static $coder_instance = NULL;

        /**
         * Variable to hold this framework url
         *
         * @var string
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_customizer_framework_url = '';

        /**
         * Variable to hold this framework path
         *
         * @var string
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_customizer_framework_path = '';

        /**
         * Name use to save customizer value
         *
         * @var string
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_customizer_name = 'coder_customizer';

        /**
         * Holds all basic control types not required class
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_basic_control_types =
            array(
                'text',
                'textarea',
                'checkbox',
                'number',
                'radio',
                'range',
                'select',
                'url',
                'email',
                'password',
                'dropdown-pages',
            );

        /**
         * Holds all panels sections settings
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_panels_sections_settings = array();

        /**
         * Holds all panels
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_panels = array();

        /**
         * Holds all sections
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_sections = array();

        /**
         * Holds all settings controls
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_settings_controls = array();

        /**
         * Holds all panel id to remove
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_remove_panels = array();

        /**
         * Holds all section id to remove
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_remove_sections = array();

        /**
         * Holds all settings control id to remove
         *
         * @var array
         * @access protected
         * @since 1.0
         *
         */
        protected $coder_remove_settings_controls = array();

        /**
         * Access this plugin’s working coder_instance
         *
         * @access public
         * @since 1.0
         * @return object of this class
         */
        public static function coder_get_instance() {
            NULL === self::$coder_instance and self::$coder_instance = new self;
            return self::$coder_instance;
        }

        /**
         * Used for regular plugin work.
         *
         * @access public
         * @since 1.0
         *
         * @return void
         *
         */
        public function coder_customizer_init($coder_panels_sections_settings = array()) {

            /*Basic variables initialization with filter*/
            if (defined('coder_customizer_theme') && coder_customizer_theme == 1 ) {
                $this->coder_customizer_framework_url = get_template_directory_uri().'/coder-customizer-framework/';
                $this->coder_customizer_framework_path = get_template_directory().'/coder-customizer-framework/';
            }
            elseif (defined('coder_customizer_child_theme') && coder_customizer_child_theme == 1) {
                $this->coder_customizer_framework_url = get_stylesheet_directory_uri().'/coder-customizer-framework/';
                $this->coder_customizer_framework_path = get_stylesheet_directory().'/coder-customizer-framework/';
            }
            else {
                $this->coder_customizer_framework_url = plugin_dir_url( __FILE__ ) ;
                $this->coder_customizer_framework_path = plugin_dir_path( __FILE__ );
            }
            $this->coder_customizer_framework_url = apply_filters( 'coder_customizer_framework_url', $this->coder_customizer_framework_url );
            $this->coder_customizer_framework_path = apply_filters( 'coder_customizer_framework_path', $this->coder_customizer_framework_path );

            /*load translation*/
            add_action('init', array($this,'coder_load_textdomain') , 12);

            /*Basic variables initialization with filter*/
            $this->coder_customizer_name = apply_filters( 'coder_customizer_name', $this->coder_customizer_name );

            $this->coder_basic_control_types = apply_filters( 'coder_basic_control_types', $this->coder_basic_control_types );

            $this->coder_panels_sections_settings = $coder_panels_sections_settings;
            $this->coder_panels_sections_settings = apply_filters( 'coder_panels_sections_settings', $this->coder_panels_sections_settings );

            /*Hook before any function of class start */
            do_action( 'coder_customizer_framework_before', $this->coder_panels_sections_settings );

            if(isset($this->coder_panels_sections_settings['panels']) && !empty($this->coder_panels_sections_settings['panels'])){
                $this->coder_panels = $this->coder_panels_sections_settings['panels'];
            }
            if(isset($this->coder_panels_sections_settings['sections']) && !empty($this->coder_panels_sections_settings['sections'])){
                $this->coder_sections = $this->coder_panels_sections_settings['sections'];
            }
            if(isset($this->coder_panels_sections_settings['settings_controls']) && !empty($this->coder_panels_sections_settings['settings_controls'])){
                $this->coder_settings_controls = $this->coder_panels_sections_settings['settings_controls'];
            }
            if(isset($this->coder_panels_sections_settings['remove_panels']) && !empty($this->coder_panels_sections_settings['remove_panels'])){
                $this->coder_remove_panels = $this->coder_panels_sections_settings['remove_panels'];
            }
            if(isset($this->coder_panels_sections_settings['remove_sections']) && !empty($this->coder_panels_sections_settings['remove_sections'])){
                $this->coder_remove_sections = $this->coder_panels_sections_settings['remove_sections'];
            }
            if(isset($this->coder_panels_sections_settings['remove_settings_controls']) && !empty($this->coder_panels_sections_settings['remove_settings_controls'])){
                $this->coder_remove_settings_controls = $this->coder_panels_sections_settings['remove_settings_controls'];
            }
            $this->coder_panels = apply_filters( 'coder_panels', $this->coder_panels );

            $this->coder_sections = apply_filters( 'coder_sections', $this->coder_sections );

            $this->coder_settings_controls = apply_filters( 'coder_settings_controls', $this->coder_settings_controls );

            $this->coder_remove_panels = apply_filters( 'coder_remove_panels', $this->coder_remove_panels );

            $this->coder_remove_sections = apply_filters( 'coder_remove_sections', $this->coder_remove_sections );

            $this->coder_remove_settings_controls = apply_filters( 'coder_remove_settings_controls', $this->coder_remove_settings_controls );

            /*Set default values for panels*/
            if(!empty($this->coder_panels)){
                foreach( $this->coder_panels as $coder_panel_id => $coder_panel ){
                    $this->coder_panels_default_values($coder_panel_id, $coder_panel);
                }
            }

            /*Set default values for sections*/
            if(!empty($this->coder_sections)){
                foreach( $this->coder_sections as $coder_section_id => $coder_section ){
                    $this->coder_sections_default_values($coder_section_id, $coder_section);
                }
            }

            /*Set default values for setting control*/
            if(!empty($this->coder_settings_controls)) {
                foreach( $this->coder_settings_controls as $coder_settings_control_id => $coder_setting_control ){
                    $this->coder_setting_control_default_values($coder_settings_control_id, $coder_setting_control);
                }
            }

            /*Enqueue necessary styles and scripts in  Theme Customizer.*/
            add_action('customize_controls_enqueue_scripts', array($this,'coder_customize_controls_enqueue_scripts'), 12 );

            /*Adding theme customization admin screen*/
            add_action( 'customize_register', array($this,'coder_customize_register'), 12 );

            /*Hook before any function of class end */
            do_action( 'coder_customizer_framework_after', $this->coder_panels_sections_settings );
        }

        /**
         * Constructor. Intentionally left empty and public.
         *
         * @access public
         * @since 1.0
         *
         *
         */
        public function __construct( $coder_customizer_init = array()){
            if(!empty($coder_customizer_init)) {
                $this->coder_customizer_init( $coder_customizer_init );
            }
        }

        /**
         * Load_textdomain
         *
         * @access public
         * @since 1.0
         *
         * @return void
         *
         */
        public function coder_load_textdomain(){
            /*Added filter for text domain path*/
            $coder_customizer_framework_textdomain_path = apply_filters( 'coder_customizer_framework_textdomain_path', $this->coder_customizer_framework_path );
            load_textdomain( 'coder_customizer_framework', $coder_customizer_framework_textdomain_path . '/languages/' . get_locale() .'.mo' );
        }

        /**
         * Function to Set default values for panels
         *
         * @access public
         * @since 1.0
         *
         * @param string $coder_panel_id Id of panel
         * @param array $coder_panel Single panel
         * @return void
         *
         */
        public function coder_panels_default_values($coder_panel_id, $coder_panel) {
            $coder_panels_default_values =
                array(
                    'priority'       => 120,
                    'capability'     => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'          => '',
                    'description'    => '',
                );
            $coder_panels_default_values = apply_filters( 'coder_panel_default_values', $coder_panels_default_values);

            $this->coder_panels[$coder_panel_id] =
                array_merge(
                    $coder_panels_default_values,
                    (array)$coder_panel
                );
        }

        /**
         * Function to Set default values for sections
         *
         * @access public
         * @since 1.0
         *
         * @param string $coder_section_id Id of section
         * @param array $coder_section Single section
         * @return void
         *
         */
        public function coder_sections_default_values($coder_section_id, $coder_section) {
            $coder_sections_default_values =
                array(
                    'priority'       => 120,
                    'capability'     => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'          => '',
                    'description'    => '',
                    'panel'          => '',
                );
            $coder_sections_default_values = apply_filters( 'coder_sections_default_values', $coder_sections_default_values);

            $this->coder_sections[$coder_section_id] =
                array_merge(
                    $coder_sections_default_values,
                    (array)$coder_section
                );
        }

        /**
         * Function to Set default values for sections
         *
         * @access public
         * @since 1.0
         *
         * @param string $coder_settings_control_id Id of settings control
         * @param array $coder_setting_control Single settings control
         * @return void
         *
         */
        public function coder_setting_control_default_values($coder_settings_control_id, $coder_setting_control) {
            $coder_setting_default_values =
                array(
                    'type'                 => 'theme_mod',
                    'capability'           => 'edit_theme_options',
                    'theme_supports'       => '',
                    'default'              => '',
                    'transport'            => 'refresh',
                    'sanitize_callback'    => 'esc_attr',
                    'sanitize_js_callback' => 'esc_attr',
                );
            $coder_control_default_values =
                array(
                    'label'                 => '',
                    'section'               => '',
                    'type'                  => '',
                    'priority'              => 12,
                    'description'           => '',
                    'active_callback'       => ''
                );
            $coder_setting_default_values = apply_filters( 'coder_setting_default_values', $coder_setting_default_values);
            $coder_control_default_values = apply_filters( 'coder_control_default_values', $coder_control_default_values);


            if(!isset($coder_setting_control['setting'])) {
                $coder_setting_control['setting'] = array();
            }
            if(!isset($coder_setting_control['control'])) {
                $coder_setting_control['control'] = array();
            }

            $this->coder_settings_controls[$coder_settings_control_id]['setting'] =
                array_merge(
                    $coder_setting_default_values,
                    (array)$coder_setting_control['setting']
                );
            $this->coder_settings_controls[$coder_settings_control_id]['control'] =
                array_merge(
                    $coder_control_default_values,
                    (array)$coder_setting_control['control']
                );
        }
        /**
         * Enqueue style and scripts at Theme Customizer
         *
         * @access public
         * @since 1.0
         *
         * @return void
         *
         */
        function coder_customize_controls_enqueue_scripts(){
            global $pagenow;
            if ( 'customize.php' == $pagenow ) {

                wp_register_style( 'coder-customizer-framework-style', $this->coder_customizer_framework_url . '/assets/css/coder-customizer-framework.css', false, $this->coder_customizer_framework_version );
                wp_enqueue_style( 'coder-customizer-framework-style' );

                /*localizing the script start*/
                /*Register the script*/
                wp_register_script( 'coder-customizer-framework', $this->coder_customizer_framework_url . '/assets/js/coder-customizer-framework.js', array( 'jquery' ), $this->coder_customizer_framework_version, true );
                wp_register_script( 'coder-customizer-framework', $this->coder_customizer_framework_url . '/assets/js/coder-customizer-framework.js', array( 'jquery' ), $this->coder_customizer_framework_version, true );
                /*Localize the script with new data*/
                $coder_customizer_localization_array = array(
                    'coder_customizer_framework_url' => $this->coder_customizer_framework_url
                );
                wp_localize_script( 'coder-customizer-framework', 'coder_customizer_framework', $coder_customizer_localization_array );
                /*enqueue script with localized data.*/
                wp_enqueue_script( 'coder-customizer-framework' );
                /*localizing the script end*/
            }
        }

        /**
         * Function to register customizer
         *
         * @access public
         * @since 1.0
         *
         * @param object $coder_wp_customize
         * @return void
         *
         */
        public function coder_customize_register( $coder_wp_customize ){

            require_once trailingslashit( $this->coder_customizer_framework_path ) . 'inc/Coder-Customizer-Custom-Control.php';

            /*Again adding filter here*/
            $coder_panels = apply_filters( 'coder_register_customize_panel', $this->coder_panels );
            $coder_sections = apply_filters( 'coder_register_customize_sections', $this->coder_sections );
            $coder_settings_controls = apply_filters( 'coder_register_customize_settings_controls', $this->coder_settings_controls );
            $coder_customizer_name = apply_filters( 'coder_register_customizer_name', $this->coder_customizer_name );
            $coder_basic_control_types = apply_filters( 'coder_register_customizer_basic_control_types', $this->coder_basic_control_types );
            $coder_remove_panels = apply_filters( 'coder_register_customize_remove_panel', $this->coder_remove_panels );
            $coder_remove_sections = apply_filters( 'coder_register_customize_remove_sections', $this->coder_remove_sections );
            $coder_remove_settings_controls = apply_filters( 'coder_register_customize_remove_settings_controls', $this->coder_remove_settings_controls );


            /*Adding Panels*/
            if ( ! empty( $coder_panels ) ) {
                foreach($coder_panels as $coder_panel_key =>  $coder_panel) {
                    $coder_wp_customize->add_panel( esc_attr( $coder_panel_key ), $coder_panel );
                }
            }

            /*Adding sections*/
            if ( ! empty( $coder_sections ) ) {
                foreach($coder_sections as $coder_section_key =>  $coder_section) {
                    $coder_wp_customize->add_section( esc_attr( $coder_section_key ), $coder_section );
                }
            }


            /*Adding settings controls*/
            if ( ! empty( $coder_settings_controls ) ) {
                foreach($coder_settings_controls as $coder_setting_control_key =>  $coder_settings_control) {
                    do_action('coder_add_setting_control',$coder_wp_customize,$coder_customizer_name, $coder_basic_control_types, $coder_setting_control_key, $coder_settings_control);
                }
            }
            /*Removing Panels*/
            if ( ! empty( $coder_remove_panels ) ) {
                foreach($coder_remove_panels as $coder_remove_panel) {
                    $coder_wp_customize->remove_panel(esc_attr( $coder_remove_panel ));
                }
            }

            /*Removing sections*/
            if ( ! empty( $coder_remove_sections ) ) {
                foreach($coder_remove_sections as $coder_remove_section) {
                    $coder_wp_customize->remove_section(esc_attr( $coder_remove_section ));
                }
            }
            /*Removing settings controls*/
            if ( ! empty( $coder_remove_settings_controls ) ) {
                foreach($coder_remove_settings_controls as $coder_remove_settings_control) {
                    echo $coder_remove_settings_control;
                    echo "<br>";
                    $coder_wp_customize->remove_setting(esc_attr( $coder_remove_settings_control ));
                    $coder_wp_customize->remove_control(esc_attr( $coder_remove_settings_control ));
                }
            }

        }/*END function coder_customize_register*/
    } /*END class Coder_Customizer_Framework*/

    /*Initialize class after theme setup*/
    add_action( 'after_setup_theme', array ( Coder_Customizer_Framework::coder_get_instance(), 'coder_customizer_init' ));

    /*I have added it through action so that it is flexible to the developer to adapt change*/
    add_action('coder_add_setting_control','coder_add_setting_control_callback', 12, 5);

    if ( ! function_exists( 'coder_add_setting_control_callback' ) ) :
        /**
         * Function to add customizer setting and controls
         *
         * @access public
         * @since 1.0
         *
         * @param object $coder_wp_customize
         * @param string $coder_customizer_name common name for all setting and controls
         * @param array $coder_basic_control_types
         * @param string $coder_setting_control_key
         * @param array $coder_settings_control
         * @return void
         *
         */
        function coder_add_setting_control_callback($coder_wp_customize, $coder_customizer_name, $coder_basic_control_types, $coder_setting_control_key, $coder_settings_control){
            $coder_wp_customize->add_setting( esc_attr( $coder_customizer_name.'['.$coder_setting_control_key.']' ), $coder_settings_control['setting']);

            $coder_control_field_type = $coder_settings_control['control']['type'];

            /*check if basic control types*/
            if ( in_array( $coder_control_field_type, $coder_basic_control_types ) ) {
                $coder_wp_customize->add_control( esc_attr( $coder_customizer_name.'['.$coder_setting_control_key.']' ), $coder_settings_control['control']);
            }
            else {
                /*Check for default WP_Customize_Custom_Control defined*/
                $coder_Explode_Customize_Custom_Control_class_name = explode('_', strtolower( $coder_control_field_type ));
                $coder_Ucfirst_Customize_Custom_Control_class_name_array = array_map('ucfirst', $coder_Explode_Customize_Custom_Control_class_name);
                $coder_Implode_Customize_Custom_Control_class_name = implode('_', $coder_Ucfirst_Customize_Custom_Control_class_name_array);

                $coder_New_Customize_Custom_Control_class_name = 'WP_Customize_'.$coder_Implode_Customize_Custom_Control_class_name.'_Control';
                $coder_customizer_class_exist = false;
                if ( class_exists( $coder_New_Customize_Custom_Control_class_name ) ) {
                    $coder_customizer_class_exist = true;
                }
                else{
                    $coder_New_Customize_Custom_Control_class_name = 'Coder_Customize_'.$coder_Implode_Customize_Custom_Control_class_name.'_Control';
                    if ( class_exists( $coder_New_Customize_Custom_Control_class_name ) ) {
                        $coder_customizer_class_exist = true;
                    }

                }
                if($coder_customizer_class_exist){
                    $coder_wp_customize->add_control(
                        new $coder_New_Customize_Custom_Control_class_name(
                            $coder_wp_customize,
                            esc_attr( $coder_customizer_name.'['.$coder_setting_control_key.']'),
                            $coder_settings_control['control']
                        )
                    );
                }
                else {
                    ?>
                    <script>
                        console.log('<?php echo  $coder_New_Customize_Custom_Control_class_name. "not found. Please create it."?>');
                    </script>
                <?php
                }

            }
        }
    endif;

    if ( ! function_exists( 'coder_get_customizer_all_values' ) ) :
        /**
         * Function to get all value
         *
         * @access public
         * @since 1.0
         *
         * @param string $coder_customizer_name
         * @return array || other values
         *
         */
        function coder_get_customizer_all_values($coder_customizer_name = null){
            if( null == $coder_customizer_name ){
                $coder_customizer_name = 'coder_customizer';
            }
            $coder_customizer_values = get_theme_mod( $coder_customizer_name);
            if(empty($coder_customizer_values)){
                $coder_customizer_values = get_option( $coder_customizer_name);
            }
            return $coder_customizer_values;

        }
    endif;

    if ( ! function_exists( 'coder_get_customizer_single_value' ) ) :
        /**
         * Function to get single value
         *
         * @access public
         * @since 1.0
         *
         * @param string $coder_customizer_name
         * @return array || other values
         *
         */
        function coder_get_customizer_single_value ($coder_single_value_name){
            $coder_customizer_values = coder_get_customizer_all_values();
            return $coder_customizer_values[$coder_single_value_name];
        }
    endif;
}/*END if(!class_exists('Coder_Customizer_Framework'))*/