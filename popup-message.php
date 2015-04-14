<?php
/*
Plugin Name: Popup Message
Plugin URI: http://appzcoder.com
Description: Display popup flash message after login.
Version: 0.1
Author: Sohel Amin
Author URI: http://sohelamin.com
*/

// Calling init action hook
add_action('init', 'ac_initialize');

/**
 * Initialize all hooks, functions
 *
 * @return Void
 */	
function ac_initialize() {
	// Adding settings link on plugin page
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'ac_plugin_settings_link' );

	add_action('login_form', 'ac_redirect_url_append');

	// create custom plugin settings menu
	add_action( 'admin_menu', 'ac_popup_message_create_menu' );	

	if ( is_user_logged_in() && isset($_GET['ac_pfmsg']) && $_GET['ac_pfmsg']==1 ) {
		add_action( 'wp_enqueue_scripts', 'ac_enqueue_scripts' );
		add_action('wp_footer', 'ac_add_this_script_footer');
		add_action('wp_footer', 'ac_show_popup');		
	}
}

/**
 * Display popup message
 *
 * @return Void
 */	
function ac_show_popup() {
	$messages = get_option( 'ac_custom_pupup_message' ) ? get_option( 'ac_custom_pupup_message' ) : "You've successfully logged in!";
    echo "<div class=\"ac-popup-message\">    
		<a class=\"dashicons dashicons-dismiss close\" href=\"javascript:void(0)\" onclick=\"this.parentNode.style.display='none'\"></a>
		<p>".$messages."</p>    
    </div>";
}

/**
 * Enqueue Scripts
 *
 * @return Void
 */	
function ac_enqueue_scripts() {
	wp_enqueue_style( 'ac-popup-flash-message',  plugins_url('assets/style.css', __FILE__) );
}

/**
 * Footer script
 *
 * @return Void
 */	
function ac_add_this_script_footer() {
	$display_time = get_option( 'ac_pupup_message_display_time' ) ? 1000*get_option( 'ac_pupup_message_display_time' ) : 5000;
	echo '<script>
	jQuery(document).ready(function($){		
		setTimeout(function(){
    		$( "div.ac-popup-message" ).fadeOut( "slow" );
		},'.$display_time.')
	});
	</script>';
}

/**
 * Add settings link on plugin page
 *
 * @return Void
 */	
function ac_redirect_url_append() {
	global $redirect_to;
	if (!isset($_GET['redirect_to'])) {
		$redirect_to = $redirect_to."?ac_pfmsg=1";
	}
}

/**
 * Create settings menu
 *
 * @return Void
 */	
function ac_popup_message_create_menu() {
	// create new top-level menu
	add_options_page( 'Popup Message Plugin Settings', 'Popup Message Settings', 'administrator', __FILE__, 'ac_popup_message_settings_page' );
	// call register settings function
	add_action( 'admin_init', 'register_ac_popup_message_settings' );
}

/**
 * Register settings field
 *
 * @return Void
 */	
function register_ac_popup_message_settings() {
	// register our settings
	register_setting( 'ac-popup-massage-settings-group', 'ac_custom_pupup_message' );
	register_setting( 'ac-popup-massage-settings-group', 'ac_pupup_message_display_time' );
}

/**
 * Display settings page
 *
 * @return Void
 */	
function ac_popup_message_settings_page() {
?>
<div class="wrap">
	<h2>Popup Message</h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'ac-popup-massage-settings-group' ); ?>
		<?php do_settings_sections( 'ac-popup-message-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Custom Popup Message:</th>
				<td><textarea name="ac_custom_pupup_message"><?php echo get_option( 'ac_custom_pupup_message' ); ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row">Display Time:</th>
				<td><input type="text" name="ac_pupup_message_display_time" value="<?php echo get_option( 'ac_pupup_message_display_time' ); ?>" /> in sec</td>
			</tr>			
		</table>    
		<?php submit_button(); ?>
	</form>
</div>
<?php 
}

/**
 * Add settings link on plugin page
 *
 * @param Array $links
 *
 * @return Array
 */		
function ac_plugin_settings_link( $links ) { 
	$settings_link = '<a href="options-general.php?page=popup-message/popup-message.php">Settings</a>'; 
	array_unshift( $links, $settings_link ); 
	return $links; 
}
