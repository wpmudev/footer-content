<?php
/*
Plugin Name: Footer Content
Plugin URI: http://premium.wpmudev.org/project/footer-content
Description: This plugin allows blog administrators to add their own content to the footer of every page on their blog
Author: S H Mohanjith (Incsub), Andrew Billits (Incsub)
Version: 1.0.2.4
Author URI: http://premium.wpmudev.org/
WDP ID: 76
Text Domain: footer_content
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
add_action('customize_register', 'footer_content_customize_register');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_init() {
	load_plugin_textdomain('footer_content', false, dirname(plugin_basename(__FILE__)).'/languages');
}

function footer_content_output() {
	$footer_content = get_theme_mod('footer_content');
        $footer_content_backend = 'theme_mod';
        if ( empty($footer_content) ) {
                $footer_content = get_option('footer_content');
                $footer_content_backend = 'option';
        }

	if ( !empty( $footer_content ) ){
		echo $footer_content;
	}
}

function footer_content_plug_pages() {
	global $wpdb, $wp_roles, $current_user;
	add_submenu_page('themes.php', __('Footer Content', 'footer_content'), __('Footer Content', 'footer_content'), 'edit_theme_options', 'footer-content', 'footer_content_page_output');
}

function footer_content_customize_register($wp_customize) {

	$wp_customize->add_setting( 'footer_content' , array(
    		'transport'   => 'refresh',
	) );

	$wp_customize->add_section( 'footer_content_section' , array(
    		'title'      => __( 'Footer Content', 'footer_content' ),
    		'priority'   => 100,
	) );
	
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_content', array(
		'label'		=> __( 'Footer Content', 'mytheme' ),
		'section'	=> 'footer_content_section',
		'settings'	=> 'footer_content',
		'type'		=> 'text',
	) ) );
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function footer_content_page_output() {
	global $wpdb, $wp_roles, $current_user;

	if(!current_user_can('edit_theme_options')) {
		echo "<p>" . __('Nice Try...', 'footer_content') . "</p>";  //If accessed properly, this message doesn't appear.
		return;
	}
	if (isset($_GET['updated'])) {
		?><div id="message" class="updated fade"><p><?php _e(urldecode($_GET['updatedmsg']), 'footer_content') ?></p></div><?php
	}

	$footer_content = get_theme_mod('footer_content');
	$footer_content_backend = 'theme_mod';
	if ( empty($footer_content) ) {
		$footer_content = get_option('footer_content');
		$footer_content_backend = 'option';
	}
	echo '<div class="wrap">';
	$action = isset($_GET[ 'action' ]) ? $_GET[ 'action' ] : '';
	switch( $action ) {
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
            <textarea name="footer_content" type="text" rows="5" wrap="soft" id="footer_content" style="width: 95%"/><?php echo $footer_content ?></textarea>
            <br /><?php _e('HTML allowed', 'footer_content') ?></td>
            </tr>
            </table>

            <p class="submit">
            <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'footer_content') ?>" />
			<input class="button button-secondary" type="submit" name="Reset" value="<?php _e('Reset', 'footer_content') ?>" />
            </p>
            </form>
			<?php
		break;
		//---------------------------------------------------//
		case "process":
			if ( isset( $_POST[ 'Reset' ] ) ) {
				update_option( "footer_content", "" );
				echo "
				<script type="text/javascript">
				window.location='themes.php?page=footer-content&updated=true&updatedmsg=" . urlencode(__('Settings cleared.', 'footer_content')) . "';
				</script>
				";
			} else {
				$footer_content =  stripslashes($_POST[ 'footer_content' ]);
				if ($footer_content_backend == 'theme_mod') {
					set_theme_mod( "footer_content", $footer_content );
				} else {
					update_option( "footer_content", $footer_content );
				}
				echo "
				<script type="text/javascript">
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

global $wpmudev_notices;
$wpmudev_notices[] = array( 'id'=> 76, 'name'=> 'Footer Content', 'screens' => array( 'appearance_page_footer-content' ) );
include_once(plugin_dir_path( __FILE__ ).'external/dash-notice/wpmudev-dash-notification.php');
