<?php
/*
 * @ class - wp_adpunch_lite
 *
 **/

if(!class_exists( 'wp_adpunch_lite' )):
class wp_adpunch_lite
{
	var $date_format;
	var $wpadp_modules;
	var $wpadp_content;
	var $ads2show;
	var $theme_height;

	/*
	*
	**/
	function __construct()
	{
		global $wpdb;

		$this->date_format 				= 'j M, Y H:i';
		$this->ads2show 					= array();
		$this->theme_height				= 0;

		$this->wpadp_modules 				= array( 'bar'	, 'slide'	, 'comments', 'banner' 	);
		$this->wpadp_content 				= array( 'twitter', 'message'	, 'rss'	, 'post' 	);

		define( "WPADPL_DIR" 				, ABSPATH . 'wp-content/plugins/'.basename(dirname(WPADPL_FILE)) . '/' );
		define( "WPADPL_URL"				, home_url().'/wp-content/plugins/'.basename(dirname(WPADPL_FILE)) . '/' );

		define( "WPADPL_VER"				, "1.0.0" 						);
		define( "WPADPL_DEBUG"				, false						);
		define( "WPADPL_ADMIN_PER_PAGE"		, 30							);

		define( "WPADPL_MSG_CACHE_TIME"		, 1							);

		global $wp_version;
		if ( version_compare($wp_version, '3.8', '>=' ) ){
			define( "WPADPL_ADMINBAR_HEIGHT"	, 32							);
			define( "WPADPL_ADMINBAR_HEIGHT2"	, 46							);
		} else {
			define( "WPADPL_ADMINBAR_HEIGHT"	, 28							);
			define( "WPADPL_ADMINBAR_HEIGHT2"	, 28							);
		}

		define( "WPADPL_ADS_TABLE"			, $wpdb->prefix . "wpadp_ads"		);
		define( "WPADPL_META_TABLE"			, $wpdb->prefix . "wpadp_meta"		);

		register_activation_hook ( WPADPL_FILE	, array( &$this, 'wpadp_activate'		)); 
		register_deactivation_hook ( WPADPL_FILE	, array( &$this, 'wpadp_deactivate'		));

		add_action( 'admin_menu'			, array( &$this, 'wpadp_options_page'	));
		add_action( 'admin_notices'			, array( &$this, 'wpadp_admin_notice' 	));

		add_action( 'plugins_loaded'			, array( &$this, 'wpadp_stylescript'	)); // lets serveCSS
		add_action( 'wp_enqueue_scripts'		, array( &$this, 'wpadp_enqueue_scripts' 	)); // lets queueCSS-Scripts

		add_filter( 'plugin_action_links'		, array( &$this, 'wpadp_plugin_actions'	), 10, 2 );

		define( '_IMGASC' 	, '<img src="'.WPADPL_URL . 'img/s_asc.png" width="11" border="0" alt="ASC" />' 	);
		define( '_IMGDESC' 	, '<img src="'.WPADPL_URL . 'img/s_desc.png" width="11" border="0" alt="DESC" />' 	);
		define( '_IMGEDIT' 	, '<img src="'.WPADPL_URL . 'img/edit.gif" width="16" border="0" alt="Edit" />' 	);
		define( '_IMGDEL' 	, '<img src="'.WPADPL_URL . 'img/delete.gif" width="16" border="0" alt="Delete" />' 	);
		define( '_IMGTICK'  	, '<img src="'.WPADPL_URL . 'img/tick.gif" width="16" border="0" alt="Login" />' 	);
		define( '_IMGQ'  		, '<img src="'.WPADPL_URL . 'img/question.png" width="16" border="0" alt="Question" />');
		define( '_IMGI'  		, '<img src="'.WPADPL_URL . 'img/info.png" width="16" border="0" alt="Info" />');
		define( '_IMGEXT'		, '<img src="'.WPADPL_URL . 'img/external.gif" width="16" border="0" alt="External Link" />');
		define( '_IMGSRTA'  	, '<img src="'.WPADPL_URL . 'img/s_asc.png" width="16" border="0" alt="Order" />' 	);
		define( '_IMGSRTD'  	, '<img src="'.WPADPL_URL . 'img/s_desc.png" width="16" border="0" alt="Order" />' 	);
		define( '_IMGATOOLTIP' 	, '&nbsp;&nbsp;<a href="javascript:;" class="tooltip">' 					);
	}

	/*
	*
	**/
	function wpadp_activate()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '". WPADPL_ADS_TABLE ."'") != WPADPL_ADS_TABLE ) {
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php' );
			/*
				adtype: See $this->wpadp_modules;
				showon: see @showon_var(); post:all;page:all;cat;tag;home;global;;; can a user set global ad to appear on home page; admin should
				globalad: enum( bar-top;slide-top;bar-bottom;slide-bottom;comment-left;comment-right; );
			**/

			dbDelta( 
				"CREATE TABLE `".WPADPL_ADS_TABLE ."` ( 
				`id` int(11) NOT NULL auto_increment, 
				`title` varchar(255) NOT NULL default '',
				`user_id` int(11) NOT NULL default '0',
				`showon`  varchar(255) default '',
				`settings` text,
				`adtype` varchar(50) default '',
  				`globalad` varchar(50) default '',
				`enabled` int(11) NOT NULL default '1',
				UNIQUE KEY `id` (`id`), 
				KEY `k_title` (`title`),
				KEY `k_user_id` (`user_id`),
				KEY `k_adtype` (`adtype`),
				KEY `k_enabled` (`enabled`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;"
			);
			/*
				rss;url_hash;titles;
				expiresat => RSS=>every 3 hours; Twitter => every 1 hour; post=>every 3 hours & updates when new is published;
			**/
			dbDelta( 
				"CREATE TABLE `".WPADPL_META_TABLE ."` ( 
				`id` int(11) NOT NULL auto_increment, 
				`bar_id` int(11) NOT NULL default '0',
				`user_id` int(11) NOT NULL default '0',
				`thisepxires`  int(11) NOT NULL default '0',
				`epxiresat` timestamp default '0000-00-00 00:00:00',
				`meta_key`  varchar(255) default '',
				`meta_value`  varchar(255) default '',
				`meta_values` text,
				UNIQUE KEY `id` (`id`), 
				KEY `k_bar_id` (`bar_id`),
				KEY `k_user_id` (`user_id`),
				KEY `k_meta_key` (`meta_key`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;"
			);
		}

		if( ! $wpadp_ver = get_option("wpadp_ver") )
			update_option ("wpadp_ver", WPADPL_VER);
	}

	/*
	*
	**/
	function wpadp_deactivate()
	{
		//nothing here.//
	}

	function wpadp_admin_notice()
	{
		global $wp_adpunch_slide, $wp_adpunch_side;

		if( ! empty( $wp_adpunch_side ) || ! empty( $wp_adpunch_slide ) )
		{
			$show_notice = false;

			if( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false )
				$show_notice = true;

			if( isset( $_GET['page'] ) && strpos( $_GET['page'], 'wpadp' ) !== false )
				$show_notice = true;

			if( $show_notice != false )
			{
			?>
				<div class="error">
					<p><?php _e( '<strong>WP AdPunch</strong>&#39;s Paid Modules only work with Full version of WP-AdPunch, Please <a href="http://wpadpunch.com/" target ="_blank"><b>upgrade to enjoy your module.</b></a>', 'wpadp_lang' ); ?></p>
				</div>
			<?php
			}
		}
	}

	/*
	*
	**/
	function wpadp_footer() 
	{
		$plugin_data = get_plugin_data( WPADPL_FILE );
		printf( '%1$s plugin | Version %2$s | by %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']); 
	}

	/*
	*
	**/
	function wpadp_page_footer() {
		echo '<br/><div id="page_footer" class="postbox" style="text-align:center;padding:10px;"><em>';
		self::wpadp_footer(); 
		echo '</em><br/>'.__('<strong>This is a Free Version, Please <a href="http://wpadpunch.com/" target ="_blank"><b>upgrade to enjoy ALL features.</b></a></strong>','wpadp_lang').'</div>';
	}

	/*
	*
	**/
	function wpadp_plugin_actions($links, $file)
	{
		if( strpos( $file, basename(WPADPL_FILE)) !== false )
		{
			$link = '<a href="http://wpadpunch.com/" target="_blank"><b>'.__( 'Upgrade', 'wpadp_lang' ).'</b></a>';
			array_push( $links, $link );
		}
		return $links;
	}

	/*
	*
	**/
	function wpadp_options_page()
	{
		global $wp_adpunch_admin, $wpadp_bar;
		add_menu_page( 'WP-AdPunch - Add Bar Ad'		, 'WP-AdPunch Lite'			, 8	, 'wpadp_addbar'	, array( &$wpadp_bar, 'wpadp_addbar' ) );
		add_submenu_page( 'wpadp_addbar', 'Add Bar Ad - WP-AdPunch Lite'	, 'Add: Bar Ad'	, 8	, 'wpadp_addbar'	, array( &$wpadp_bar, 'wpadp_addbar' ) );
		add_submenu_page( 'wpadp_addbar', 'List Ads - WP-AdPunch Lite', 'List Ads', 8	, 'wpadp_list'	, array( &$wp_adpunch_admin, 'wpadp_listads' ) );
		add_submenu_page( 'wpadp_addbar', 'Edit CSS - WP-AdPunch Lite', 'Edit CSS', 8	, 'wpadp_css'	, array( &$wp_adpunch_admin, 'wpadp_editcss' ) );
	}

	/*
	*
	**/
	function wpadp_help( $mes, $echo = true )
	{
		$html = '&nbsp;&nbsp;<a href="javascript:;" class="tooltip"><img src="'.WPADPL_URL.'img/question.png" width="16" border="0" alt="Question" />';
		$html .= '<span>'. $mes .'</span></a>'."\n";

		if( $echo != false )
			echo $html;
		else
			return $html;		
	}

	/*
	 *
	 *
	 * this function gets called on plugins_loaded 
	 *
	 * collective CSS for all ads on the front end;
	**/
	function wpadp_stylescript()
	{
		if( is_admin() || is_feed() )
			return;

		if( isset( $_GET['wpadp_style_css'] ) )
		{
			header("Content-Type: text/css");
			ob_start();
			do_action( 'wpadp_stylescripts', 'css' );
			$html = ob_get_contents();
			ob_end_clean();

			$html = preg_replace(array( '/\s+/', '/\t+/', '/\n+/', '/\r+/'), ' ', $html );
			echo $html;
			exit;
		}

		if( isset( $_GET['wpadp_style_js'] ) )
		{
			header("Content-Type: text/javascript");
			ob_start();
			do_action( 'wpadp_stylescripts', 'js' );
			$html = ob_get_contents();
			ob_end_clean();

			$html = preg_replace(array( '/\s+/', '/\t+/', '/\n+/', '/\r+/'), ' ', $html );
			echo $html;
			exit;
		}
	}

	/*
	 *
	 *
	 *
	 *
	 * enqueueing style/ script for all modules.//
	**/
	function wpadp_enqueue_scripts()
	{
		$css = '';
		$css	= apply_filters( 'wpadp_stylescript_css', $css );

		if( ! empty( $css ) )
			$css = implode( '&', $css );

		if( empty( $css ) )
			$css = '';

		wp_enqueue_style( 'wpadp_style1', home_url(). '?wpadp_style_css=1&'.$css  );

		if( file_exists( WPADPL_DIR . 'css/common.css' ) )
			wp_enqueue_style( 'wpadp_style2', WPADPL_URL . 'css/common.css'  );

		wp_enqueue_script(   'wpadp_js', home_url(). '/?wpadp_style_js=1&'.$css, array('jquery') );
	}

}
endif;

require_once  dirname(__FILE__).'/wp-adpunch-admin.php';
require_once  dirname(__FILE__).'/modules/wp-adpunch-head.php';
require_once dirname(__FILE__). '/modules/wp-adpunch-message.php';
require_once dirname(__FILE__). '/modules/wp-adpunch-bar.php';

global $wp_adpunch_lite;
if( ! $wp_adpunch_lite ) $wp_adpunch_lite = new wp_adpunch_lite();