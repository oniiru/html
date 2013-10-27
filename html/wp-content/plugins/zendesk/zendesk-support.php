<?php
/*
 * Plugin Name: Zendesk Support for WordPress
 * Plugin URI: http://zendesk.com
 * Description: Zendesk Support for WordPress
 * Author: Konstantin Kovshenin
 * Version: 1.3
 * Author URI: http://kovshenin.com
 * 
 */

// Debug
define( 'ZENDESK_DEBUG', false );

// Load Zendesk API Class & Compatibility hacks
require_once( plugin_dir_path( __FILE__ ) . 'zendesk-compatibility.php' );
require_once( plugin_dir_path( __FILE__ ) . 'zendesk-api.php' );


/*
 * Zendesk Support Class
 * 
 * This is the main plugin class, handles all the plugin options, WordPress
 * hooks and filters, as well as options validation. The Zendesk Dropbox
 * is fully defined in this class too.
 * 
 */
class Zendesk_Support {
	public $settings = array();
	
	/*
	 * Class Constructor
	 * 
	 * Fired during WordPress init, assign actions and add filters, read
	 * the settings from the database and construct the Zendesk URL.
	 * 
	 */
	public function __construct() {
		
		// Load text domain
		load_plugin_textdomain( 'zendesk', null, basename( dirname( __FILE__ ) ) . '/languages' );
		
		add_action( 'admin_menu', array( &$this, '_admin_menu' ) );
		add_action( 'admin_init', array( &$this, '_admin_init' ) );
		
		// AJAX calls
		add_action( 'wp_ajax_zendesk_view_ticket', array( &$this, '_ajax_view_ticket' ) );
		add_action( 'wp_ajax_zendesk_get_view', array( &$this, '_ajax_get_view' ) );
		add_action( 'wp_ajax_zendesk_convert_to_ticket', array( &$this, '_ajax_convert_to_ticket' ) );
		add_action( 'wp_ajax_zendesk_convert_to_ticket_post', array( &$this, '_ajax_convert_to_ticket_post' ) );
		add_action( 'wp_ajax_zendesk_view_comments', array( &$this, '_ajax_view_comments' ) );
		
		// Initialize
		$this->setup();
		
		// Setup the Dashboard widget.
		add_action( 'wp_dashboard_setup', array( &$this, '_dashboard_widget_setup' ) );
		
		// Let's see if we need to do a remote auth.
		$this->_do_remote_auth();
		
		// Let's see if Dropbox is set to auto
		if ( isset( $this->settings['dropbox_display'] ) && $this->settings['dropbox_display'] == 'auto' )
			add_action( 'wp_footer', array( &$this, 'dropbox_code' ) );
	}
	
	/*
	 * Plugin Setup
	 * 
	 * Load settings, set URLs, authenticate the current user.
	 * 
	 */
	public function setup() {
		// Load up the settings, set the Zendesk URL and initialize the API object.
		$this->_load_settings();
		
		// Load default settings if there are no settings
		if ( false === $this->settings )
			$this->_default_settings();
		
		// $this->_delete_settings();
		
		$https = ( isset( $this->settings['ssl'] ) && $this->settings['ssl'] ) ? 'https' : 'http';
		$this->zendesk_url = $https . '://' . $this->settings['account'] . '.zendesk.com';
		$this->api = new Zendesk_API( $this->zendesk_url );
		
		// Zendesk Authentication Magic
		$this->zendesk_user = false;
		global $current_user;
		wp_get_current_user();
		
		// If the current user is logged in
		if ( 0 != $current_user->ID ) {
			
			// Gather the Zendesk user options
			$this->user = $current_user;
			$this->zendesk_user = get_user_meta( $current_user->ID, 'zendesk_user_options', true );

			if ( $this->zendesk_user )
				$this->api->authenticate( $this->zendesk_user['username'], $this->zendesk_user['password'], false );

		}
	}
	
	/*
	 * Load Default Settings
	 * 
	 * Sets the defaults for the settings array and calls _update_settings()
	 * to write changes to the database. Generally run during plugin
	 * activation or first run.
	 * 
	 */
	private function _default_settings() {
		$this->settings = $this->default_settings;
		$this->remote_auth_settings = $this->default_remote_auth_settings;
		
		$this->_update_settings();
	}
	
	/*
	 * Load Settings
	 * 
	 * Private function to load current settings from the database. Sets
	 * settings to false if settings are not found (i.e. plugin is new).
	 * 
	 */
	private function _load_settings() {
		$this->settings = get_option( 'zendesk-settings', false );
		$this->remote_auth_settings = get_option( 'zendesk-settings-remote-auth', false );
		
		$this->default_settings = array(
			'version' => 1,
			'account' => '',
			
			'dashboard_administrator' => 'tickets-widget',
			'dashboard_editor' => 'contact-form',
			'dashboard_author' => 'contact-form',
			'dashboard_contributor' => 'contact-form',
			'dashboard_subscriber' => 'contact-form',
			
			'contact_form_anonymous' => false,
			'contact_form_anonymous_user' => false,
			'contact_form_title' => __( 'How can we help you?', 'zendesk' ) . '  ',
			'contact_form_summary' => __( 'Briefly describe your question', 'zendesk' ) . '  ',
			'contact_form_details' => __( 'Give us some further details', 'zendesk' ) . '  ',
			'contact_form_submit' => __( 'Submit', 'zendesk' ) . '  ',
			
			'dropbox_display' => 'none',
			'dropbox_code' => '',
			'ssl' => false
		);
		
		$this->default_remote_auth_settings = array(
			'enabled' => false,
			'token' => ''
		);
	}
	
	/*
	 * Delete Settings
	 * 
	 * Removes all Zendesk settings from the database, as well as flushes
	 * all the user's authentication settings. Use this during plugin
	 * deactivation.
	 * 
	 */
	private function _delete_settings() {
		delete_option( 'zendesk-settings' );
		delete_option( 'zendesk-settings-remote-auth' );
	}
	
	/*
	 * Update Settings
	 * 
	 * Use this private method after doing any changes to the settings
	 * arrays. This method writes the changes to the database.
	 * 
	 */
	private function _update_settings() {
		update_option( 'zendesk-settings', $this->settings );
		update_option( 'zendesk-settings-remote-auth', $this->remote_auth_settings );
	}
	
	/*
	 * Admin Initialization
	 * 
	 * Register a bunch of sections and field for the Zendesk plugin
	 * options. All the options are stored in the $this->settings
	 * array which is kept under the 'zendesk-settings' key inside
	 * the WordPress database.
	 * 
	 * @uses register_setting, add_settings_section, add_settings_field
	 * 
	 */
	public function _admin_init() {
		
		// Scripts and styles
		add_action( 'admin_print_styles', array( &$this, '_admin_print_styles' ) );
		
		// Comments columns & row actions
		add_filter( 'comment_row_actions', array( &$this, '_comment_row_actions' ), 10, 2 );
		add_filter( 'manage_edit-comments_columns', array( &$this, '_comments_columns_filter' ), 10, 1 );
		add_action( 'manage_comments_custom_column', array( &$this, '_comments_columns_action' ), 10, 1 );
		add_action( 'admin_notices', array( &$this, '_wp_admin_notices' ) );
		
		// General Settings
		register_setting( 'zendesk-settings', 'zendesk-settings', array( &$this, '_validate_settings' ) );
		
		// Authentication Details
		add_settings_section( 'authentication', __( 'Your Zendesk Account', 'zendesk' ), array( &$this, '_settings_section_authentication' ), 'zendesk-settings' );
		add_settings_field( 'account', __( 'Subdomain', 'zendesk' ), array( &$this, '_settings_field_account' ), 'zendesk-settings', 'authentication' );
		
		// Show SSL when debug is on.
		if ( ZENDESK_DEBUG )
			add_settings_field( 'ssl', __( 'Use SSL', 'zendesk' ), array( &$this, '_settings_field_ssl' ), 'zendesk-settings', 'authentication' );
		
		// Display the rest of the settings only if a Zendesk account has been specified.
		if ( $this->settings['account'] ) {
			// Dashboard Widget Section
			add_settings_section( 'dashboard_widget', __( 'Dashboard Widget Visibility', 'zendesk' ), array( &$this, '_settings_section_dashboard_widget' ), 'zendesk-settings' );
			add_settings_field( 'dashboard_administrator', __( 'Administrators', 'zendesk' ), array( &$this, '_settings_field_dashboard_access' ), 'zendesk-settings', 'dashboard_widget', array( 'role' => 'administrator' ) );
			add_settings_field( 'dashboard_editor', __( 'Editors', 'zendesk' ), array( &$this, '_settings_field_dashboard_access' ), 'zendesk-settings', 'dashboard_widget', array( 'role' => 'editor' ) );
			add_settings_field( 'dashboard_author', __( 'Authors', 'zendesk' ), array( &$this, '_settings_field_dashboard_access' ), 'zendesk-settings', 'dashboard_widget', array( 'role' => 'author' ) );
			add_settings_field( 'dashboard_contributor', __( 'Contributors', 'zendesk' ), array( &$this, '_settings_field_dashboard_access' ), 'zendesk-settings', 'dashboard_widget', array( 'role' => 'contributor' ) );
			add_settings_field( 'dashboard_subscriber', __( 'Subscribers', 'zendesk' ), array( &$this, '_settings_field_dashboard_access' ), 'zendesk-settings', 'dashboard_widget', array( 'role' => 'subscriber' ) );
			
			// Contact Form Section
			add_settings_field( 'contact_form_anonymous', __( 'Anonymous Requests', 'zendesk' ), array( &$this, '_settings_field_contact_form_anonymous' ), 'zendesk-settings', 'contact_form' );
			add_settings_field( 'contact_form_anonymous_user', __( 'Anonymous Requests By', 'zendesk' ), array( &$this, '_settings_field_contact_form_anonymous_user' ), 'zendesk-settings', 'contact_form' );
			
			add_settings_section( 'contact_form', __( 'Contact Form Settings', 'zendesk' ), array( &$this, '_settings_section_contact_form' ), 'zendesk-settings' );
			add_settings_field( 'contact_form_title', __( 'Form Title', 'zendesk' ), array( &$this, '_settings_field_contact_form_title' ), 'zendesk-settings', 'contact_form' );
			add_settings_field( 'contact_form_summary', __( 'Summary Label', 'zendesk' ), array( &$this, '_settings_field_contact_form_summary' ), 'zendesk-settings', 'contact_form' );
			add_settings_field( 'contact_form_details', __( 'Details Label', 'zendesk' ), array( &$this, '_settings_field_contact_form_details' ), 'zendesk-settings', 'contact_form' );
			add_settings_field( 'contact_form_submit', __( 'Submit Button Label', 'zendesk' ), array( &$this, '_settings_field_contact_form_submit' ), 'zendesk-settings', 'contact_form' );
						
			// Dropbox Settings
			add_settings_section( 'dropbox', __( 'Dropbox Settings', 'zendesk' ), array( &$this, '_settings_section_dropbox' ), 'zendesk-settings' );
			add_settings_field( 'dropbox_display', __( 'Display', 'zendesk' ), array( &$this, '_settings_field_dropbox_display' ), 'zendesk-settings', 'dropbox' );
			add_settings_field( 'dropbox_code', __( 'Dropbox Code', 'zendesk' ), array( &$this, '_settings_field_dropbox_code' ), 'zendesk-settings', 'dropbox' );
			
			// Remote Authentication Settings
			register_setting( 'zendesk-settings-remote-auth', 'zendesk-settings-remote-auth', array( &$this, '_validate_remote_auth_settings' ) );
			
			// Remote Authentication Section Zendesk
			add_settings_section( 'zendesk', __( 'Zendesk Configuration', 'zendesk' ), array( &$this, '_settings_remote_auth_section_zendesk' ), 'zendesk-settings-remote-auth' );
			add_settings_field( 'login_url', __( 'Remote Login URL', 'zendesk' ), array( &$this, '_settings_field_remote_auth_login_url' ), 'zendesk-settings-remote-auth', 'zendesk' );
			add_settings_field( 'logout_url', __( 'Remote Logout URL', 'zendesk' ), array( &$this, '_settings_field_remote_auth_logout_url' ), 'zendesk-settings-remote-auth', 'zendesk' );
			
			// Remote Authentication Section
			add_settings_section( 'general', __( 'General Settings', 'zendesk' ), array( &$this, '_settings_remote_auth_section_general' ), 'zendesk-settings-remote-auth' );
			add_settings_field( 'enabled', __( 'Remote Auth Status', 'zendesk' ), array( &$this, '_settings_field_remote_auth_enabled' ), 'zendesk-settings-remote-auth', 'general' );
			add_settings_field( 'token', __( 'Remote Auth Shared Token', 'zendesk' ), array( &$this, '_settings_field_remote_auth_token' ), 'zendesk-settings-remote-auth', 'general' );
			
			// Zendesk Forms
			$this->_process_forms();

		}
	}
	
	/*
	 * Admin Menu
	 * 
	 * Fired during the WordPress admin_menu hook, registers a new
	 * admin menu page called Zendesk Support, the contents callback
	 * is the semi-private _admin_menu_contents function.
	 * 
	 * @uses add_menu_page
	 * 
	 */
	public function _admin_menu() {
		add_menu_page( 'Zendesk Support Settings', 'Zendesk', 'manage_options', 'zendesk-support', array( &$this, '_admin_menu_contents' ), plugins_url( '/images/zendesk-16.png', __FILE__ ) );
		$settings_page = add_submenu_page( 'zendesk-support', __( 'Zendesk Support Settings', 'zendesk' ), __( 'Settings', 'zendesk' ), 'manage_options', 'zendesk-support', array( &$this, '_admin_menu_contents' ) );
		
		if ( $this->settings['account'] )
			add_submenu_page( 'zendesk-support', __( 'Zendesk Remote Authentication', 'zendesk' ), __( 'Remote Auth', 'zendesk' ), 'manage_options', 'zendesk-remote-auth', array( &$this, '_admin_menu_remote_auth_contents' ) );
		
		add_action( 'admin_print_styles-' . $settings_page, array( &$this, '_admin_print_styles_settings' ) );
	}
	
	/*
	 * Admin Styles & Scripts
	 * 
	 * This method is fired for any possible admin page, which is why
	 * we includet he main admin.js scripts and the colorbox to use
	 * for tickets widgets, and the comment to ticket forms.
	 * 
	 */
	public function _admin_print_styles() {
		// Admin Scripts		
		wp_enqueue_script( 'zendesk-admin', plugins_url( '/js/admin.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'zendesk-admin', plugins_url( '/css/admin.css', __FILE__ ) );
		wp_enqueue_script( 'colorbox', plugins_url( '/js/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'colorbox', plugins_url( '/css/colorbox.css', __FILE__ ) );

		
		wp_localize_script( 'zendesk-admin', 'zendesk', array(
			'plugin_url' => plugins_url( '', __FILE__ )
		) );
	}
	
	/*
	 * Admin Styles & Scripts on Settings page
	 * 
	 * This method is fired on the plugin settings page, handles the
	 * child settings showing and hiding, placeholders and more.
	 * 
	 */
	public function _admin_print_styles_settings() {
		wp_enqueue_script( 'zendesk-settings', plugins_url( '/js/settings.js', __FILE__ ), array( 'jquery', 'zendesk-admin' ) );
	}
	
	/*
	 * Dropbox Code
	 * 
	 * Displays the javascript code for the Zendesk Dropbox. The options
	 * in the $this->settings array are used for certain Zenbox options.
	 * Depending on the options chosen, this fire in wp_footer or via a 
	 * custom template tag: zendesk_insert_dropbox()
	 * 
	 */
	public function dropbox_code() {
		echo $this->settings['dropbox_code'];
	}
	
	/*
	 * Admin Menu Contents
	 * 
	 * The contents of the admin menu registered above for the Zendesk
	 * options. Below is one for remote auth options, uses the
	 * WordPress Settings API.
	 * 
	 */
	public function _admin_menu_contents() {
	?>
	<div class="wrap">
		<div id="icon-zendesk-32" class="icon32"><br></div>
		<h2><?php _e('Zendesk for WordPress Settings', 'zendesk'); ?></h2>
		
		<?php if ( ! $this->settings['account'] ): ?>
			<div id="message" class="updated below-h2 zendesk-info">
				<p><strong><?php _e( "You're almost there! Just one more thing...", 'zendesk' ); ?></strong></p>
				<p><?php _e( "Before you get your hands on all the juicy Zendesk for Wordpress features, we need to know your Zendesk subdomain. <br /> Your subdomain tells us who you are, and gives us access to the Zendesk API.", 'zendesk' ); ?></p>
			</div>
		<?php endif; ?>
		
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<?php settings_fields('zendesk-settings'); ?>
			<?php do_settings_sections('zendesk-settings'); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'zendesk'); ?>" />
			</p>
		</form>
	</div>
	<?php
		// Print settings array for debug.
		if ( ZENDESK_DEBUG )	echo '<pre>' . print_r($this->settings, true) . '</pre>';
	}
	
	/*
	 * Admin Menu Remote Auth Contents
	 * 
	 * The contents of the remote auth settings page registered in the
	 * admin menu. Uses the Settings API to render the options.
	 * 
	 */
	public function _admin_menu_remote_auth_contents() {
	?>
	<div class="wrap">
		<div id="icon-zendesk-32" class="icon32"><br></div>
		<h2><?php _e('Zendesk Remote Authentication Settings', 'zendesk'); ?></h2>
		<div id="message" class="updated below-h2">
			<p><strong><?php _e( 'Woah there Nelly!', 'zendesk' ); ?></strong></p>
			<p><?php _e( "Remote authentication takes a little bit of setup in here and inside Zendesk too. Don't worry, it's not rocket surgery.", 'zendesk' ); ?></p>
			<p><a target="_blank" href="https://support.zendesk.com/entries/20110872-setting-up-remote-authentication-for-wordpress"><?php _e( 'Check out this handy guide on getting it set up for WordPress.', 'zendesk' ); ?></a></p>
		</div>

		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<?php settings_fields('zendesk-settings-remote-auth'); ?>
			<?php do_settings_sections('zendesk-settings-remote-auth'); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'zendesk'); ?>" />
			</p>
		</form>
	</div>
	<?php
	}
	
	/*
	 * Settings Validation
	 * 
	 * Validates all the incoming settings, generally submitted from
	 * the Zendesk Settings admin page. Check, sanitize, strip and
	 * return. The returning array is stored in the database and then
	 * accessible through $this->settings.
	 * 
	 */
	public function _validate_settings( $settings ) {

		// Check for SSL activity and keep the version.
		$settings['ssl'] = $this->api->is_ssl( $settings['account'] );
		$settings['version'] = $this->default_settings['version'];
		
		// Validate the Zendesk Account
		if ( ! preg_match( '/^[a-zA-Z0-9]{0,}$/', $settings['account'] ) )
			unset( $settings['account'] );

		// Dashboard widgets visibility
		foreach ( array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ) as $role )
			if ( isset( $settings['dashboard_' . $role] ) && ! array_key_exists( $settings['dashboard_' . $role], $this->_available_dashboard_widget_options() ) )
				unset( $settings['dashboard_' . $role] );
		
		// Clean up contact form title and others
		foreach ( array( 'contact_form_title', 'contact_form_summary', 'contact_form_details', 'contact_form_submit' ) as $key )
			$settings[$key] = empty( $settings[$key] ) ? $this->default_settings[$key] : htmlspecialchars( trim( $settings[$key] ) );

		// Anonymous contact form (checkbox)
		if ( ! isset( $settings['contact_form_anonymous'] ) )
			$settings['contact_form_anonymous'] = false;


		// Nuke login credentials if account has changed.
		if ( $settings['account'] !== $this->settings['account'] ) {
			// Running a direct SQL query is *way* faster than meta querying users one by one.
			global $wpdb;
			$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'zendesk_user_options';" );
		}
		
		// Merge the submitted settings with the defaults. Second
		// argument will overwrite the first.
		if ( is_array( $this->settings ) )
			$settings = array_merge( $this->settings, $settings );
		else
			$settings = array_merge( $this->default_settings, $settings );
		
		return $settings;
	}
	
	/*
	 * Remote Auth Settings Validation
	 * 
	 * Validates remote authentication settings submitted through the
	 * settings page. Not too much settings here, nothing to validate.
	 * Accessible through $this->remote_auth_settings
	 * 
	 */
	public function _validate_remote_auth_settings( $settings ) {
		$settings['enabled'] = empty( $settings['token'] ) ? false : true;
		return $settings;
	}
	
	
	/*
	 * Get Current User Dashboard Widget (helper)
	 * 
	 * Internal function, returns the current user's dashboard widget
	 * settings based on his or her role and the plugin settings. The
	 * returned string is 'tickets-widget', 'contact-form' or 'none'.
	 * 
	 */
	private function _get_current_user_dashboard_widget() {
		$role = $this->_get_current_user_role();

		if ( array_key_exists( 'dashboard_' . $role, (array) $this->settings ) )
			return $this->settings['dashboard_' . $role];
			
		return 'none';
	}
	
	/*
	 * Form Processing
	 * 
	 * This method is fired during admin init, does all the form
	 * processing. Most of the forms are fired using the POST method,
	 * although some (such as logout) can use the GET. GET should be
	 * processed before post.
	 * 
	 */
	private function _process_forms() {

		// Logout
		if ( isset( $_REQUEST['zendesk-logout'] ) && $this->zendesk_user ) {
			$this->zendesk_user = false;
			delete_user_meta( $this->user->ID, 'zendesk_user_options' );
			wp_redirect( admin_url('?zendesk-logout-success=true') );
			die();
		}
		
		// Display a logout success message
		if ( isset( $_REQUEST['zendesk-logout-success'] ) )
			$this->_add_notice( 'zendesk_login', __( 'You have successfully logged out of your Zendesk account.', 'zendesk' ), 'confirm' );

		
		// Change tickets view, probably never reached since an AJAX call
		// is more likely to respond to such a request. Lave this just in case.
		if ( isset( $_REQUEST['zendesk-tickets-change-view'] ) && is_numeric( $_REQUEST['zendesk-tickets-change-view'] ) && $this->zendesk_user ) {
			
			// Is somebody trying to cheat?
			if ( $this->_get_current_user_dashboard_widget() != 'tickets-widget' ) {
				$this->_add_notice( 'zendesk_login', __( 'You are not allowed to view the tickets widget', 'zendesk' ), 'alert' );
				return;
			}
			
			// Fire a request to catch all available views.
			$requested_view = (int) $_REQUEST['zendesk-tickets-change-view'];
			$views = $this->api->get_views();
			
			if ( ! is_wp_error( $views ) ) {
				
				// Loop through the views and update the user meta.
				foreach ( $views as $view ) {
					if ( $view->id == $requested_view ) {
						$this->zendesk_user['default_view'] = array(
							'id' => $view->id,
							'title' => $view->title
						);
						
						// Update and redirect.
						update_user_meta( $this->user->ID, 'zendesk_user_options', $this->zendesk_user );
						wp_redirect( admin_url() );
						die();
					}
				}
			} else {
				// Views could not be fetched
				$this->_add_notice( 'zendesk_tickets_widget', $views->get_error_message(), 'alert' );
				return;
			}
		}
		
		// Gather and validate some form data	
		if ( ! isset( $_POST['zendesk-form-submit'], $_POST['zendesk-form-context'], $_POST['zendesk-form-data'] ) ) return;
		$context = $_POST['zendesk-form-context'];
		$form_data = $_POST['zendesk-form-data'];
		
		// Pick the right form processor		
		switch ( $context ) {
			case 'login':
				if ( ! isset( $form_data['username'], $form_data['password'] ) ) {
					$this->_add_notice( 'zendesk_login', __( 'All fields are required. Please try again.', 'zendesk' ), 'alert' );
					return;
				}
				
				$username = $form_data['username'];
				$password = $form_data['password'];
				
				$auth = $this->api->authenticate($username, $password);

				if ( ! is_wp_error( $auth ) ) {
					// Get the user views
					$views = $this->api->get_views();
					
					if ( ! is_wp_error( $views ) ) {
						$default_view = array_shift( $views );
					} else {
						$default_view = false;
						$default_view->id = 0;
						$default_view->title = __( 'My open requests', 'zendesk' );
					}
					
					// Since this is not a remote auth set remote_auth to
					// false.
					$this->zendesk_user = array(
						'username' => $username,
						'password' => $password,
						'roles' => $auth->roles,
						'default_view' => array(
							'id' => $default_view->id,
							'title' => $default_view->title,
						)
					);
					
					$this->_add_notice( 'zendesk_login', sprintf( __( 'Howdy, <strong>%s</strong>! You are now logged in to Zendesk.', 'zendesk' ), $auth->name ), 'confirm' );

					update_user_meta( $this->user->ID, 'zendesk_user_options', $this->zendesk_user );
				} else {
					$this->_add_notice( 'zendesk_login', $auth->get_error_message(), 'alert' );
				}
				
				break;
				
			case 'create-ticket':
			
				// Is somebody trying to cheat?
				if ( $this->_get_current_user_dashboard_widget() != 'contact-form' ) {
					$this->_add_notice( 'zendesk_login', __( 'You are not allowed to view the contact form.', 'zendesk' ), 'alert' );
					return;
				}

				if ( ! isset( $form_data['summary'], $form_data['details'] ) ) {
					$this->_add_notice( 'zendesk_contact_form', __( 'All fields are required. Please try again.', 'zendesk' ), 'alert' );
					return;
				}
				
				$summary = strip_tags( stripslashes( trim( $form_data['summary'] ) ) );
				$details = strip_tags( stripslashes( trim( $form_data['details'] ) ) );
				
				// Quick validation
				if ( empty( $summary ) || empty( $details ) ) {
					$this->_add_notice( 'zendesk_contact_form', __( 'All fields are required. Please try again.', 'zendesk' ), 'alert' );
					return;
				}

				// Either tickets.json or requests.json based on user role.
				if ( $this->_is_agent() ) {
					
					// Agent requests
					$response = $this->api->create_ticket( $summary, $details );
					
				} elseif ( ! $this->_is_agent() && $this->zendesk_user ) {
					
					// End-users request (logged in)
					$response = $this->api->create_request( $summary, $details );
					
				} else {
					
					// Anonymous requests (if allowed in plugin settings)
					if ( $this->settings['contact_form_anonymous'] && $this->_is_agent( $this->settings['contact_form_anonymous_user'] ) ) {
						
						// Find the agent to fire anonymous requests
						$agent = $this->_get_agent( $this->settings['contact_form_anonymous_user'] );
						
						// Make sure the agent is there and is an agent (again)
						if ( ! $agent ) {
							$this->_add_notice( 'zendesk_contact_form', __( 'Something went wrong. We could not use the agent to fire this request.', 'zendesk' ), 'alert' );
							break;
						}
						
						// Awkwward!
						if ( $agent['username'] == $this->user->user_email ) {
							$this->_add_notice( 'zendesk_contact_form', sprintf( __( 'Wow, you managed to fire a request "on behalf of" yourself! Why don\'t you <a href="%s">login first</a>?', 'zendesk' ), admin_url('?zendesk-login-form=true') ) , 'alert' );
							break;
						}
						
						// Clone the current API settings and change the authentication pair
						$api = clone $this->api;
						$api->authenticate( $agent['username'], $agent['password'], false );
						
						// Fire a new ticket using the current user's name and email, similar to comments to tickets thing.
						$response = $api->create_ticket( $summary, $details, $this->user->display_name, $this->user->user_email );
						
						// Get rid of the cloned object
						unset( $api );
					}
				}
				
				// Error handling
				if ( ! is_wp_error( $response ) ) {
					$this->_add_notice( 'zendesk_contact_form', __( 'Your request has been created successfully!', 'zendesk' ), 'confirm' );
				}	else {
					$this->_add_notice( 'zendesk_contact_form', $response->get_error_message(), 'alert' );
				}
				
				break;
		}
	}
	
	/*
	 * AJAX Response: Convert to Ticket
	 * 
	 * This request responds when attempting to convert a comment into
	 * a ticket. Does not do any conversions, nor requests to the API,
	 * simply passes in the current user and the requested comment. The
	 * function below does the rest.
	 * 
	 */
	public function _ajax_convert_to_ticket() {
		if ( isset( $_REQUEST['comment_id'] ) && is_numeric( $_REQUEST['comment_id'] ) && $this->_is_agent() ) {
			$comment_id = $_REQUEST['comment_id'];
			$comment = get_comment( $comment_id );
			
			// Comment found
			if ( $comment ) {
				$html = array();
				
				$html[] = '<div class="zendesk-comment-to-ticket">';
					
					$html[] = get_avatar( $comment->comment_author_email, 40 );
					$html[] = '<div class="zendesk-comment-box">';
						$html[] = '<div class="zendesk-comment-arrow"></div>';
						$html[] = '<p class="zendesk-author">' . sprintf( __( '<strong>%s</strong> said...', 'zendesk' ), $comment->comment_author ) . '</p>';
						$html[] = wpautop( $this->_excerpt( strip_tags( $comment->comment_content ), 70 ) );
						$html[] = '<p class="zendesk-comment-date">' . date( get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime( $comment->comment_date ) ) . '</p>';
					$html[] = '</div>';
					
					$html[] = '<br class="clear" />';
					$html[] = '<p class="zendesk-after-comment-box">' . __( 'A new ticket will be created inside your Zendesk account, and your response below will be added as a comment to that ticket.', 'zendesk' ) . '</p>';

					$html[] = get_avatar( $this->zendesk_user['username'], 40 );
					$html[] = '<div class="zendesk-comment-box">';
						$html[] = '<div class="zendesk-comment-arrow"></div>';
						$html[] = '<p class="zendesk-author">' . __( '<strong>You</strong> say:', 'zendesk' ) . '</p>';
						$html[] = '<form class="zendesk-comment-to-ticket-form">';
							$html[] = '<textarea name="zendesk-comment-reply" class="zendesk-comment-reply"></textarea>';
							$html[] = '<br class="clear" />';
							$html[] = '<div class="zendesk-options">';
								$html[] = '<label><input name="zendesk-comment-public" value="1" checked="checked" type="checkbox" /> ' . __( 'Make this a public comment in the ticket', 'zendesk' ) . '</label>';
								$html[] = '<label><input name="zendesk-post-reply" value="1" type="checkbox" /> ' . __( 'Post as a reply on this blog post', 'zendesk' ) . '</label>';						
							$html[] = '</div>';
							$html[] = '<input type="hidden" name="zendesk-comment-id" value="' . $comment->comment_ID . '" />';
							$html[] = '<input type="submit" class="button-primary zendesk-submit" value="' . __( 'Create ticket', 'zendesk' ) . '" /><div class="zendesk-loader" style="display: none;">loading</div>';
							$html[] = '<br class="clear" /><div class="zendesk-notices"></div>';
						$html[] = '</form>';
					$html[] = '</div>';
				
				$html[] = '</div>';
				$html[] = '<br class="clear" />';

				$html = implode( "\n", $html );
				
				$response = array(
					'status' => 200,
					'html' => $html
				);
			}
		}
		
		echo json_encode( $response );
		die();
	}
	
	/*
	 * AJAX Response: Convert to Ticket POST
	 * 
	 * This requests responds upon the actual posting of the comments
	 * to tickets integration, i.e. when the agent has typed a response
	 * message and clicked the Create ticket button. The whole logics of
	 * creating the ticket, attaching a comment to the ticket (private,
	 * or public), associating a WordPress comment with the ticket and
	 * posting back a WordPress comment as a reply happens here.
	 * 
	 */
	public function _ajax_convert_to_ticket_post() {

		// If a different response is not set use this one.
		$response = array(
			'status' => 500,
			'error' => __( 'Whoopsie! Problem communicating with Zendesk. Try that again.', 'zendesk' )
		);
		
		// Some validation
		if ( isset( $_REQUEST['comment_id'] ) && is_numeric( $_REQUEST['comment_id'] ) && $this->_is_agent() ) {
			$comment_id = $_REQUEST['comment_id'];
			$comment = get_comment( $comment_id );
			
			// Make sure it's a valid comment
			if ( $comment && $comment->comment_type != 'pingback' ) {
				
				// Fetch the associated post
				$post = get_post( $comment->comment_post_ID );
				
				// Fetch the incoming data
				$message = trim( stripslashes( $_REQUEST['message'] ) );
				$comment_public = isset( $_REQUEST['comment_public'] ) ? true : false;
				$post_reply = isset( $_REQUEST['post_reply'] ) ? true : false;
				
				// Let's format the new ticket
				$subject = $post->post_title . ': ' . $this->_excerpt( strip_tags( $comment->comment_content ), 5 );
				$description = strip_tags( $comment->comment_content );
				$requester_name = $comment->comment_author;
				$requester_email = $comment->comment_author_email;
				
				// Create the ticket
				$ticket_id = $this->api->create_ticket( $subject, $description, $requester_name, $requester_email );
				
				if ( ! is_wp_error( $ticket_id ) ) {
					
					// Ticket went okay so update the comment meta to associated it.
					update_comment_meta( $comment->comment_ID, 'zendesk-ticket', $ticket_id );
					
					// If we have a message set
					if ( strlen( $message ) ) {

						// Post a comment to the ticket
						$ticket_comment = $this->api->create_comment( $ticket_id, $message, $comment_public );
						
						if ( ! is_wp_error( $ticket_comment ) ) {
							
							// Let's see if we need to post a comment back to WordPress.
							if ( $post_reply ) {
								$wp_comment = array(
									'comment_post_ID' => $post->ID,
									'comment_author' => $this->user->display_name,
									'comment_author_email' => $this->user->user_email,
									'comment_content' => $message,
									'comment_parent' => $comment_id,
									'user_id' => $this->user->ID,
									'comment_date' => current_time( 'mysql' ),
									'comment_approved' => 1
								);
								wp_insert_comment( $wp_comment );
							}
							
							$response = array(
								'status' => 200,
								'ticket_id' => $ticket_id,
								'ticket_url' => $this->_ticket_url( $ticket_id )
							);
							
						} else {
							
							// The ticket was created but the comment didn't get through.
							$response = array(
								'status' => 500,
								'error' => __( 'A ticket has been created, but failed to post a comment to it.', 'zendesk' )
							);
						}
					} else {
						
						// A message is not set but the ticket was created.						
						$response = array(
							'status' => 200,
							'ticket_id' => $ticket_id,
							'ticket_url' => $this->_ticket_url( $ticket_id )
						);
						
					}

				} else {
					
					// Failed to create the ticket.
					$response = array(
						'status' => 500,
						'error' => $ticket_id->get_error_message()
					);
				}
			}
		}
		
		// Return the response JSON
		echo json_encode( $response );
		die();
	}
	
	/*
	 * AJAX Response: View Ticket Comments
	 * 
	 * This is an AJAX response to the zendesk_view_comments request which
	 * displays a colorbox with the ticket comments. This is available
	 * to agents only.
	 * 
	 */
	public function _ajax_view_comments() {
		if ( isset( $_REQUEST['ticket_id'] ) && is_numeric( $_REQUEST['ticket_id'] ) && $this->_is_agent() ) {
			$ticket_id = $_REQUEST['ticket_id'];
			
			$ticket = $this->api->get_ticket_info( $ticket_id );
			
			if ( ! is_wp_error( $ticket ) ) {
				
				$html = array();
				$html[] = '<div class="zendesk-comment-to-ticket">';
				
				foreach ( $ticket->comments as $comment ) {
					
					$author = $this->api->get_user( $comment->author_id );

					if ( is_wp_error( $author ) ) {
						$author = null;
						$author->name = 'Unknown';
						$author->email = 'unknown@zendesk.com';
					}
					
					$html[] = '<a target="_blank" href="' . $this->_user_url( $comment->author_id ) . '">' . get_avatar( $author->email, 40 ) . '</a>';
					$html[] = '<div class="zendesk-comment-box">';
						$html[] = '<div class="zendesk-comment-arrow"></div>';
						$html[] = '<p class="zendesk-author">' . sprintf( __( '%s said...', 'zendesk' ), '<a target="_blank" href="' . $this->_user_url( $comment->author_id ) . '">' . $author->name .  '</a>' ) . '</p>';
						$html[] = wpautop( $comment->value );
						
						// Let's see if we have any attachments there.
						if ( isset( $comment->attachments ) && count( $comment->attachments) ) {
							$html[] = '<div class="zendesk-comment-attachments">';
							
							foreach ( $comment->attachments as $attachment )
								$html[] = '<p class="zendesk-comment-attachment"><a target="_blank" href="' . $attachment->url . '">' . $attachment->filename . '</a> <span class="zendesk-attachment-size">(' . $this->_file_size( $attachment->size ) . ')</span></p>';

							$html[] = '</div>';
						}
						
						$html[] = '<p class="zendesk-comment-date">' . date( get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime( $comment->created_at ) ) . '</p>';
					$html[] = '</div>';
					
					$html[] = '<br class="clear" />';

				}
				
				$html[] = '</div>';
				$html[] = '<br class="clear" />';
				
				$html = implode( "\n", $html );

				$response = array(
					'status' => 200,
					'html' => $html
				);			
			}
		}
		
		echo json_encode( $response );
		die();
	}
	
	/*
	 * AJAX Response: Get View
	 * 
	 * This method is fired by WordPress wehn requesting via the AJAX
	 * API and the zendesk_get_view action is set. Gathers the view
	 * into an HTML table and outputs as a JSON response.
	 * 
	 */
	public function _ajax_get_view() {
		if ( isset( $_REQUEST['view_id'] ) && is_numeric( $_REQUEST['view_id'] ) && $this->_is_agent() ) {
			$requested_view = $_REQUEST['view_id'];
			
			// Is somebody trying to cheat?
			if ( $this->_get_current_user_dashboard_widget() != 'tickets-widget' ) return;
			$views = $this->api->get_views();

			if ( ! is_wp_error( $views ) ) {
				foreach ( $views as $view ) {
					if ( $view->id == $requested_view ) {
						$this->zendesk_user['default_view'] = array(
							'id' => $view->id,
							'title' => $view->title
						);
						
						update_user_meta( $this->user->ID, 'zendesk_user_options', $this->zendesk_user );
						break;
					}
				}
			}

			// API requests based on the Zendesk role.
			$tickets = $this->api->get_tickets_from_view( (int) $this->zendesk_user['default_view']['id'] );

			// Empty the arrays if they are errors.
			if ( is_wp_error( $tickets ) ) { $tickets = array(); }
			
			$response = array(
				'status' => 200,
				'html' => $this->_get_tickets_widget_html( $tickets )
			);
		} else {
			$response = array(
				'status' => 403,
				'error' => __( 'Access denied', 'zendesk' )
			);
		}
		
		echo json_encode( $response );
		die();
	}
	
	/*
	 * AJAX Response: View Ticket
	 * 
	 * This method is fired by WordPress when requesting via the AJAX
	 * API and the zendesk_view_ticket action is set. Gathers the info
	 * given the ticket id and returns a JSON object containing a status
	 * code, the ticket details, and the ticket data formatted in an
	 * HTML table.
	 * 
	 */
	public function _ajax_view_ticket() {
		
		if ( isset( $_REQUEST['ticket_id'] ) && is_numeric( $_REQUEST['ticket_id'] ) ) {

			$ticket_id = $_REQUEST['ticket_id'];
		
			// API requests based on the Zendesk role.
			if ( $this->_is_agent() ) {
				$ticket = $this->api->get_ticket_info( $ticket_id );
			} else {
				$ticket = $this->api->get_request_info( $ticket_id );
			}
			
			// If there was no error fetch further
			if ( ! is_wp_error( $ticket ) ) {
				
				// If there's a requester ID let's resolve it
				if ( isset( $ticket->requester_id ) ) {
					$requester = $this->api->get_user( $ticket->requester_id );
					
					if ( ! is_wp_error( $requester ) ) {
						$requester = $requester->name;
					} else {
						$requester = __( 'Unknown', 'zendesk' );
					}
				// Otherwise set it to blank, blank fields don't show up.
				} else {
					$requester = '';
				}
				
				// Updated field is not viewable by end-users, so if it's
				// not set then set it to blank.
				if ( ! isset( $ticket->updated_at ) )
					$ticket->updated_at = '';

				// Create the table values, where key is the label and value
				// is the value, doh!
				$table_values = array(
					__( 'Subject:', 'zendesk' ) => htmlspecialchars( $ticket->subject ),
					__( 'Description:', 'zendesk' ) => nl2br( htmlspecialchars( $ticket->description ) ),
					__( 'Ticket Status:', 'zendesk' ) => '<span class="zendesk-status-' . $this->_ticket_status_class_name( $ticket->status_id ) . '">' . $this->_ticket_status( $ticket->status_id ) . '</span>',
					__( 'Requested by:', 'zendesk' ) => '<a target="_blank" href="' . $this->_user_url( $ticket->requester_id ) . '">' . $requester . '</a>',
					__( 'Created:', 'zendesk' ) => date( get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime( $ticket->created_at ) ),
					__( 'Updated:', 'zendesk' ) => date( get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime( $ticket->updated_at ) )
				);
				
				// Agents only data
				if ( $this->_is_agent() ) {
					
					// Custom fields
					$table_custom_field_values = array();
					$ticket_fields = $this->api->get_ticket_fields();
					
					// Perhaps optimize this a little bit, though this is
					// the way values come in from Zendesk.
					if ( ! is_wp_error( $ticket_fields ) && ! empty( $ticket_fields ) ) {
						foreach ( $ticket_fields as $field ) {
							if ( ! isset( $field->custom_field_options ) ) continue;
							foreach ( $ticket->ticket_field_entries as $field_entry ) {
								if ( $field_entry->ticket_field_id == $field->id ) {
									foreach ( $field->custom_field_options as $option ) {
										if ( $option->value == $field_entry->value ) {
											$table_custom_field_values[$field->title] = $option->name;
										}
									}
								}
							}
						}
					}
					
					$table_actions = array(
						__( 'Comments:', 'zendesk' ) => '<a data-id="' . $ticket->nice_id . '" href="#" class="zendesk-view-comments">' . __( 'View the comments thread', 'zendesk' ) . '</a>',
						__( 'View:', 'zendesk' ) => '<a target="_blank" href="' . $this->_ticket_url( $ticket->nice_id ) . '">' . __( 'View this ticket on Zendesk', 'zendesk' ) . '</a>'
					);

				}
				
				// Use these for debug values
				//$table_values['Ticket'] = print_r($ticket, true);
				//$table_values['Fields'] = print_r($ticket_fields, true);
				
				// Start formatting the general HTML table.
				$html  = '<table id="zendesk-ticket-details-table" class="zendesk-ticket-details-table">';
				
				foreach ( $table_values as $label => $value ) {
					if ( strlen( $value ) < 1 ) continue;
					$html .= '<tr><td class="zendesk-first"><span class="description">' . $label . '</span></td>';
					$html .= '<td>' . $value . '</td></tr>';
				}
				
				// Custom Fields Table (agents only)
				if ( isset( $table_custom_field_values ) && ! empty( $table_custom_field_values ) ) {
					$html .= '<tr><td colspan="2"><p class="zendesk-heading" style="margin-bottom: 0px;">' . __( 'Custom Fields', 'zendesk' ) . '</p></td></tr>';
					
					foreach ( $table_custom_field_values as $label => $value ) {
						if ( strlen( $value ) < 1 ) continue;
						$html .= '<tr><td class="zendesk-first"><span class="description">' . $label . '</span></td>';
						$html .= '<td>' . $value . '</td></tr>';
					}
				}
				
				// Actions Table (agents only)
				if ( isset( $table_actions ) && ! empty( $table_actions ) ) {
					$html .= '<tr><td colspan="2"><p class="zendesk-heading" style="margin-bottom: 0px;">' . __( 'Actions', 'zendesk' ) . '</p></td></tr>';
					foreach ( $table_actions as $label => $value ) {
						$html .= '<tr><td class="zendesk-first"><span class="description">' . $label . '</span></td>';
						$html .= '<td>' . $value . '</td></tr>';
					}
				}
				
				$html .= '</table>';
				
				// Format the response to output.
				$response = array(
					'status' => 200,
					'ticket' => $ticket,
					'html' => $html,
				);
				
			} else {
				
				// Something went wrong
				$response = array(
					'status' => 404,
					'data' => $ticket->get_error_message()
				);
			}
			
		} else {
			
			// Something went really wrong
			$response = array(
				'status' => 404,
				'data' => __( 'Ticket was not found.', 'zendesk' )
			);
		}
		
		// Output the response array as a JSON object.
		echo json_encode( $response );
		die();
	}
	
	
	/*
	 * Dashboard Widget Setup
	 * 
	 * This function checks the current user's Zendesk credentials as well
	 * as the plugin settings for Dashboard Widget, then displays the
	 * correct widget. All dashboard widgets have the same ID, meaning
	 * that only one instance could be used every time. This is done to
	 * keep the widget's sort order once set.
	 * 
	 * @uses wp_add_dashboard_widget
	 * 
	 */
	function _dashboard_widget_setup() {
		$widget_options = $this->_get_current_user_dashboard_widget();
		
		// If the plugin hasn't been configured yet.
		if ( ! isset( $this->settings['account'] ) || empty( $this->settings['account'] ) && $widget_options != 'none' ) {
			wp_add_dashboard_widget( 'zendesk-dashboard-widget', __( 'Zendesk Support', 'zendesk' ), array( &$this, '_dashboard_widget_config' ) );
			return;
		}
		
		if ( ! $this->zendesk_user && $widget_options == 'contact-form' && $this->settings['contact_form_anonymous'] && $this->_is_agent( $this->settings['contact_form_anonymous_user'] ) ) {
			wp_add_dashboard_widget( 'zendesk-dashboard-widget', $this->settings['contact_form_title'], array( &$this, '_dashboard_widget_contact_form' ) );
			return;
		}

		if ( ! $this->zendesk_user && $widget_options != 'none'	 ) {
			wp_add_dashboard_widget( 'zendesk-dashboard-widget', __( 'Zendesk Support Login', 'zendesk' ), array( &$this, '_dashboard_widget_login' ) );
		} else {
			
			// Based on user role and the plugin settings.
			switch ( $widget_options ) {
				case 'contact-form':
					wp_add_dashboard_widget( 'zendesk-dashboard-widget', $this->settings['contact_form_title'], array( &$this, '_dashboard_widget_contact_form' ) );
					break;
					
				case 'tickets-widget':
					wp_add_dashboard_widget( 'zendesk-dashboard-widget', __( 'Zendesk for WordPress', 'zendesk' ), array( &$this, '_dashboard_widget_tickets' ) );
					break;
			}
		}
	}
	
	/*
	 * Dashboard Widget Config
	 * 
	 * This widget is displayed if the plugin is activated, but the
	 * administrator has not set up the account settings yet.
	 * 
	 */
	public function _dashboard_widget_config() {
	?>
		<div class="inside">
			<?php if ( current_user_can( 'manage_options' ) ): ?>
			  <img class="zendesk-buddha-smaller" src="<?php echo plugins_url( '/images/zendesk-32-color.png', __FILE__ ); ?>" alt="Zendesk" />
				<p class="description"><?php printf( __( "Howdy! You're almost ready to go, we just need you to <a href='%s'>set up a few things first.</a>", 'zendesk' ), admin_url( 'admin.php?page=zendesk-support' ) ); ?></p>
			<?php else: ?>
			  <img class="zendesk-buddha-smaller" src="<?php echo plugins_url( '/images/zendesk-32-color.png', __FILE__ ); ?>" alt="Zendesk" />
				<p class="description"><?php _e( "Howdy! Looks like the WordPress administrator hasn't set this plugin up yet. Give them a poke to get moving!", 'zendesk' ); ?></p>
			<?php endif; ?>
		</div>
	<?php
	}
	
	/*
	 * Dashboard Widget: Login
	 * 
	 * The login dashboard widget, displayed to the users that are logged
	 * in, but not into their Zendesk account. Zendesk account credentials
	 * are kept in the zendesk_user_options user meta field in the database,
	 * loaded during admin_init and could be accessed via $this->zendesk_user.
	 * 
	 */
	public function _dashboard_widget_login() {
	?>
		<div class="inside">
			<?php $this->_do_notices( 'zendesk_login' ); ?>
			
			<img class="zendesk-buddha" src="<?php echo plugins_url( '/images/zendesk-32-color.png', __FILE__ ); ?>" alt="Zendesk" />
			<p class="description"><?php _e( 'Use your Zendesk account credentials to log in the form below. Please note that these are not your WordPress username and password.', 'zendesk' ); ?></p>
			<form id="zendesk-login" method="post" action="<?php echo admin_url(); ?>">
				<input type="hidden" name="zendesk-form-submit" value="1" />
				<input type="hidden" name="zendesk-form-context" value="login" />
				<p>
					<label><?php _e( 'Username:', 'zendesk' ); ?></label>
					<input name="zendesk-form-data[username]" type="text" class="regular-text" value="<?php echo $this->user->user_email; ?>" /><br />
				</p>
				<p>
					<label><?php _e( 'Password:', 'zendesk' ); ?></label>
					<input name="zendesk-form-data[password]" type="password" class="regular-text" />
				</p>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Login to Zendesk', 'zendesk'); ?>" /><br />
					<?php _e( "Don't have an account?", 'zendesk' ); ?> <a href="<?php echo trailingslashit($this->zendesk_url); ?>registration"><?php _e( 'Sign up!', 'zendesk' ); ?></a>
				</p>
				<br class="clear" />
			</form>
		</div>
	<?php
	}
		
	/*
	 * Dashboard Widget: Contact Form
	 * 
	 * Displays the Contact Form widget in the dashboard. Upon processing,
	 * a new ticket is created via the Zendesk API with the Summary and
	 * Details filled out in this form. A logout link is also present.
	 * 
	 */
	public function _dashboard_widget_contact_form() {
	?>
		<div class="inside">
			<?php
				$this->_do_notices( 'zendesk_login' );
				$this->_do_notices( 'zendesk_contact_form' );
			?>
			<form id="zendesk-contact-form" method="post" action="<?php echo admin_url(); ?>">
				<input type="hidden" name="zendesk-form-submit" value="1" />
				<input type="hidden" name="zendesk-form-context" value="create-ticket" />
				<p>
					<label><?php echo $this->settings['contact_form_summary']; ?></label>
					<input name="zendesk-form-data[summary]" class="large-text" type="text" />
				</p>
				
				<p>
					<label><?php echo $this->settings['contact_form_details']; ?></label>
					<textarea id="zendesk-contact-form-details" name="zendesk-form-data[details]" class="large-text" style="height: 10em;"></textarea>
				</p>
				
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php echo esc_attr( trim( $this->settings['contact_form_submit'] ) ); ?>" /> 
					
					<?php if ( $this->zendesk_user ): ?>
						<?php printf( __( 'Logged in as <strong>%s</strong>', 'zendesk' ), $this->zendesk_user['username'] ); ?> (<a href="?zendesk-logout=true"><?php _e( 'logout', 'zendesk' ); ?></a>)
					<?php endif; ?>
					
					
					<a target="_blank" href="http://zendesk.com/?source=wordpress-plugin" class="powered-by-zendesk"><?php _e( 'powered by Zendesk', 'zendesk' ); ?></a>
				</p>
				<br class="clear" />
			</form>
		</div>
	<?php
	}
	
	/*
	 * Dashboard Widget: Tickets
	 * 
	 * This method displays the tickets widget in the dashboard screen.
	 * Depending on logged in user Zendesk role, the tickets or the
	 * requests are shown, with an option to change view. Views are
	 * gathered from Zendesk via the API. This view has also got the
	 * place holder for single ticket views accessed via AJAX calls.
	 * 
	 */
	public function _dashboard_widget_tickets() {
		?>
		<div class="inside">
		<?php
			// API requests based on the Zendesk role.
			if ( $this->_is_agent() ) {
				$tickets = $this->api->get_tickets_from_view( (int) $this->zendesk_user['default_view']['id'] );
				$views = $this->api->get_views();
			} else {
				$tickets = $this->api->get_requests();
				$views = array();
			}

			// Empty the arrays if they are errors.
			if ( is_wp_error( $views ) ) { 
				$this->_add_notice( 'zendesk_tickets_widget', $views->get_error_message(), 'alert' );
				$views = array(); 
			}
			
			if ( is_wp_error( $tickets ) ) { 
				$this->_add_notice( 'zendesk_tickets_widget', $tickets->get_error_message(), 'alert' );
				$tickets = array();
			}
			
			// Notifications
			$this->_do_notices( 'zendesk_login' );
			$this->_do_notices( 'zendesk_tickets_widget' );
		?>
		</div>
		<div class="zendesk-tickets-widget">
		
			<!-- Dashboard Widget Main View -->
			<div class="zendesk-tickets-widget-main">
				<?php echo $this->_get_tickets_widget_html( $tickets ); ?>
			</div>
			
			<!-- Dashboard Widget Select View -->
			<div class="zendesk-tickets-widget-views" style="display: none;">
				<p class="zendesk-heading"><?php _e( 'Change view', 'zendesk' ); ?> <span class="zendesk-heading-link">(<a class="zendesk-change-view-cancel" href="<?php echo admin_url(); ?>"><?php _e( 'cancel', 'zendesk' ); ?></a>)</span></p>
				<table class="zendesk-views-table">
				<?php
					if ( count( $views ) > 0 && is_array( $views ) ):
						foreach ( $views as $view ):
				?>
					<tr>
						<td>
							<?php if ( $view->is_active != 1 ): ?>
							<span class="zendesk-view-empty">
								<?php echo $view->title; ?>
							</span>
							<?php else: ?>
							<a data-id="<?php echo $view->id; ?>" href="<?php echo admin_url(); ?>?zendesk-tickets-change-view=<?php echo $view->id; ?>">
								<?php echo $view->title; ?>
							</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php
						endforeach;
					else: // no views
				?>
					<tr>
						<td><span class="description"><?php _e( 'There are no views available for this account.', 'zendesk' ); ?></span></td>
					</tr>
				<?php
					endif;
				?>
				</table>
			</div>
			
			<!-- Dashboard Widget Single View -->
			<div class="zendesk-tickets-widget-single" style="display: none;">
				<p class="zendesk-heading"><?php _e( 'Viewing Ticket', 'zendesk' ); ?> <span id="zendesk-ticket-title"></span> <span class="zendesk-heading-link">(<a class="zendesk-change-single-cancel" href="<?php echo admin_url(); ?>"><?php _e( 'back', 'zendesk' ); ?></a>)</span></p>
				<div id="zendesk-ticket-details-placeholder"></div>
			</div>
			
			<!-- Dashboard Widget Bottom -->
			<br class="clear" />
			<div class="zendesk-tickets-bottom">
				<p>
					<a target="_blank" href="<?php echo trailingslashit( $this->zendesk_url ); ?>" class="button"><?php _e( 'My Helpdesk', 'zendesk' ); ?></a>
					<?php _e( 'Logged in as', 'zendesk' ); ?> <strong><?php echo $this->zendesk_user['username']; ?></strong> (<a href="?zendesk-logout=true"><?php _e( 'logout', 'zendesk' ); ?></a>) 
					<a target="_blank" href="http://zendesk.com/?source=wordpress-plugin" class="powered-by-zendesk"><?php _e( 'powered by Zendesk', 'zendesk' ); ?></a>
				</p>
			</div>

		</div>
		<br class="clear" />
		<?php
	}
	
	
	/*
	 * Get Tickets Widget HTML (helper)
	 * 
	 * This function returns the tickets table and current view as HTML.
	 * Inteded to use inside the tickets view widget, passed on to the
	 * AJAX responses that loads different views without refreshing.
	 * 
	 */
	private function _get_tickets_widget_html( $tickets ) {
		$html = array();
		
		// Heading
		$html[] = '<p class="zendesk-heading">' . $this->zendesk_user['default_view']['title']; 
		if ( $this->_is_agent() )
			$html[] = '<span class="zendesk-heading-link">(<a class="zendesk-change-view" href="#">' . __( 'change view', 'zendesk' ) . '</a>)</span>';
		$html[] = '</p>';
		
		$html[] = '<table class="zendesk-tickets-table">';
		
		if ( count( $tickets ) > 0 && is_array( $tickets ) ) {
			foreach ( $tickets as $ticket ) {
				
				if ( ! strlen( $ticket->subject ) )
					$ticket->subject = $this->_excerpt( $ticket->description, 15 );
				
				$html[] = '<tr>';
				$html[] = '<td class="zendesk-ticket-id"><div class="zendesk-loader" style="display: none"></div><a class="zendesk-ticket-id-text zendesk-ticket-view" data-id="' . $ticket->nice_id . '" href="' . $this->_ticket_url( $ticket->nice_id ) . '">#' . $ticket->nice_id . '</a></td>';
				$html[] = '<td><a class="zendesk-ticket-view zendesk-ticket-subject" data-id="' . $ticket->nice_id . '" href="' . $this->_ticket_url( $ticket->nice_id ) . '">' . $ticket->subject . '</a></td>';
				$html[] = '<td class="zendesk-ticket-status"><a href="' . $this->_ticket_url( $ticket->nice_id ) . '" target="_blank" class="zendesk-status-' . $this->_ticket_status_class_name( $ticket->status_id ) . '">' . $this->_ticket_status( $ticket->status_id ) . '</a></td>';
				$html[] = '</tr>';
			}
		} else {
			$html[] = '<tr><td><span class="description">' . __( 'There are no tickets in this view.', 'zendesk' ) . '</span></td></tr>';
		}
		
		$html[] = '</table>';
		
		// Glue the HTML pieces and delimit with a line break
		return implode( "\n", $html );
	}
	
	/*
	 * Get Available Dashboard Widget Options (helper)
	 * 
	 * Returns an array with the available dashboard widget options,
	 * where the array key is stored in the database and the array
	 * value is displayed (thus localized) to the user.
	 * 
	 */
	private function _available_dashboard_widget_options() {
		return array(
			'none' => __( "Don't display anything", 'zendesk' ),
			'contact-form' => __( 'Show a Contact Form', 'zendesk' ),
			'tickets-widget' => __( 'Show the Tickets widget', 'zendesk' ),
		);
	}
	
	
	/*
	 * Comment Row Actions
	 * 
	 * Filtered at comment_row_actions, displays a Convert to Zendesk
	 * ticket in the admin panel (comments view).
	 * 
	 */
	public function _comment_row_actions( $actions, $comment ) {
		
		// Do some validation, only agents can convert comments to tickets.
		// Pingbacks cannot be converted to tickets, and comments already
		// converted too.
		
		if ( $this->_is_agent() && $comment->comment_type != 'pingback' && ! get_comment_meta( $comment->comment_ID, 'zendesk-ticket', true ) )
			$actions['zendesk'] = '<a class="zendesk-convert" href="#" data-id="' . $comment->comment_ID . '">' . __( 'Convert to Zendesk Ticket', 'zendesk' ) . '</a>';

		return $actions;
	}
	
	/*
	 * Comment Columns Filter
	 * 
	 * Adds an extra column to the comments table with the "zendesk" key
	 * and "Zendesk" as the caption.
	 * 
	 */
	public function _comments_columns_filter( $columns ) {
		if ( $this->_is_agent() )
			$columns['zendesk'] = 'Zendesk';
		
		return $columns;
	}

	/*
	 * Comment Columns Action
	 * 
	 * Works in pair with the function above, scans for when a table
	 * contains the 'zendesk' column and whether the current user is
	 * an agent.
	 * 
	 */
	public function _comments_columns_action( $column ) {
		global $comment;
		if ( $column == 'zendesk' && $this->_is_agent() ) {
			$ticket_id = get_comment_meta( $comment->comment_ID, 'zendesk-ticket', true );
			
			// Make sure it's valid before printing.
			if ( $comment->comment_type != 'pingback' && $ticket_id )
				echo '<a target="_blank" class="zendesk-comment-ticket-id" href="' . $this->_ticket_url( $ticket_id ) . '">#' . $ticket_id . '</a>';
		}
	}

	
	/*
	 * Remote Authentication Process
	 * 
	 * This is fired during plugin setup, i.e. during the init WordPress
	 * action, thus we have control over any redirects before the request
	 * is ever processed by the WordPress interpreter.
	 * 
	 * Remote Auth is described here: http://www.zendesk.com/api/remote-authentication
	 * 
	 * This method does both login and logout requests.
	 * 
	 */
	public function _do_remote_auth() {
		// This is a login request.
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'zendesk-remote-login' ) {
			
			// Don't waste time if remote auth is turned off.
			if ( ! isset( $this->remote_auth_settings['enabled'] ) || ! $this->remote_auth_settings['enabled'] ) {
				_e( 'Remote authentication is not configured yet.', 'zendesk' );
				die();
			}

			
			// These are created by Zendesk
			$timestamp = $_REQUEST['timestamp'];
			$return_to = $_REQUEST['return_to'];

			global $current_user;
			wp_get_current_user();
			
			// If the current user is logged in
			if ( 0 != $current_user->ID ) {
				
				// Pick the most appropriate name for the current user.
				if ( $current_user->user_firstname != '' && $current_user->user_lastname != '' )
					$name = $current_user->user_firstname . ' ' . $current_user->user_lastname; 
				else
					$name = $current_user->display_name; 

				// Gather more info from the user, incl. external ID
				$email = $current_user->user_email;
				$external_id = $current_user->ID;
				
				// The token is the remote "Shared Secret" under Settings - Security - SSO
				$token = $this->remote_auth_settings['token'];
				
				// Generate the hash as per http://www.zendesk.com/api/remote-authentication
				$hash = md5( $name . $email . $external_id . $token . $timestamp );
				
				// Create the SSO redirect URL and fire the redirect.
				$sso_url = trailingslashit( $this->zendesk_url ) . 'access/remote/?action=zendesk-remote-login&return_to=' . urlencode( $return_to ) . '&name=' . urlencode( $name ) . '&email=' . urlencode( $email ) . '&external_id=' . urlencode( $external_id ) . '&timestamp=' . urlencode( $timestamp ) . '&hash=' . urlencode( $hash );
				wp_redirect( $sso_url );
				
				// No further output.
				die();
			} else {
				
				// If the current user is not logged in we ask him to visit the login form
				// first, authenticate and specify the current URL again as the return
				// to address. Hopefully WordPress will understand this.
				wp_redirect( wp_login_url( wp_login_url() . '?action=zendesk-remote-login&timestamp=' . urlencode( $timestamp ) . '&return_to=' . urlencode( $return_to ) ) );
				die();
			}
		}
		
		// Is this a logout request? Errors from Zendesk are handled here too.
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'zendesk-remote-logout' ) {
			
			// Don't waste time if remote auth is turned off.
			if ( ! isset( $this->remote_auth_settings['enabled'] ) || ! $this->remote_auth_settings['enabled'] ) {
				_e( 'Remote authentication is not configured yet.', 'zendesk' );
				die();
			}

			
			// Error processing and info messages are done here.
			$kind = isset( $_REQUEST['kind'] ) ? $_REQUEST['kind'] : 'info';
			$message = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : 'nothing';
			
			// Depending on the message kind
			if ( $kind == 'info' ) {
				
				// When the kind is an info, it probably means that the logout
				// was successful, thus, logout of WordPress too.
				wp_redirect( htmlspecialchars_decode( wp_logout_url() ) );
				die();
				
			} elseif ( $kind == 'error' ) {
				// If there was an error...
			?>
				<p><?php _e( 'Remote authentication failed: ', 'zendesk' ); ?><?php echo $message; ?>.</p>
				<ul>
					<li><a href="<?php echo $this->zendesk_url; ?>"><?php _e( 'Try again', 'zendesk' ); ?></a></li>
					<li><a href="<?php echo wp_logout_url(); ?>"><?php printf( __( 'Log out of %s', 'zendesk' ), get_bloginfo( 'name' ) ); ?></a></li>
					<li><a href="<?php echo admin_url(); ?>"><?php printf( __( 'Return to %s dashboard', 'zendesk' ), get_bloginfo( 'name' ) ); ?></a></li>
				</ul>
			<?php
			}

			// No further output.
			die();
		}
	}
	
	/*
	 * Settings: Authentication Section
	 * 
	 * Outputs the description for the authentication settings registered
	 * during admin_init, displayed underneath the section title, which
	 * is defined during section registration.
	 * 
	 */	 
	 
	public function _settings_section_authentication() {
		_e( "We need your Zendesk subdomain, so we can use Zendesk's API to get your ticket information.", 'zendesk' );
	}
	
	/*
	 * Settings: Account Field
	 * 
	 * Field for $this->settings['account'] -- simply the account name,
	 * without any http or zendesk.com prefixes and postfixes. Validated
	 * together with all the other options.
	 * 
	 */
	public function _settings_field_account() {
	?>
		<?php if ( ! $this->settings['account'] ): ?>
			<strong>http://<input type="text" style="width: 120px;" class="regular-text" id="zendesk_account" name="zendesk-settings[account]" value="<?php echo $this->settings["account"]; ?>" />.zendesk.com</strong> <br />
			<span class="description">Even if you have host mapping, please use your subdomain here.<br />
			  We will automatically detect if you use SSL or not.</span>
		<?php else: ?>
			http://<input type="text" style="width: 120px; display: none;" class="regular-text" id="zendesk_account" name="zendesk-settings[account]" value="<?php echo $this->settings["account"]; ?>" /><strong id="zendesk_account_string"><?php echo $this->settings['account']; ?></strong>.zendesk.com <br />
			<span class="description">
			<a id="zendesk_account_change" href="#"><?php _e( 'Click here to change your subdomain', 'zendesk' ); ?></a>
			</span>
		<?php endif; ?>
	<?php
	}
	
	/*
	 * Settings: SSL Field
	 * 
	 * Boolean field for $this->settings['ssl'] -- switches on or off
	 * SSL access to the Zendesk servers.
	 *
	 */
	public function _settings_field_ssl() {
		$ssl = (bool) $this->settings['ssl'];
	?>
		<?php if ( $ssl ): ?>
		<span class="description"><?php _e( 'Your account is using SSL', 'zendesk' ); ?></span>
		<?php else: ?>
		<span class="description"><?php _e( 'Your account is <strong>not</strong> using SSL', 'zendesk' ); ?></span>
		<?php endif; ?>
	<?php
	}
	
	/*
	 * Settings: Dashboard Widget Section
	 * 
	 * Outputs the description for the Dashboard Widget section, which
	 * appears underneath the section title.
	 *
	 */
	public function _settings_section_dashboard_widget() {
		_e( "The Dashboard Widget can be changed depending on a User's capabilitites.", 'zendesk' );
	}
	
	/*
	 * Settings: Dashboard Widget Access
	 * 
	 * This function is used to output several different options fields,
	 * which is why there's an $args input array which generally contains
	 * one key called 'role' with a value listed in the array below. Works
	 * well for Administrator, Editor, Author, Contributor and Subscriber.
	 * 
	 * @uses $this->_available_dashboard_widget_options()
	 * 
	 */
	public function _settings_field_dashboard_access($args) {
		if ( ! isset( $args['role'] ) || ! in_array( $args['role'], array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ) ) ) return;
		$role = $args['role'];
	?>
		<select name="zendesk-settings[dashboard_<?php echo $role; ?>]" id="zendesk_dashboard_<?php echo $role; ?>">
		<?php foreach ( $this->_available_dashboard_widget_options() as $value => $caption ): ?>
			<option <?php selected( $value == $this->settings['dashboard_' . $role] ); ?> value="<?php echo $value; ?>"><?php echo $caption; ?></option>
		<?php endforeach; ?>
		</select>
	<?php
	}
	
	/*
	 * Settings: Contact Form Section
	 * 
	 * Outputs the contact form section description, appears underneath
	 * the section heading
	 * 
	 */
	public function _settings_section_contact_form() {
		_e( 'The contact form is a way for users to submit support requests. It can be added to the dashboard using the options above.', 'zendesk' );
	}
	
	/*
	 * Settings: Contact Form Title
	 * 
	 * The title of the contact form dashboard widget, accessible via
	 * $this->settings['contact_form_title']
	 * 
	 */
	public function _settings_field_contact_form_title() {
		$value = $this->_is_default( 'contact_form_title' ) ? '' : $this->settings['contact_form_title'];
	?>
		<input type="text" class="regular-text" name="zendesk-settings[contact_form_title]" value="<?php echo $value; ?>" placeholder="<?php echo $this->default_settings['contact_form_title']; ?>" />
	<?php
	}
	
	/*
	 * Settings: Contact Form Summary Label
	 * 
	 * The Summary label text in the contact form dashboard widget,
	 * accessible via $this->settings['contact_form_summary']
	 * 
	 */
	public function _settings_field_contact_form_summary() {
		$value = $this->_is_default( 'contact_form_summary' ) ? '' : $this->settings['contact_form_summary'];
	?>
		<input type="text" class="regular-text" name="zendesk-settings[contact_form_summary]" value="<?php echo $value; ?>" placeholder="<?php echo $this->default_settings['contact_form_summary']; ?>" />
	<?php
	}
	
	/*
	 * Settings: Contact From Details Label
	 * 
	 * The Details label text in the contact form dashboard widget,
	 * accessible via $this->settings['contact_form_details']
	 * 
	 */
	public function _settings_field_contact_form_details() {
		$value = $this->_is_default( 'contact_form_details' ) ? '' : $this->settings['contact_form_details'];
	?>
		<input type="text" class="regular-text" name="zendesk-settings[contact_form_details]" value="<?php echo $value; ?>" placeholder="<?php echo $this->default_settings['contact_form_details']; ?>" />
	<?php
	}
	
	/*
	 * Settings: Contact Form Submit Label
	 * 
	 * The caption of the submit button in the contact form dashboard
	 * widget, accessible via $this->settings['contact_form_submit']
	 * Escape when printing.
	 * 
	 */
	public function _settings_field_contact_form_submit() {
		$value = $this->_is_default( 'contact_form_submit' ) ? '' : $this->settings['contact_form_submit'];
	?>
		<input type="text" class="regular-text" name="zendesk-settings[contact_form_submit]" value="<?php echo $value; ?>" placeholder="<?php echo $this->default_settings['contact_form_submit']; ?>" />
	<?php
	}
	
	/*
	 * Settings Field: Contact Form Anonymous Status
	 * 
	 * This says whether anonymous tickets submissions through the
	 * contact form widget are allowed or not. The field below appears
	 * only when this is active (via javascript of course)
	 * 
	 */
	public function _settings_field_contact_form_anonymous() {
	?>
		<input id="zendesk_contact_form_anonymous" type="checkbox" name="zendesk-settings[contact_form_anonymous]" value="1" <?php checked( (bool) $this->settings['contact_form_anonymous'] ); ?> />
		<label for="zendesk_contact_form_anonymous"><?php _e( 'Check this to allow users without Zendesk accounts to submit requests.', 'zendesk' ); ?></label><br />
		<span class="description"><?php _e( 'If disabled, users will need to login to Zendesk to submit requests.', 'zendesk' ); ?></span>
	<?php
	}
	
	/*
	 * Settings Field: Contact Form Anonymous User
	 * 
	 * This is the user via whom the requests are fired when the
	 * anonymous contact form is enabled. A select box is given with
	 * a list of agents and the current user.
	 * 
	 */
	public function _settings_field_contact_form_anonymous_user() {
		
		// Fetch the agents
		$users = $this->_get_agents();
		
		// Let's see if the current user *is* an agent.
		$contains_current_user = false;
		foreach ( $users as $user ) {
			if ( $user->ID == $this->user->ID ) {
				$contains_current_user = true;
				break;
			}
		}
		
		// If the current user's not an agent append them to the beginning of the list.
		if ( ! $contains_current_user )
			array_unshift( $users, $this->user );
		
	?>
		<select id="zendesk_contact_form_anonymous_user" name="zendesk-settings[contact_form_anonymous_user]">
			<?php foreach ( $users as $user ): ?>
			<option <?php selected( $user->ID == $this->settings['contact_form_anonymous_user'] ); ?> value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?> (<?php echo $user->user_email; ?>)</option>
			<?php endforeach; ?>
		</select><br />
		<span class="description">
			<?php _e( 'Contact form submissions will be done "via" this agent, through the Zendesk API. <br /> This agent must be authenticated into Zendesk via the Wordpress for Zendesk widget.<br /> Agents not authenticated via the dashboard widget are not shown here.', 'zendesk' ); ?>
			<br /><a target="_blank" href="https://support.zendesk.com/entries/20116518-setting-up-anonymous-ticket-submissions-with-zendesk-for-wordpress"><?php _e( 'Learn more at Zendesk.com', 'zendesk' ); ?></a>
		</span>
	<?php
	}

	/*
	 * Settings: Dropbox Section
	 * 
	 */
	public function _settings_section_dropbox() {
		_e( 'The Zendesk Dropbox places a convenient tab on your pages that allow your visitors to contact you via a pop-up form.', 'zendesk' );
	}
	
	/*
	 * Settings: Dropbox Display
	 * 
	 * Boolean value which turns on or off the Zendesk Dropbox. This
	 * value is checked when registering Dropbox scritps, styles and
	 * code. Accessed from $this->settings['dropbox_display']
	 * 
	 */
	public function _settings_field_dropbox_display() {
	?>
		<select name="zendesk-settings[dropbox_display]" id="zendesk_dropbox_display">
			<option value="none" <?php selected( $this->settings['dropbox_display'] == 'none' ); ?> ><?php _e( 'Do not display the Zendesk dropbox anywhere', 'zendesk' ); ?></option>
			<option value="auto" <?php selected( $this->settings['dropbox_display'] == 'auto' ); ?> ><?php _e( 'Display the Zendesk dropbox on all posts and pages', 'zendesk' ); ?></option>
			<option value="manual" <?php selected( $this->settings['dropbox_display'] == 'manual' ); ?> ><?php _e( 'I will decide where the Zendesk dropbox displays using a template tag' ); ?></option>
		</select>
		
	<?php
	}
	
	/*
	 * Settings: Dropbox Code
	 * 
	 * A text area to stick in the dropbox code which is printed
	 * during the wp_footer action in the theme if the dropbox display
	 * setting is set to true. Access via $this->settings['dropbox_code']
	 * 
	 */
	public function _settings_field_dropbox_code() {
	?>
		<span class="description float-left"><strong><?php printf( __( 'Obtain your Dropbox code from the %s in your Zendesk.', 'zendesk' ), sprintf( '<a target="_blank" href="' . trailingslashit( $this->zendesk_url ) . 'account/dropboxes/new">%s</a>', __( 'Dropbox Configuration page', 'zendesk' ) ) ); ?></strong></span><br />
		<textarea id="zendesk_dropbox_code" cols="60" rows="5" name="zendesk-settings[dropbox_code]"><?php echo esc_textarea( $this->settings['dropbox_code'] ); ?></textarea><br />
	<?php
	}
	
	
	/*
	 * Settings Section: Remote Auth General
	 * 
	 */
	public function _settings_remote_auth_section_general() {
		_e( 'The general remote authentication settings', 'zendesk' );
	}
	
	/*
	 * Settings Remote Auth: Enabled
	 * 
	 * This simply says whether remote authentication is enabled or not,
	 * used to be a checkbox, but that is now handled in the remote
	 * auth validation section.
	 * 
	 */
	public function _settings_field_remote_auth_enabled() {
	
		$remote_auth = (bool) $this->remote_auth_settings['enabled'];
	?>
			<span class="description">
				<?php if ( $remote_auth ): ?>
					<strong><?php _e( 'Remote authentication is enabled', 'zendesk' ); ?></strong>
				<?php else: ?>
					<strong><?php _e( 'Remote authentication is <strong>disabled</strong>', 'zendesk' ); ?></strong>
			<?php endif; ?>
			
			<br /><?php _e( 'To activate remote authentication, ensure a shared token <br /> is entered below and click &quot;Save Changes&quot;', 'zendesk' ); ?>
			
			</span>
	<?php
	}
	
	/*
	 * Settings Remote Auth: Shared Token
	 * 
	 * Shared token is the shared secret located under the single sign-on
	 * settings on the Zendesk Account Security page. We ask for that
	 * token right here.
	 * 
	 */
	public function _settings_field_remote_auth_token() {
	?>
		<input type="text" class="regular-text" name="zendesk-settings-remote-auth[token]" value="<?php echo $this->remote_auth_settings['token']; ?>" /><br />
		<span class="description">
			<?php printf( __( 'Your shared token could be obtained on the %s in the <br /> Single Sign-On section.', 'zendesk' ), sprintf( '<a target="_blank" href="' . trailingslashit( $this->zendesk_url ) . 'settings/security">%s</a>', __( 'Account Security page', 'zendesk' ) ) ); ?>
			<br /><br />
			<?php printf( __( '<strong>Remember</strong> that you can always go to: <br /> %s to use the regular login <br /> in case you get unlucky and somehow lock yourself out of Zendesk.', 'zendesk' ), '<a target="_blank" href="' . trailingslashit( $this->zendesk_url ) . 'access/normal' . '">' . trailingslashit( $this->zendesk_url ) . 'access/normal' . '</a>' ); ?>
		</span>
	<?php
	}
	
	/*
	 * Settings Section: Remote Auth for Zendesk
	 * 
	 */
	public function _settings_remote_auth_section_zendesk() {
		_e( 'The settings that need to be configured in your Zendesk account.', 'zendesk' );
	}
	
	/*
	 * Settings Field: Remote Auth Login URL
	 * 
	 * Displays the login URL for the Zendesk remote auth settings.
	 * 
	 */
	public function _settings_field_remote_auth_login_url() {
		echo '<code>' . wp_login_url() . '?action=zendesk-remote-login' . '</code>';
	}
	
	/*
	 * Settings Field: Remote Auth Logout URL
	 * 
	 * Same as above but displays the logout URL.
	 * 
	 */
	public function _settings_field_remote_auth_logout_url() {
		echo '<code>' . wp_login_url() . '?action=zendesk-remote-logout' . '</code>';
	}
	
	

	/*
	 * Add Notice
	 * 
	 * An internal function to add a notice to a specific context, where
	 * contexts are "places" that display notice messages, such as
	 * 'login_form' or 'tickets_widget'. The $text is the text to display
	 * and the $type is either 'note', 'confirm', or 'alert' which
	 * differs in colors when output.
	 * 
	 */
	private function _add_notice( $context, $text, $type = 'note' ) {
		if ( isset( $this->notices[$context . '_' . $type] ) )
			$this->notices[$context . '_' . $type][] = $text;
		else
			$this->notices[$context . '_' . $type] = array( $text );
	}
	
	/*
	 * Do Notices
	 * 
	 * Process all the added notices for a specific context and output
	 * them on screen using the _notice function. Loops through notes,
	 * confirms and alerts for the given context.
	 * 
	 */
	private function _do_notices( $context ) {
		echo '<div class="zendesk-notices-group">';
		foreach ( array( 'note', 'confirm', 'alert' ) as $type ) {
			if ( isset( $this->notices[$context . '_' . $type] ) ) {
				$notices = $this->notices[$context . '_' . $type];
		
				foreach ( $notices as $notice )
					$this->_notice( $notice, $type );
			}
		}
		echo '</div>';
	}
	
	/*
	 * Notice
	 * 
	 * Prints the notice to screen given a certain $type, which can be
	 * 'note', 'alert' and 'confirm' according to the stylesheets.
	 * 
	 */
	private function _notice( $text, $type = 'note' ) {
	?>
		<div class="zendesk-admin-notice zendesk-<?php echo $type; ?>">
			<p><?php echo $text; ?></p>
		</div>
	<?php
	}
	
	/*
	 * Admin Notices
	 * 
	 * These are different than the Zendesk notices, this is the core
	 * WordPress functionality to display notices at the top of the
	 * admin pages. Used for notifications.
	 * 
	 */
	public function _wp_admin_notices() {
		
		if ( isset( $this->settings['contact_form_anonymous'] ) && $this->settings['contact_form_anonymous'] ) {
			
			$agent = $this->settings['contact_form_anonymous_user'];
			if ( $this->settings['account'] && ! $this->_is_agent( $agent ) && current_user_can( 'manage_options' ) ) {
			?>
				<div id="message" class="error"><p>
					<?php printf( __( '<strong>Whoops!</strong> The user specified as the anonymous requests author is not logged in to Zendesk! You can %s or kindly ask them to log in.', 'zendesk' ), sprintf( '<a href="' . admin_url( 'admin.php?page=zendesk-support' ) . '">%s</a>', __( 'change the user', 'zendesk' ) ) ); ?>
				</p></div>
			<?php
			}
		}
	}
	
	/*
	 * Helper: Ticket Status
	 * 
	 * Internal function, repeats Zendesk's ticket statuses, ready
	 * for translation. Used by the tickets widget.
	 * 
	 */
	private function _ticket_status( $status_id ) {
		$statuses = array(
			0 => __( 'New', 'zendesk' ),
			1 => __( 'Open', 'zendesk' ),
			2 => __( 'Pending', 'zendesk' ),
			3 => __( 'Solved', 'zendesk' ),
			4 => __( 'Closed', 'zendesk' ),
		);
		
		if ( array_key_exists( $status_id, $statuses ) )
			return $statuses[$status_id];
		else 
			return $status_id;
	}
	
	/*
	 * Helper: Ticket Status Class
	 * 
	 * Same as above but non-localized lowercase name of the status,
	 * used to output the class names so that they're colorized via
	 * the stylesheets.
	 * 
	 */
	private function _ticket_status_class_name( $status_id ) {
		$statuses = array(
			0 => 'new',
			1 => 'open',
			2 => 'pending',
			3 => 'solved',
			4 => 'closed',
		);
		
		if ( array_key_exists( $status_id, $statuses ) )
			return $statuses[$status_id];
		else 
			return $status_id;
	}
	
	/*
	 * Helper: Is Agent
	 * 
	 * A conditional function that returns true if the current or
	 * specified Zendesk user is an agent, otherwise returns false, 
	 * meaning that it's probably an end-user.
	 * 
	 */
	private function _is_agent( $user_ID = false ) {
		
		// Current user or a specific user ID.
		$zendesk_user = $user_ID ? get_user_meta( $user_ID, 'zendesk_user_options', true ) : $this->zendesk_user;

		if ( isset( $zendesk_user['roles'] ) && $zendesk_user['roles'] > 0 )
			return true;
		else
			return false;
	}
	
	/*
	 * Helper: Get Agents
	 * 
	 * Scans the WordPress database for all users, loops through each
	 * and every one of them, returns an array of those who are authenticated
	 * with Zendesk and who's roles are agents.
	 * 
	 */
	private function _get_agents() {
		global $wpdb;
		$users = $wpdb->get_col( $wpdb->prepare( "SELECT $wpdb->users.ID FROM $wpdb->users" ) );
		$data = array();
		
		foreach ( $users as $user_ID )
			if ( $this->_is_agent( $user_ID ) )
				$data[] = get_userdata( $user_ID );
				
		return $data;
	}
	
	/*
	 * Helper: Get Agent
	 * 
	 * Returns the Zendesk user options for the requested user ID.
	 * 
	 */
	private function _get_agent( $user_ID ) {
		if ( ! $this->_is_agent( $user_ID ) ) return false;
		return get_user_meta( $user_ID, 'zendesk_user_options', true );
	}
	
	/*
	 * Helper: Zendesk Ticket URL
	 * 
	 * Returns the URL to the Zendesk ticket given the ticket ID.
	 * 
	 */
	private function _ticket_url( $ticket_id ) {
		return trailingslashit( $this->zendesk_url ) . 'tickets/' . $ticket_id;
	}
	
	/*
	 * Helper: Zendesk User URL
	 * 
	 * Returns the URL to the Zendesk user profile given the user ID.
	 * 
	 */
	private function _user_url( $user_id ) {
		return trailingslashit( $this->zendesk_url ) . 'users/' . $user_id;
	}
	
	/*
	 * Get Current User Role (helper)
	 * 
	 * Used internally, since the tickets widget and contact form widget
	 * are distributed among roles, not capabilities. This private method
	 * returns the current role as a string.
	 * 
	 * @uses current_user_can
	 *
	 */
	private function _get_current_user_role() {
		foreach ( array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ) as $role )
			if ( current_user_can( $role ) )
				return $role;
	}
	
	/*
	 * Helper: Custom Excerpt
	 * 
	 * Create an excerpt of any string given the string and the number
	 * of words to truncate to, default is 50.
	 *
	 */
	private function _excerpt( $string, $words = 50 ) {
		$blah = explode( ' ', $string );
		$return = '';
		if ( count( $blah ) > $words ) {
			for ( $i = 0; $i < $words; $i++ )
				$return .= $blah[$i] . ' ';
				
			$return .= '...';
			return $return;
		} else {
			return $string;
		}
	}
	
	/*
	 * Helper: File Size
	 * 
	 * Used to display the sizes of the attachments in Zendesk comments.
	 * 
	 */
	private function _file_size( $bytes ) {
		$filesizename = array(" bytes", " kb", " mb", " gb", " tb", " pb", " eb", " zb", " yb");
		return $bytes ? round( $bytes / pow( 1024, ( $i = floor( log( $bytes, 1024 ) ) ) ), 2 ) . $filesizename[$i] : '0 bytes';
	}
	
	/*
	 * Helper: Is Default
	 * 
	 * Checks whether the given key exists in the settings and whether
	 * it's equal to the default settings. Used in contact form labels
	 * to provide placeholders.
	 * 
	 */
	private function _is_default( $key ) {
		return $this->settings[$key] === $this->default_settings[$key];
	}
	
	/*
	 * Dropbox template tag
	 * 
	 * Definition is outside of this class, logic is inside.
	 * 
	 */
	public function the_zendesk_dropbox() {
		if ( isset( $this->settings['dropbox_display'] ) && $this->settings['dropbox_display'] == 'manual' )
			return $this->dropbox_code();
	}
};

// Register the Zendesk_Support class initialization during WordPress' init action. Globally available through $zendesk_support global.
add_action( 'init', create_function( '', 'global $zendesk_support; $zendesk_support = new Zendesk_Support();' ) );


/* 
 * Dropbox template tag
 *
 * This is the template tage used by those users who only want the dropbox
 * displayed on certain pages. 
 * 
 * @global $zendesk_support
 * 
 */
function the_zendesk_dropbox() {
	global $zendesk_support;
	
	// Simply call the method inside the object. Make sure object is
	// initialized before calling it's method.
	if ( $zendesk_support )
		$zendesk_support->the_zendesk_dropbox();
}
