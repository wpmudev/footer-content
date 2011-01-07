<?php
/*
Plugin Name: Footer Content
Plugin URI: http://premium.wpmudev.org/project/footer-content
Description: This plugin allows blog administrators to add their own content to the footer of every page on their blog
Author: Andrew Billits (Incsub)
Version: 1.0.1
Author URI: http://premium.wpmudev.org/
WPID: 76
*/

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('init', 'footer_content_init');
add_action('admin_menu', 'footer_content_plug_pages');
add_action('wp_footer', 'footer_content_output');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_init() {
	if ( !is_multisite() )
		exit( 'The Footer Content plugin is only compatible with WordPress Multisite.' );
		
	load_plugin_textdomain('footer_content', false, dirname(plugin_basename(__FILE__)).'/languages');
}

function footer_content_output() {
	$footer_content = get_option('footer_content');
	if ( !empty( $footer_content ) ){
		echo $footer_content;
	}
}

function footer_content_plug_pages() {
	global $wpdb, $wp_roles, $current_user;
	if ( is_site_admin() ) {
		add_submenu_page('themes.php', __('Footer Content', 'footer_content'), __('Footer Content', 'footer_content'), 10, 'footer-content', 'footer_content_page_output');
	}
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_page_output() {
	global $wpdb, $wp_roles, $current_user;
	
	if(!current_user_can('manage_options')) {
		echo "<p>" . __('Nice Try...', 'footer_content') . "</p>";  //If accessed properly, this message doesn't appear.
		return;
	}
	if (isset($_GET['updated'])) {
		?><div id="message" class="updated fade"><p><?php _e(urldecode($_GET['updatedmsg']), 'footer_content') ?></p></div><?php
	}
	echo '<div class="wrap">';
	switch( $_GET[ 'action' ] ) {
		//---------------------------------------------------//
		default:
			?>
			<h2><?php _e('Footer Content', 'footer_content') ?></h2>
            <p><?php _e('The footer content is displayed at the bottom of every page on your blog', 'footer_content'); ?></p>
            <form method="post" action="themes.php?page=footer-content&action=process">
            <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php _e('Footer Content', 'footer_content') ?></th>
            <td>
            <textarea name="footer_content" type="text" rows="5" wrap="soft" id="footer_content" style="width: 95%"/><?php echo get_option('footer_content') ?></textarea>
            <br /><?php _e('HTML allowed', 'footer_content') ?></td>
            </tr>
            </table>
            
            <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes', 'footer_content') ?>" />
			<input type="submit" name="Reset" value="<?php _e('Reset', 'footer_content') ?>" />
            </p>
            </form>
			<?php
		break;
		//---------------------------------------------------//
		case "process":
			if ( isset( $_POST[ 'Reset' ] ) ) {
				update_option( "footer_content", "" );
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				window.location='themes.php?page=footer-content&updated=true&updatedmsg=" . urlencode(__('Settings cleared.', 'footer_content')) . "';
				</script>
				";			
			} else {
				update_option( "footer_content", stripslashes($_POST[ 'footer_content' ]) );
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				window.location='themes.php?page=footer-content&updated=true&updatedmsg=" . urlencode(__('Settings saved.', 'footer_content')) . "';
				</script>
				";
			}
		break;
		//---------------------------------------------------//
		case "temp":
		break;
		//---------------------------------------------------//
	}
	echo '</div>';
}

if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}
