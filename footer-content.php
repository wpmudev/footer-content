<?php
/*
Plugin Name: Footer Content
Plugin URI: 
Description:
Author: Andrew Billits (Incsub)
Version: 1.0.1
Author URI:
WDP ID: 76
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
add_action('admin_menu', 'footer_content_plug_pages');
add_action('wp_footer', 'footer_content_output');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_output() {
	$footer_content = get_option('footer_content');
	if ( !empty( $footer_content ) ){
		echo $footer_content;
	}
}

function footer_content_plug_pages() {
	global $wpdb, $wp_roles, $current_user;
	if ( is_site_admin() ) {
		add_submenu_page('themes.php', 'Footer Content', 'Footer Content', 10, 'footer-content', 'footer_content_page_output');
	}
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_page_output() {
	global $wpdb, $wp_roles, $current_user;
	
	if(!current_user_can('manage_options')) {
		echo "<p>" . __('Nice Try...') . "</p>";  //If accessed properly, this message doesn't appear.
		return;
	}
	if (isset($_GET['updated'])) {
		?><div id="message" class="updated fade"><p><?php _e('' . urldecode($_GET['updatedmsg']) . '') ?></p></div><?php
	}
	echo '<div class="wrap">';
	switch( $_GET[ 'action' ] ) {
		//---------------------------------------------------//
		default:
			?>
			<h2><?php _e('Footer Content') ?></h2>
            <p><?php _e('The footer content is displayed at the bottom of every page on your blog'); ?></p>
            <form method="post" action="themes.php?page=footer-content&action=process">
            <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php _e('Footer Content') ?></th>
            <td>
            <textarea name="footer_content" type="text" rows="5" wrap="soft" id="footer_content" style="width: 95%"/><?php echo get_option('footer_content') ?></textarea>
            <br /><?php _e('HTML allowed') ?></td>
            </tr>
            </table>
            
            <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			<input type="submit" name="Reset" value="<?php _e('Reset') ?>" />
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
				window.location='themes.php?page=footer-content&updated=true&updatedmsg=" . urlencode(__('Settings cleared.')) . "';
				</script>
				";			
			} else {
				update_option( "footer_content", stripslashes($_POST[ 'footer_content' ]) );
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				window.location='themes.php?page=footer-content&updated=true&updatedmsg=" . urlencode(__('Settings saved.')) . "';
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

?>
