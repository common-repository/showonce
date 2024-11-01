<?php

/**
 * Plugin Name: ShowOnce
 * Plugin URI: http://showonce.iltli.de/
 * Description: ShowOnce is a unique plugin that allows you to display content using a shortcode which will only display once. You set the conditions, even the requirement to show until use dismisses message.
 * Version: 1.1
 * Author: Abdelouahed Errouaguy
 * Author URI: mailto:wordpress@iltli.de
 * License: GPLv2 or later
**/

/*
	Developed by Abdelouahed Errouaguy (All rights reserved)
	Copyright © iliketotallyloveit GmbH 9-10 Neuebahnhofstr, 10245, Berlin, Germany.
	Amtsgericht Berlin-Charlottenburg, HRB 121185B
 
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

class SHOW_One {
	private $default_html = 
'<div class="ShowOnce">
  <a #Dismiss class="ShowOnce-Dismiss">×</a>
  #ShowOnce
</div>';
	private $default_css = 
'.ShowOnce {
  border: 1px solid #E6DB55;
  background: #FFFFE0;
  padding: 7px 10px;
  margin-bottom: 20px;
  position: relative;
}
.ShowOnce-Dismiss {
  position: absolute;
  top: 0px;
  right: 10px;
  cursor: pointer;
  font-weight: bold;
}';
	private $default_js = "";
	
	// Construct
	function __construct() {
		global $wpdb;
		$this->url = plugins_url('/',__FILE__);
		$this->url_ajax = get_bloginfo('home').'/wp-admin/admin-ajax.php';
		$this->dir = __DIR__;
		$this->db = $wpdb;
		$this->table = $wpdb->prefix.'showonce_dismisses';
		
		register_activation_hook(__FILE__,array(&$this,'activation'));
		register_deactivation_hook(__FILE__, array(&$this,'deactivation'));
		
		add_action('init', array(&$this,'init'));
	}
	
	// Activation
	function activation() {
		$this->db->query("
		CREATE TABLE IF NOT EXISTS {$this->table} (
			`post_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL
		);
		");
	}
	
	// Deactivation
	function deactivation() {
		$this->db->query("DROP TABLE IF EXISTS {$this->table}");
	}
	
	// Init
	function init() {
		add_action('admin_menu', array(&$this,'admin_menus'));
		add_action('add_meta_boxes', array(&$this,'meta_boxes') );
		add_action( 'save_post', array(&$this,'meta_save') );
		
		/* ShowOnce */
		$labels = array(
			'name'                => 'ShowOnce Posts',
			'singular_name'       => 'ShowOnce Post',
			'all_items'           => 'All ShowOnce Posts',
			'view_item'           => 'View ShowOnce Post',
			'add_new_item'        => 'Add New ShowOnce Post',
			'add_new'             => 'New ShowOnce Post',
			'edit_item'           => 'Edit ShowOnce Post',
			'update_item'         => 'Update ShowOnce Post',
			'search_items'        => 'Search ShowOnce Posts',
			'not_found'           => 'No ShowOnce Posts found',
			'not_found_in_trash'  => 'No ShowOnce Posts found in Trash',
		);
		
		$args = array(
			'label'               => 'ShowOnce Posts',
			'description'         => 'Custom post type to allow adding ShowOnce Posts',
			'labels'              => $labels,
			'supports'            => array( 'title','editor' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'showonce', $args );
		
		/* Styles */
		$labels = array(
			'name'                => 'ShowOnce Styles',
			'singular_name'       => 'ShowOnce Style',
			'all_items'           => 'All ShowOnce Styles',
			'view_item'           => 'View ShowOnce Style',
			'add_new_item'        => 'Add New ShowOnce Style',
			'add_new'             => 'New ShowOnce Style',
			'edit_item'           => 'Edit ShowOnce Style',
			'update_item'         => 'Update ShowOnce Style',
			'search_items'        => 'Search ShowOnce Styles',
			'not_found'           => 'No ShowOnce Styles found',
			'not_found_in_trash'  => 'No ShowOnce Styles found in Trash',
		);
		
		$args = array(
			'label'               => 'ShowOnce Styles',
			'description'         => 'Custom post type to allow adding ShowOnce Styles',
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'showonce_style', $args );
		
		if(is_admin()) {
			add_filter('manage_showonce_posts_columns', array(&$this,'column_id_post'), 5);
			add_action('manage_showonce_posts_custom_column', array(&$this,'column_id_content'), 5, 2);
			
			add_action('admin_footer-edit.php', array(&$this, 'custom_bulk_admin_footer'));
			add_action('load-edit.php',         array(&$this, 'custom_bulk_action'));
			add_action('admin_notices',         array(&$this, 'custom_bulk_admin_notices'));
			
			add_filter('manage_showonce_style_posts_columns', array(&$this,'column_id_style'), 5);
			add_action('manage_showonce_style_posts_custom_column', array(&$this,'column_id_content'), 5, 2);
			
			add_action('admin_head', array(&$this,'admin_head'));
			
			add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array( &$this, 'plugin_manage_link' ), 10, 4 );
		}
		
		
		$this->user = wp_get_current_user();
		add_action('wp_ajax_showonce_dismiss', array(&$this,'ajax_dismiss')); // Ajax for members
		// add_action('wp_ajax_nopriv_showonce_dismiss', array(&$this,'ajax_dismiss')); // Ajax for guests
		
		wp_register_script('showonce_dismiss',$this->url.'js/dismiss.js','jquery');
		
		$sc_alias = array( 'showonce','Showonce','ShowOnce','SHOWONCE' );
		foreach( $sc_alias as $alias ){
			add_shortcode( $alias, array(&$this,'shortcode') );
		}
	}
	
	// Plugin Links
	function plugin_manage_link($actions, $plugin_file, $plugin_data, $context) {
		unset($actions['edit']);
		$actions['support'] = '<a href="edit.php?post_type=showonce&page=showonce_settings" title="Help & Support">Support</a>';
		$actions['website'] = '<a href="http://showonce.iltli.de/" title="Official site" target="_blank">Official site</a>';
		return $actions;
	}
	
	// Shortcode
	function shortcode($attrs=array()) {
		extract( shortcode_atts( array(
			'show' => 'once',
			'post' => false,
			'style' => false,
			'limit' => 0,
			'start' => 0,
			'end' => 0
		), $attrs ) );
		$html = '';
		
		if( $start ) $start = strtotime($start)-time();
		if( $end ) $end = strtotime($end)-time();
		$indate = ($start<=0 and $end>=0);
		
		if( $indate and !empty($post) ) {
			$js = "var URL_Ajax = '$this->url_ajax';";
			$css = '';
			
			$posts = str_replace(' ','',(string)$post);
			$posts = explode(',',$posts);
			
			if( empty($style) or get_post_type($style)!='showonce_style' or !$code_html=get_post_meta($style,'code_html',true) ) {
				$code_html = $this->default_html;
			}
			
			$count = 0;
			foreach( $posts as $post ) {
				$post = intval($post);
				if( get_post_type($post)=='showonce' and $the_post = get_post($post) ) {
					if( $this->dismissed( $post ) ) {
						continue;
					} else {
						$count++;
					}
					$filters = array(
						'/#showonce/i' => $the_post->post_content,
						'/#dismiss/i' => 'data-post="'.$post.'"'
					);
					if( $show=='once' ) {
						$this->dismiss($post);
					}
					$showonce = preg_replace(array_keys($filters),array_values($filters),$code_html);
					$html .= do_shortcode($showonce);
					if( $count==$limit ) {
						break;
					}
				}
			}
			if( $count ) {
				wp_enqueue_script('jquery');
				wp_enqueue_script('showonce_dismiss');
				
				if( $style ) {
					if( $code_css = get_post_meta($style,'code_css',true) ) {
						$css .= $code_css;
					}
					
					if( $code_js = get_post_meta($style,'code_js',true) ) {
						$js .= $code_js;
					}
				} else {
					$css .= $this->default_css;
				}
				if( $css ) echo '<style type="text/css">'.$css.'</style>';
				if( $js ) echo '<script type="text/javascript">'.$js.'</script>';
			} else {
				$html = '';
			}
		}
		
		return $html;
	}
	
	// Dismiss posts
	function dismissed( $post ) {
		if( !empty($this->user->ID) and !empty($post) and is_int($post) and $post>0 ) {
			$user = $this->user->ID;
			return $this->db->get_var("SELECT post_id FROM {$this->table} WHERE user_id=$user and post_id=$post limit 1");
		} else {
			return true;
		}
	}
	function dismiss( $post ) {
		if( !empty($this->user->ID) and !empty($post) and is_int($post) and $post>0 ) {
			$user = $this->user->ID;
			return $this->db->query("INSERT INTO {$this->table} values ($post,$user)");
		}
		return false;
	}
	function clearhistory( $post ) {
		if( !empty($post) and is_int($post) and $post>0 ) {
			return $this->db->query("DELETE FROM {$this->table} WHERE post_id=$post");
		}
		return false;
	}
	function ajax_dismiss() {
		$post = intval($_POST['post']);
		
		if( $this->dismissed( $post ) ) {
			echo 'ok';
		} else if( $this->dismiss($post) ) {
			echo 'ok';
		} else {
			echo 'error';
		}
		die;
	}
	
	// Add ID column to posts and styles
	function column_id_post($defaults){
		$defaults['ID'] = __('Post ID');
		return $defaults;
	}
	function column_id_style($defaults){
		$defaults['ID'] = __('Style ID');
		return $defaults;
	}
	function column_id_content($column_name, $id){
		if($column_name === 'ID'){
			echo $id;
		}
	}
	
	// Add Reset action to posts
	function custom_bulk_admin_footer() {
		global $post_type;

		if($post_type == 'showonce') {
		?>
		<script type="text/javascript">
		(function($) {
			$('<option>').val('clearhistory').text('<?php _e('Clear History')?>').appendTo("select[name='action']");
			$('<option>').val('clearhistory').text('<?php _e('Clear History')?>').appendTo("select[name='action2']");
		})(jQuery);
		</script>
		<?php
		}
	}
	function custom_bulk_action() {
		global $typenow;
		$post_type = $typenow;
		
		if($post_type == 'showonce') {
			$wp_list_table = _get_list_table('WP_Posts_List_Table');
			$action = $wp_list_table->current_action();

			$allowed_actions = array('clearhistory');
			if(!in_array($action, $allowed_actions)) {
				return;
			}
			check_admin_referer('bulk-posts');
			
			if(isset($_REQUEST['post'])) {
				$post_ids = array_map('intval', $_REQUEST['post']);
			}
			if(empty($post_ids)) return;
			
			$sendback = remove_query_arg( array('cleared', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
			if( !$sendback ) $sendback = admin_url( "edit.php?post_type=$post_type" );

			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
			
			switch($action) {
				case 'clearhistory':
					$cleared = 0;
					foreach( $post_ids as $post_id ) {
						if( $this->clearhistory(intval($post_id)) ) {
							$cleared++;
						}
					}
					$sendback = add_query_arg( array('cleared' => $cleared, 'ids' => join(',', $post_ids) ), $sendback );
				break;
				
				default: return;
			}

			$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );

			wp_redirect($sendback);
			exit();
		}
	}
	function custom_bulk_admin_notices() {
		global $post_type, $pagenow;

		if($pagenow == 'edit.php' && $post_type == 'showonce' && isset($_REQUEST['cleared']) && (int) $_REQUEST['cleared']) {
			$message = sprintf( _n( 'Post cleared.', '%s ShowOnce data cleared. All users may now begin to see post information again, if published.', $_REQUEST['cleared'] ), number_format_i18n( $_REQUEST['cleared'] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";
		}
	}
	
	// Meta Boxes
	function meta_boxes() {
		add_meta_box( 'html', 'Html Container', array(&$this,'meta_box_html'), 'showonce_style' );
		add_meta_box( 'css', 'Custom CSS', array(&$this,'meta_box_css'), 'showonce_style' );
		add_meta_box( 'js', 'Custom Javascript', array(&$this,'meta_box_js'), 'showonce_style' );
	}
	function meta_box_html($post) {
		wp_nonce_field( basename(__FILE__), 'showonce_style_nonce' );
		$html = get_post_meta( $post->ID,'code_html',true )?:$this->default_html;
		?>
		<textarea name="code_html" id="code_html" rows="3"><?php echo $html ?></textarea>
		<?php
	}
	function meta_box_css($post) {
		$css = get_post_meta( $post->ID,'code_css',true );
		if( empty($_GET['post']) ) {
			$css = $css?:$this->default_css;
		}
		?>
		<textarea name="code_css" id="code_css" rows="3"><?php echo $css ?></textarea>
		<?php
	}
	function meta_box_js($post) {
		$js = get_post_meta( $post->ID,'code_js',true )?:$this->default_js;
		?>
		<textarea name="code_js" id="code_js" rows="3"><?php echo $js ?></textarea>
		<link rel="stylesheet" href="<?php echo $this->url ?>css/codemirror.css">
		<style type="text/css">
		.postbox textarea {
			width: 100%;
			min-height: 100px;
			resize: vertical;
		}
		.CodeMirror {
			height: auto;
			margin-left: -10px;
			line-height: 1.4;
		}
		.CodeMirror-scroll {
			overflow-y: hidden;
			overflow-x: auto;
		}
		</style>
		<script src="<?php echo $this->url ?>js/codemirror/codemirror.js"></script>
		<script src="<?php echo $this->url ?>js/codemirror/addon/closetag.js"></script>
		<script src="<?php echo $this->url ?>js/codemirror/mode/xml.js"></script>
		<script src="<?php echo $this->url ?>js/codemirror/mode/javascript.js"></script>
		<script src="<?php echo $this->url ?>js/codemirror/mode/css.js"></script>
		<script src="<?php echo $this->url ?>js/codemirror/mode/htmlmixed.js"></script>
		<script type="text/javascript">
		jQuery("#normal-sortables").remove();
		jQuery("#toplevel_page_edit-post_type-showonce").attr('class','wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_edit?post_type=showonce').children('a').attr('class','wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_edit?post_type=showonce');
		var code_html = CodeMirror.fromTextArea(document.getElementById("code_html"), {
			mode: 'text/html',
			autoCloseTags: true,
			lineNumbers: true,
			lineWrapping: true,
			viewportMargin: Infinity
		});
		var code_css = CodeMirror.fromTextArea(document.getElementById("code_css"), {
			mode: 'text/css',
			autoCloseTags: true,
			lineNumbers: true,
			lineWrapping: true,
			viewportMargin: Infinity
		});
		var code_js = CodeMirror.fromTextArea(document.getElementById("code_js"), {
			mode: 'text/javascript',
			autoCloseTags: true,
			lineNumbers: true,
			lineWrapping: true,
			viewportMargin: Infinity
		});
		</script>
		<?php
	}
	function meta_save( $post_id ) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'showonce_style_nonce' ] ) && wp_verify_nonce( $_POST[ 'showonce_style_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;
		
		if( isset( $_POST['code_html'] ) ) {
			update_post_meta( $post_id, 'code_html', $_POST['code_html'] );
		}
		if( isset( $_POST['code_css'] ) ) {
			update_post_meta( $post_id, 'code_css', $_POST['code_css'] );
		}
		if( isset( $_POST['code_js'] ) ) {
			update_post_meta( $post_id, 'code_js', $_POST['code_js'] );
		}
	}
	
	// Admin Menu
	function admin_menus() {
		$parent_slug = 'edit.php?post_type=showonce';
		$capability = 'edit_posts';
		
		add_menu_page( 'ShowOnce Posts', 'ShowOnce', $capability, $parent_slug, '', '', 4 );
		add_submenu_page( $parent_slug, 'ShowOnce Posts', 'ShowOnce Posts', $capability, $parent_slug, '' );
		add_submenu_page( $parent_slug, 'ShowOnce Styles', 'ShowOnce Styles', $capability, 'edit.php?post_type=showonce_style', '' );
		add_submenu_page( $parent_slug, 'ShowOnce Help and Support', 'Help & Support', $capability, 'showonce_settings', array(&$this,'display_pages') );
	}
	
	// Admin head
	function admin_head() {
		global $menu;
		foreach( $menu as $key => $value )
		{
			if( $value[0]=='ShowOnce' ) $menu[$key][4] .= " toplevel_showonce";
		}
		wp_register_style('showonce_admin', $this->url.'css/admin.css');
		wp_enqueue_style('showonce_admin');
	}
	
	// Setting and Pages
	function display_pages() {
		$tab = empty($_GET['tab'])?'support':$_GET['tab'];
		if( !in_array($tab,array('support','project')) ) $tab = 'support';
	?>
		<div id="showonce" class="wrap">
			<a class="icon32" id="icon-showonce" href="http://showonce.iltli.de/" target="_blank"><br></a>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if($tab=='support') echo ' nav-tab-active' ?>" href="?post_type=showonce&page=showonce_settings&tab=support">ShowOnce Support</a>
				<a class="nav-tab<?php if($tab=='project') echo ' nav-tab-active' ?>" href="?post_type=showonce&page=showonce_settings&tab=project">Need help on your project?</a>
			</h2>
			<iframe src="http://contact.iltli.de/<?php echo $tab ?>" frameborder="0" width="100%" height="500px"></iframe>
		</div>
	<?php
	}
}
new SHOW_One();