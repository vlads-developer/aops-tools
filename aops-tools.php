<?php
/**
 *
 * Plugin Name:       Audience Ops Tools
 * Version:           0.1
 * Author:            -
 * Author URI:        -
 * Text Domain:       aops-tools
 */

add_action( 'admin_menu', 'aops_tools_add_options_page' );
add_action( 'admin_init', 'aops_tools_register_options' );
add_action( 'add_meta_boxes', 'aops_tools_add_meta_boxes' );
add_action( 'admin_enqueue_scripts', 'aops_tools_admin_enqueue_scripts' );

register_activation_hook( __FILE__, 'aops_tools_activate' );

function aops_tools_add_options_page() {
    
	add_options_page( __( 'Audience Ops Tools Settings', 'aops-tools' ), __( 'Audience Ops Tools', 'aops-tools' ), 'manage_options', 'aops-tools', 'aops_tools_options_page' );

}

function aops_tools_register_options() {

	register_setting( 'aops_tools_options', 'aops_tools_options', 'aops_tools_sanitize_options' );

}

function aops_tools_sanitize_display_post_types( $display_post_types ) {

	if ( ! is_array( $display_post_types ) ) {

		return array();

	}

	$post_types = get_post_types();

	foreach ( array_keys( $display_post_types ) as $key ) {

		if ( ! is_string( $display_post_types[$key] ) || ! isset( $post_types[$display_post_types[$key]] ) ) {

			unset( $display_post_types[$key] );

		}

	}

	return $display_post_types;

}

function aops_tools_sanitize_display_users( $display_users ) {

	if ( ! is_array( $display_users ) ) {

		return array();

	}

	$users = aops_tools_get_users();

	foreach ( array_keys( $display_users ) as $key ) {

		if ( ! is_string( $display_users[$key] ) || ! isset( $users[$display_users[$key]] ) ) {

			unset( $display_users[$key] );

		}

	}

	return $display_users;

}

function aops_tools_sanitize_options( $input ) {

	if ( ! isset( $input['display_post_types'] ) ) {

		$input['display_post_types'] = array();

	} else {

		$input['display_post_types'] = aops_tools_sanitize_display_post_types( $input['display_post_types'] );

	}

	if ( ! isset( $input['display_users'] ) ) {

		$input['display_users'] = array();

	} else {

		$input['display_users'] = aops_tools_sanitize_display_users( $input['display_users'] );

	}

	return $input;

}

function aops_tools_get_users() {

	$rv = array();

	$users = get_users();

	foreach ( $users as $user ) {

		// Exclude "subscribers"
		if ( ! in_array( 'subscriber', $user->roles ) ) {

			$rv[$user->ID] = $user->data->user_login;

		}

	}

	return $rv;

}

function aops_tools_options_page() {

	$options = get_option( 'aops_tools_options' );

	$tabs = array(
		'general'		=> 'General',
		'special_instructions'	=> 'Special Instructions',
		'content_checklist'	=> 'Content Checklist'
	);

?>
	<div class="wrap aops-tools-settings-wrap">
		<h2><?php _e( 'Audience Ops Tools Settings', 'aops-tools' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'aops_tools_options' ); ?>
			<input id="aops_tools_last_opened_settings_tab" type="hidden" name="aops_tools_options[last_opened_settings_tab]" value="<?php echo esc_attr( $options['last_opened_settings_tab'] ); ?>">
			<div id="aops_tools_tab_container" class="tab-container" style="margin-top: 25px;">
				<ul class="etabs">
					<?php foreach ( $tabs as $tab_id => $tab_name ) : ?>
						<li class="tab" id="tab_<?php echo $tab_id; ?>"><a href="#aops_tools_tab_<?php echo $tab_id; ?>"><?php _e( $tab_name, 'aops-tools' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="panel-container" style="border: solid #ccc 1px;">
					<div id="aops_tools_tab_general">
						<table class="form-table-wide">
							<tr style="display: table-row;">
								<td class="col-name" style=""><label><?php _e( 'Display Tools on...', 'aops-tools' ); ?></label></td>
								<td class="col-input">
									<?php $post_types = get_post_types(); ?>
									<?php foreach ( $post_types as $post_type ) : ?>
										<?php $checked = ( in_array( $post_type, $options['display_post_types'] ) ) ? 'checked' : ''; ?>
										<input id="aops_tools_display_post_types_<?php echo esc_attr( $post_type ); ?>" type="checkbox" name="aops_tools_options[display_post_types][]" <?php echo $checked; ?> value="<?php echo esc_attr( $post_type ); ?>">
										<label for="aops_tools_display_post_types_<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $post_type ); ?></label><br>
									<?php endforeach; ?>
								</td>
								<td class="col-info">
									<a href="javascript: void(0);" class="aops-tooltip">
										<span class="info-icon">?</span>
										<span class="tooltip-body"><?php _e( 'Select the post type(s) where the tools metabox should display on the editor', 'aops-tools' ); ?></span>
									</a>
								</td>
							</tr>
							<tr style="display: table-row;">
								<td class="col-name" style=""><label><?php _e( 'Display For Users', 'aops-tools' ); ?></label></td>
								<td class="col-input">
									<?php $users = aops_tools_get_users(); ?>
									<?php foreach ( $users as $user_id => $user_login ) : ?>
										<?php $checked = ( in_array( $user_id, $options['display_users'] ) ) ? 'checked' : ''; ?>
										<input id="aops_tools_display_users_<?php echo esc_attr( $user_id ); ?>" type="checkbox" name="aops_tools_options[display_users][]" <?php echo $checked; ?> value="<?php echo esc_attr( $user_id ); ?>">
										<label for="aops_tools_display_users_<?php echo esc_attr( $user_id ); ?>"><?php echo esc_html( $user_login ); ?></label><br>
									<?php endforeach; ?>
								</td>
								<td class="col-info">
									<a href="javascript: void(0);" class="aops-tooltip">
										<span class="info-icon">?</span>
										<span class="tooltip-body"><?php _e( 'Select user(s) ...', 'aops-tools' ); ?></span>
									</a>
								</td>
							</tr>
						</table>
					</div>
					<div id="aops_tools_tab_special_instructions">
						<table class="form-table-wide">
							<tr><td><?php _e( 'Options coming soon', 'aops-tools' ); ?></td></tr>
						</table>
					</div>
					<div id="aops_tools_tab_content_checklist">
						<table class="form-table-wide">
							<tr><td><?php _e( 'Options coming soon', 'aops-tools' ); ?></td></tr>
						</table>
					</div>
				</div>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

function aops_tools_add_meta_boxes() {

	$options = get_option( 'aops_tools_options' );

	$current_user = wp_get_current_user();

	$screen = get_current_screen();

        if ( in_array( $screen->post_type, $options['display_post_types'] ) && in_array( $current_user->ID, $options['display_users'] ) ) {

		add_meta_box( 'aops-tools-metabox', __( 'Audience Ops Tools', 'aops-tools' ), 'aops_tools_metabox', '', 'side', 'default' );

	}

}

function aops_tools_metabox() {
?>
	<div id="aops-tools-metabox-collapsible">
		<h3><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e( 'Special Instructions', 'aops-tools' ); ?></h3>
		<div>
			<table class="form-table-wide">
				<tr><td>Special instructions content will go here</td></tr>
			</table>
		</div>
		<h3><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e( 'Checklist', 'aops-tools' ); ?></h3>
		<div>
			<table class="form-table-wide">
				<tr><td>Checklist will go here</td></tr>
			</table>
		</div>
		<h3><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e( 'Links', 'aops-tools' ); ?></h3>
		<div>
			<table class="form-table-wide">
				<tr><td>Links will go here</td></tr>
			</table>
		</div>
		<h3><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e( 'Progress Report', 'aops-tools' ); ?></h3>
		<div>
			<table class="form-table-wide">
				<tr><td>Progress report will go here</td></tr>
			</table>
		</div>
	</div>
<?php
}

function aops_tools_admin_enqueue_scripts() {

	wp_enqueue_script( 'jquery-easytabs-js', plugins_url( '/js/jquery.easytabs.js' , __FILE__ ), array( 'jquery' ), '3.2.0' );
	wp_enqueue_script( 'jquery-collapse-js', plugins_url( '/js/jquery.collapse.min.js' , __FILE__ ), array('jquery' ), '1.0.0' );
	wp_enqueue_script( 'aops-tools-admin-js', plugins_url( '/js/aops-tools-admin.js' , __FILE__ ), array( 'jquery' ) );
	wp_enqueue_style( 'aops-tools-admin-css', plugins_url( '/css/aops-tools-admin.css', __FILE__ ) );

}

function aops_tools_activate() {

	$options = get_option( 'aops_tools_options' );

	// Set default options if they don't exist
	if ( $options === false ) {

		// "Posts" should be checked by default
		$options['display_post_types'] = array( 'post' => 'post' );

		$options['display_users'] = array();

		$options['last_opened_settings_tab'] = '#tab_general';

	}

	update_option( 'aops_tools_options', $options );

}
