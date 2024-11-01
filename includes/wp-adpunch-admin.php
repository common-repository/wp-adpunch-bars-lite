<?php
/*
 * @ class - wp_adpunch_admin
 *
 **/


if(!class_exists('wp_adpunch_admin')):
class wp_adpunch_admin
{
	var $date_format;

	function __construct()
	{
		global $wpdb;

		$this->date_format 				= 'j M, Y H:i';

		add_action( 'admin_head'			, array( &$this, 'wpadp_admin_header'	));
		add_action( 'admin_enqueue_scripts'		, array( &$this, 'wpadp_admin_style'	));

		add_action( 'admin_menu'			, array( &$this, 'wpadp_meta_box'		));
		add_action( 'save_post'				, array( &$this, 'wpadp_save_data'		));
	}

	/*
	*
	**/
	function wpadp_admin_style()
	{
		if( is_admin() && 
			( 
				strpos( $_GET['page'], 'wpadp' ) !== false ||
				strpos( $_SERVER['REQUEST_URI'], '/post.php' )!== false ||
				strpos( $_SERVER['REQUEST_URI'], '/post-new.php' )!== false 
			)
		)
		{
			wp_enqueue_style( 'wpadp_style', WPADPL_URL. 'css/admin.css' );
		}
	}

	/*
	*
	**/
	function wpadp_admin_header()
	{
		global $wpdb;

		if( is_admin() && 
			( 
				strpos( $_GET['page'], 'wpadp' ) !== false ||
				strpos( $_SERVER['REQUEST_URI'], '/post.php' )!== false ||
				strpos( $_SERVER['REQUEST_URI'], '/post-new.php' )!== false 
			)
		)
		{
			?>
		<script type="text/javascript">
		if(typeof jQuery == "function") {
			jQuery(document).ready(function($) {
				$("#socialicons").click(function(){
					if( $(this).is(":checked") != false )
						$("#wpadp_ssocialicons").slideDown("slow");
					else
						$("#wpadp_ssocialicons").slideUp("slow");
				});

				$("#wpadp_manage_campaign thead tr:last th:first input:checkbox").click(function() {
					var checkedStatus = this.checked;
					$("#wpadp_manage_campaign tbody tr td:first-child input:checkbox").each(function() {
						this.checked = checkedStatus;
					});
				});
			});
		}

		function chkbulkform()
		{
			if(jQuery("#bulkselect :selected").val() == 'noaction') return false;
			if(jQuery("#bulkselect :selected").val() == 'multidelete')
			{
				ret = confirm("Are you sure you want to delete\nThere is no undo, click 'cancel' to stop.");
				if(ret) 
					return true;
				else
					return false;
			}
		}

		function doDelete(obj)
		{
			ret = confirm("Are you sure you want to delete this Ad\nThere is no undo, click 'cancel' to stop");
			if(ret) 
				return true;
			else
				return false;
		}
		</script>
			<?php
		}
	}

	/*
	*
	**/
	function wpadp_meta_box()
	{
		add_meta_box( 'wpadp-meta-box', 'WP-AdPunch Bar Ads', array( &$this, 'wpadp_show_box' ), 'post', 'side', 'high' );
		add_meta_box( 'wpadp-meta-box', 'WP-AdPunch Bar Ads', array( &$this, 'wpadp_show_box' ), 'page', 'side', 'high' );
	}

	/*
	*
	**/
	function wpadp_getadselection( $topadid = 0, $bottomadid  = 0 )
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		$adstoshow = " ( 'bar', 'slide' )";
		$ads = $wpdb->get_results("SELECT * FROM `".WPADPL_ADS_TABLE."` WHERE adtype IN ".$adstoshow."  AND enabled=1 ORDER BY adtype", ARRAY_A);//userid

		$html1 = '<select name="wpadp_topad" id="wpadp_topad">
				<option value="">'.__('Show Global Ad','wpadp_lang').'</option>
				<option value="none"'. selected( "none", $topadid , false ).'>'.__('Display No Ads','wpadp_lang').'</option>';
		$html2 = '<select name="wpadp_bottomad" id="wpadp_bottomad">
				<option value="">'.__('Show Global Ad','wpadp_lang').'</option>
				<option value="none"'. selected( "none", $bottomadid , false ).'>'.__('Display No Ads','wpadp_lang').'</option>';

		foreach( $ads as $ad )
		{
			$ad['settings'] = maybe_unserialize( $ad['settings'] );
			$pos = ( in_array( $ad['settings']['wpadp_pos'], array( 'topf', 'topfl' ) )? 'top': 'bottom' );

			if( $pos == 'top' )
				$html1 .= '<option value="'.$ad['id'].'" '. selected( $ad['id'], $topadid , false ).'>'.$ad['title'].'  ['.$ad['adtype'].']</option>'."\n";
			else
				$html2 .= '<option value="'.$ad['id'].'" '. selected( $ad['id'], $bottomadid , false ).'>'.$ad['title'].'  ['.$ad['adtype'].']</option>'."\n";
		}

		$html1 .= '</select>';
		$html2 .= '</select>';
		return array( $html1, $html2 );
	}

	/*
	*
	**/
	function wpadp_show_box()
	{
		global $meta_box, $post;

		$topad 	= get_post_meta( $post->ID, '_wpadp_topad', true );
		$bottomad 	= get_post_meta( $post->ID, '_wpadp_bottomad', true );

		$updated = false;

		list( $html1, $html2 )	= $this->wpadp_getadselection( $topad, $bottomad );
		$wpadp_disableall		= get_post_meta( $post->ID, '_wpadp_disableall', true );

	?>
		<input type="hidden" name="wpadp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>" />
		<table class="widefat" width="100%">

		<tr valign="top"><td><strong><?php _e( 'AdPunch Top Ads:','wpadp_lang' )?></strong></td></tr><tr><td><?php echo $html1; ?></td></tr>
		<tr valign="top"><td style="border-bottom:1px dotted #000;"><?php _e( 'Choose a Top Ad ( Top Bar/ Top slide ) to Show on this Post/ Page.','wpadp_lang' ); ?></td></tr>

		<tr valign="top"><td><strong><?php _e( 'Disable ALL Ads for this post:','wpadp_lang' ); ?></strong></td></tr><tr><td><input type="checkbox" name="wpadp_disableall" id="wpadp_disableall" value="on" <?php checked( $wpadp_disableall, "on" ); ?>/></td></tr>
		<tr valign="top"><td style="border-bottom:1px dotted #000"><?php _e( 'Check this to disable all Ads on this Post/ Page.','wpadp_lang' ); ?></td></tr>

		</table>
	<?php
	}

	/*
	*
	**/
	function wpadp_save_data( $post_id )
	{
		global $wpdb;

		// verify nonce
		if (!wp_verify_nonce($_POST['wpadp_meta_box_nonce'], basename(__FILE__))) {
			return $post_id;
		}

		// check autosave
		if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check permissions
		if ( 'page' == $_POST['post_type']) {
			if (!current_user_can( 'edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can( 'edit_post', $post_id)) {
			return $post_id;
		}

		$postid = (int) trim( $_POST['post_ID'] );

		//--
		$topad 	= get_post_meta( $postid, '_wpadp_topad', true );

		$topadnew 	= trim( esc_attr( strip_tags( stripslashes( $_POST['wpadp_topad'] ) ) ) );

		if($topadnew && $topadnew != $topad) 
			update_post_meta( $postid, '_wpadp_topad', $topadnew );
		elseif( '' == $topadnew && $topad ) 
			delete_post_meta( $postid, '_wpadp_topad', $topad );

		//--

		$disableall = get_post_meta( $postid, '_wpadp_disableall', true );
		$disableallnew = trim( esc_attr( strip_tags( stripslashes( $_POST['wpadp_disableall'] ) ) ) );
		if( $disableallnew != 'on' ) $disableallnew = '';

		if($disableallnew && $disableallnew != $disableall) 
			update_post_meta( $postid, '_wpadp_disableall', $disableallnew );
		elseif( '' == $disableallnew && $disableall ) 
			delete_post_meta( $postid, '_wpadp_disableall', $disableall );
	}

	/*
	*
	**/
	function wpadp_main()
	{
		global $wpdb, $wpadp_twitter;
		if (!current_user_can( 'manage_options' )) wp_die(__( 'Sorry, but you have no permissions to change settings.' ));

		?>
		<div class="wrap">
		<h2><?php _e( 'WP-AdPunch Lite', 'wpadp_lang' )?></h2>
		<h3><?php _e( 'WP-AdPunch ~ Redefining Ad Spaces!','wpadp_lang' );?></h3>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">

		<?php
		if( ! empty ( $wpadp_twitter ) ) 
			$wpadp_twitter->wp_adpunch_settings();
		?>

	  <hr class="clear" />
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">
		  <?php $this->wpadp_admin_side(); ?>
	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->
	</div><!-- /wrap -->

	<!-- ==================== -->
	<?php
		wp_adpunch_lite::wpadp_page_footer();
	}

	function wpadp_daily_feed() { return ( 60 * 60 * 24 * 2 ); }

	function wpadp_admin_side_boxs()
	{
		global $wp_adpunch_admin;

		include_once(ABSPATH . WPINC . '/rss.php');
	 
		add_filter( 'wp_feed_cache_transient_lifetime', array( &$wp_adpunch_admin, 'wpadp_daily_feed' ) );
		$rss = @fetch_feed( "http://wpadpunch.com/?wpadpserver_xml=1" );
		remove_filter('wp_feed_cache_transient_lifetime', array( &$wp_adpunch_admin, 'wpadp_daily_feed' ) );

		if ( is_wp_error($rss) )
		{
			?>
	 	   <div id="modsidediv-err" class="postbox"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
	 	     <h3 class='hndle'><span><?php _e('SimplePie Error');?></span></h3>
	 	     <div class="inside">
				<p><?php echo sprintf(__("<b>SimplePie Error: </b> %s"), (implode("<br/>", $rss->get_error_messages() ))); ?></p>
	 	     </div>
		    </div>
			<?php
			return;
		}

		if( empty( $rss ) ) return;

		$max = $rss->get_item_quantity();
		$max = min( $max, 10 );

		if( $max == 0 ) return;

		for ($x = 0; $x < $max; $x++)
		{
			$item 	= $rss->get_item($x);

			$this_item 	= reset($item->data['child']);
			$this_class = trim( esc_attr( strip_tags( $this_item['item_class'][0]['data'] ) ) );
			$this_version = trim( esc_attr( strip_tags( $this_item['item_version'][0]['data'] ) ) );
			$this_version = str_replace( '.', '_', $this_version );

			$link 	= esc_url(strip_tags($item->get_permalink()));
			$title	= esc_html($item->get_title());
			$date 	= $item->get_date('Y-m-d');
			$content 	= html_entity_decode( $item->get_description(), ENT_QUOTES, get_option('blog_charset'));
	?>
 	   <div id="modsidediv-<?php echo $x;?>" class="postbox <?php echo $this_class .'_xml '.$this_class .'-'. $this_version.'_xml';?>"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
 	     <h3 class='hndle'><span><?php echo $title;?></span></h3>
 	     <div class="inside">
			<p><?php echo $content; ?></p>
 	     </div>
	    </div>
	<?php
		}
	}

	/*
	*
	**/
	function wpadp_admin_side() 
	{
	?>
 	   <div id="followdiv" class="postbox"><div class="handlediv" title="<?php _e( 'Click to toggle', wpadp_LOCAL_NAME ); ?>"><br /></div>
 	     <h3 class='hndle'><span><?php _e( 'Help us Spread the word', 'wpadp_lang' );?></span></h3>
 	     <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="200px">
		<tr><td>

			<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FWpAdPunch&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe><br/>

			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wpadpunch.com" data-text="Wordpress Plugin WP-AdPunch Bar - Redefining Ad Spaces" data-via="WPAdPunch" data-hashtags="WPAdPunch, Wordpress, Plugin">Tweet</a>
			<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script><br/>

			<!-- Place this tag where you want the +1 button to render -->
			<g:plusone annotation="inline" href="http://wpadpunch.com"></g:plusone>
		
		<!-- Place this render call where appropriate -->
			<script type="text/javascript">
			  (function() {
			    var po = document.createElement( 'script' ); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName( 'script' )[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script>

			</td></tr>
			</table>
 	     </div>
 	   </div>
 	   <div id="supportdiv" class="postbox"><div class="handlediv" title="<?php _e( 'Click to toggle', wpadp_LOCAL_NAME ); ?>"><br /></div>
 	     <h3 class='hndle'><span><?php _e( 'Need Support', 'wpadp_lang' );?></span></h3>
 	     <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr><td><?php echo sprintf( __( 'If you are having problems with this plugin, or if you&#39;re sure you&#39;ve found a bug, or have a feature request, be sure <a href="%s" target="_blank">Support</a> is just a click away','wpadp_lang' ), 'http://wpadpunch.com/contact-us/' );?>
			</td></tr>
			</table>
 	     </div>
	    </div>
	<?php

		self::wpadp_admin_side_boxs();
	}

	/*
	*
	**/
	function wpadp_setGlobal( $id, $adtype )
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		if( empty( $id ) )
			return false;

		list( $adtype, $pos ) = explode( '-', $adtype );

		if( in_array( $adtype, array( 'bar', 'slide' ) ) ) // top/ bottom//
		{
			$pos = ( $pos == 'top'? 'top': 'bottom' );

			$wpdb->query( "UPDATE `".WPADPL_ADS_TABLE."` SET `globalad` = '' WHERE `globalad` IN ( 'bar-".$pos."', 'slide-".$pos."' )" );//userid
			$globalad = $adtype .'-'. $pos;

			$wpdb->update( WPADPL_ADS_TABLE, array( 'globalad' => $globalad, 'enabled' => 1 ), array( 'id' => $id ) );//userid
		}
		else if( in_array( $adtype, array( 'comment' ) ) ) // left / right//
		{
			$pos = ( $pos == 'left' ? 'left': 'right' );

			$wpdb->query( "UPDATE `".WPADPL_ADS_TABLE."` SET `globalad` = '' WHERE `globalad` IN ( 'comment-".$pos."', 'comment-".$pos."' )" );//userid
			$globalad = $adtype .'-'. $pos;

			$wpdb->update( WPADPL_ADS_TABLE, array( 'globalad' => $globalad, 'enabled' => 1 ), array('id' => $id ) );//userid
		}
		return true;
	}

	/*
	*
	**/
	function wpadp_listads()
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		if (!current_user_can( 'manage_options' )) wp_die(__( 'Sorry, but you have no permissions to change settings.' ));

		if(isset($_REQUEST['call2']) && trim( $_REQUEST['call2'] ) == 'edit' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
		{
			$id = (int) trim( strip_tags( $_REQUEST['id'] ) );
			$this->wpadp_addcampaigns( $id );
			return;
		}

		if(isset($_REQUEST['call2']) && trim( $_REQUEST['call2'] ) == 'global' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
		{
			$id = (int) trim( strip_tags( $_REQUEST['id'] ) );
			$adtype = trim( strip_tags( $_REQUEST['adtype'] ) );
			$this->wpadp_setGlobal( $id, $adtype );
			$result1 = __('Ad has been set as Global','wpadp_lang');
		}

			/**
			 * delete and multi-delete here
			 */
			if(isset($_REQUEST['call2']) && $_REQUEST['call2'] == 'delete' )
			{
				$results 		= $wpdb->query( "DELETE FROM `". WPADPL_ADS_TABLE ."` WHERE `id` = ".(int)esc_attr($_REQUEST['id']) );//userid
				$result1 		= __('Ad has been deleted', 'wpadp_lang');
				unset($results);
			}

			if(isset($_REQUEST['bulk']) && in_array( trim( $_REQUEST['bulk'] ), array( 'multidelete', 'multienable', 'multidisable' ) ) )
			{
				check_admin_referer('list-wpadp-campaign');
				$deleteIds 		= '';
				$count 		= 0;
				$check_select 	= $_REQUEST['check_select'];
				if(!empty($check_select))
				{
					foreach($check_select as $id => $val)
					{
						if($val == 'on')
							$deleteIds .= $deleteIds? ', '. $id: $id;
					}

					if( trim( $_REQUEST['bulk'] ) == 'multidelete' )
					{
						$results 	= $wpdb->query( "DELETE FROM `". WPADPL_ADS_TABLE ."` WHERE  `id` IN (".$deleteIds.")" );//userid
						$result1 	= __('Ad(s) have been deleted', 'wpadp_lang');
						unset($results);
					}

					if( trim( $_REQUEST['bulk'] ) == 'multienable' )
					{
						$results 	= $wpdb->query( "UPDATE `". WPADPL_ADS_TABLE ."` SET enabled = 1 WHERE  `id` IN (".$deleteIds.")" );//userid
						$result1 	= __('Ad(s) have been Enabled', 'wpadp_lang');
						unset($results);
					}

					if( trim( $_REQUEST['bulk'] ) == 'multidisable' )
					{
						$results 	= $wpdb->query( "UPDATE `". WPADPL_ADS_TABLE ."` SET enabled = 0 WHERE  `id` IN (".$deleteIds.")" );//userid
						$result1 	= __('Ad(s) have been Disabled', 'wpadp_lang');
						unset($results);
					}
				}
				else
					$err .= __('<li>No Ads selected</li>', 'wpadp_lang');
			}

			/******* END OF DELETE ***********/


		/**
		 * showing all campaigns
		 */

			/* ########## the 'where' part ########## */
			$where = " WHERE 1 ";//userid
			if( isset( $_REQUEST['view'] ) && is_numeric( $_REQUEST['view'] ) )
			{
				$view =  trim( $_REQUEST['view'] );

				$ads = array_keys( $adtype );
				if( in_array( $view, $ads ) )
					$where .= " AND `adtype` = '". $view . "' ";
				else
					$where .= " AND 1 ";
			}

			/* ########## the 'order' part ########## */
			$order	= '';
			$sort 	= '';

			$order_val 	= array('id', 'title', 'adtype', 'enabled' ); 
			$columns 	= array('id', 'title', 'adtype', 'enabled', 'globalad' );

			if(isset($_GET['o']) && $_GET['o'] != '')
			{
				if(in_array($_GET['o'], $order_val))
				{
					if(isset($_GET['s']) && $_GET['s'] != '')
						$sort = ($_GET['s'] == 'desc')? 'DESC':'';

					$order = " ORDER BY `".$_GET['o']."` ".$sort."";
					if($_GET['o'] != 'id') $order .= ", `id`";
				}
				else
				{
					// if any other value is passed, unset it immediately.//
					unset($_GET['o']);
					if(isset($_GET['s'])) unset($_GET['s']);
				}
			}
			if(!$order) 
			{
				$order = " ORDER BY `id`, `title` ";
				$_GET['o'] = 'id';
			}

			unset($order_val);

			/* ########### the paging part ######### */

			$tcount 	= $wpdb->get_var( "SELECT count(*) as count FROM `". WPADPL_ADS_TABLE ."` {$where}" );

			if($tcount > 0)
			{
				if(isset($_GET['p']) && $_GET['p'] != '')
				{ 
					$page = (int)$_GET['p'];
					$page = is_numeric($page)?$page:1;
					if($page > 1)
				 		$limit = " LIMIT ". (($page-1) * WPADPL_ADMIN_PER_PAGE) .", ". WPADPL_ADMIN_PER_PAGE."";
					else
				 		$limit = " LIMIT ".WPADPL_ADMIN_PER_PAGE;
				}
				else
				{
					$page = 1;
			 		$limit = " LIMIT ".WPADPL_ADMIN_PER_PAGE;
				}
				/* ======= the paging part ======= */

				$results = $wpdb->get_results( "SELECT * FROM `". WPADPL_ADS_TABLE ."` {$where} {$order} $limit" );
 
				/* ################### the paging contd... ################### */

				$pages = $tcount/ WPADPL_ADMIN_PER_PAGE;
				if($pages < 1)$pages = 1;
				else if( (int)$pages != $pages ) $pages = (int)$pages +1;

				if($page > 4)
				{
					$ll = $page - 4;
			 		$ul = $page + 4;
					$ul = min($ul, $pages);
				}
				else
				{
					$ll = 1;
					$ul = 7;
					$ul = min($ul, $pages);
				}

				$pager = '';

				$ss == '';

				if(isset($_GET['o']) && $_GET['o'] != '')
				{
					$ss = '&amp;o='.$_GET['o'];
					$ss .= ( ($_GET['s']=='asc' || $_GET['s']=='')? '': '&amp;s=desc');
				}

				if($page == 1)
					$pager .= 'First';
				else
					$pager .= '<a href="'.admin_url('admin.php?page=wpadp_list'.$ss.'').'" title="Page '.$i.'">First</a>';
		
				for($i=$ll; $i < $ul; $i++)
				{
					if($i==1)
						{}
					else if($page==$i)
							$pager .= ' ['.$i.'] ';
					else
						$pager .= '<a href="'.admin_url('admin.php?page=wpadp_list'.$ss.'&amp;p='.$i.'').'" title="Page '.$i.'">'.$i.'</a>';
					$pager .= ' | ';
				}

				if($page == $pages)
					$pager .= ' Last';
				else
					$pager .= '<a href="'.admin_url('admin.php?page=wpadp_list'.$ss.'&amp;p='.$pages.'').'" title="Page '.$pages.'">Last</a>';
		
				if($pages == 1) $pager = '-';
				unset($ll, $ul, $ss);
			}
		?>
		<div class="wrap">
		<h2><?php _e( 'WP-AdPunch Lite', 'wpadp_lang' )?></h2>
		<h3><?php _e( 'List Ads' )?></h3>

<?php

if($error)
{
?>
<div class="error fade"><p><b><?php _e('Error: ', 'mof_lang')?></b><?php echo $error;?></p></div>
<?php
}

if($result1)
{
?>
<div id="message" class="updated fade"><p><?php echo $result1; ?></p></div>
<?php
}
?>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">

	<form name="form1" id="form1" method="post" action="<?php echo admin_url('admin.php?page=wpadp_list');?>" onsubmit="return chkbulkform()">
	<?php wp_nonce_field('list-wpadp-campaign'); ?>
<!-- ================== -->
       <div class="tablenav">
        <div class="alignleft actions" style="margin-right:10px">
			<select name="bulk" id="bulkselect" style="vertical-align:middle;max-width:150px"/>
			<option value="noaction" ><?php _e('Bulk Actions', 'wpadp_lang'); ?></option>
			<option value="multidelete"><?php _e('Delete Ad', 'wpadp_lang'); ?></option>
			<option value="multienable"><?php _e('Enable Ad', 'wpadp_lang'); ?></option>
			<option value="multidisable"><?php _e('Disable Ad', 'wpadp_lang'); ?></option>
			</select>
			<input type="submit" value="<?php _e('Apply', 'wpadp_lang'); ?>" class="button-secondary action" />  
		  </div>
		<div class="tablenav-pages">
			<?php 
			echo '<span class="displaying-num">';
			echo str_replace(array("%1$s", "%2$s"), array($page, $pages), __("Page %1$s of %2$s.", 'wpadp_lang'));
			echo '</span><strong>'. $pager .'</strong>'; ?>
		 </div>
		</div>
<!-- ================== -->
	    <table class="widefat" id="wpadp_manage_campaign">
		<thead>
		    	<tr>
					<th scope="col" style="white-space: nowrap; width:20px; padding-left:0;padding-right:0; margin-left:0;">
					<input type="checkbox" style="padding-left:0" /></th>   
					<th style="white-space: nowrap;width:30px; padding-left:0;padding-right:0; margin-left:0;">
<?php
$t = $img = '';
if( $_GET['o']=='id')
{
		if($_GET['s']=='asc' || $_GET['s']=='') {
			$t = '&amp;s=desc';
			$img = _IMGDESC;
		} else if($_GET['s']=='desc') {
			$t = '';
			$img = _IMGASC;
		}
}
?>					<a href="<?php echo admin_url( 'admin.php?page=wpadp_list&amp;o=id'.$t.'' );?>"><?php _e('ID', 'wpadp_lang')?><?php echo $img ?></a>
					</th>
					<th align="center" style="white-space: nowrap;">
<?php
$t = $img = '';
if( $_GET['o']=='title')
{
		if($_GET['s']=='asc' || $_GET['s']=='') {
			$t = '&amp;s=desc';
			$img = _IMGDESC;
		} else if($_GET['s']=='desc') {
			$t = '';
			$img = _IMGASC;
		}
}
?>					<a href="<?php echo admin_url( 'admin.php?page=wpadp_list&amp;o=title'.$t.'' );?>"><?php _e('Title', 'wpadp_lang')?><?php echo $img ?></a>
					</th>
					<th style="white-space: nowrap; padding-left:0;padding-right:0; margin-left:0; width:150px;">
<?php
$t = $img = '';
if( $_GET['o']=='adtype')
{
		if($_GET['s']=='asc' || $_GET['s']=='') {
			$t = '&amp;s=desc';
			$img = _IMGDESC;


		} else if($_GET['s']=='desc') {
			$t = '';
			$img = _IMGASC;
		}
}
?>					<a href="<?php echo admin_url('admin.php?page=wpadp_list&amp;o=adtype'.$t.'');?>"><?php _e('Ad Type', 'wpadp_lang')?><?php echo $img ?></a>
					</th>
					<th style="white-space: nowrap; width:50px">
<?php
$t = $img = '';
if( $_GET['o']=='enabled')
{
		if($_GET['s']=='asc' || $_GET['s']=='') {
			$t = '&amp;s=desc';
			$img = _IMGDESC;
		} else if($_GET['s']=='desc') {
			$t = '';
			$img = _IMGASC;
		}
}
?>					<a href="<?php echo admin_url('admin.php?page=wpadp_list&amp;o=enabled'.$t.'');?>"><?php _e('Enabled', 'wpadp_lang')?><?php echo $img ?></a>
					</th>
					<th style="white-space: nowrap; width:100px;"><?php _e('Global', 'wpadp_lang')?></th>
					<th style="white-space: nowrap; width:20px;"><?php _e('Actions', 'wpadp_lang')?></th>
				</tr>
			</thead>
			<tbody>
			<?php

			$globalad_exists = array();
			if( $results ){
				$i = 1;
				foreach( $results as $result )
				{
					$result->settings = maybe_unserialize( $result->settings );

					if( $result->adtype == 'bar' || $result->adtype == 'slide' )
						$pos = ( in_array( $result->settings['wpadp_pos'], array( 'topf', 'topfl' ) )? 'top': 'bottom' );

					//lite//
					if( $pos != 'top' || $result->adtype != 'bar' ) continue;

					if( !isset( $globalad_exists[$pos] ) )
						$globalad_exists[$pos] = 0;

					if( $result->globalad != '' )
						$globalad_exists[$pos]++;

					$i++;
					$j = '';
					if($i%2 != 0 )
						$j = ' class="alternate"';if($i>6)break;
			 ?>
			<!-- ========================== campaigns ========================== -->
		    	<tr valign="top" id="tr-<?php echo $result->id; ?>" <?php echo $j; ?>>
					<td scope="col" style="white-space: nowrap;"><input type="checkbox" name="check_select[<?php echo $result->id?>]" value="on"/> </td> 
					<td scope="col"><?php echo $result->id; ?></td>
					<td><?php echo $result->title;?></td>
					<td><?php echo ucfirst( $result->adtype ).' [ '.ucfirst($pos).' ]';?></td>
					<td><?php 
						if ($result->enabled == 1 )
							_e("Yes", 'wpadp_lang');
						else
							echo '<font color="red">'.__( "No", 'wpadp_lang' ).'</font>';
					?></td>
					<td><?php 
							if( $result->globalad != '' ) {
								_e("Yes", 'wpadp_lang');
							} else {
								echo '<a href="'.admin_url('admin.php?page=wpadp_list&call2=global&adtype='.$result->adtype.'-'.$pos.'&id='.$result->id ) .'" title="'.__('Edit', 'wpadp_lang').'">'.__('Set as Global','wpadp_lang').'</a>';	
							}
					?></td>
					<td><a href="<?php echo admin_url('admin.php?page=wpadp_add'.$result->adtype.'&call2=edit&id='.$result->id.'')?>" title="<?php _e('Edit', 'wpadp_lang')?>"><?php echo _IMGEDIT;?></a> &nbsp; &nbsp;
					<a href="<?php echo admin_url('admin.php?page=wpadp_list&call2=delete&id='.$result->id.'')?>" onclick="javascript:return doDelete('<?php echo $result->id?>')" title="<?php _e('Delete', 'wpadp_lang')?>"><?php echo _IMGDEL;?></a></td>	
			</tr>
		<?php
				}
			}
			else
			{
				?>
			    	<tr>
					<td colspan="8">&nbsp;~ No Ads ~</td>
			</tr>
				<?php
			}

			if($pager) {
		?>
			<tr><td colspan="8"><?php echo $pager ?></td></tr>
		<?php } ?>
			</tbody>
			</table>
		<?php 
		$str = array();//__('Error: ', 'wpadp_lang');
		foreach($globalad_exists as $modl => $gl )
		{
			if( $gl == 0 )
				$str[] = $modl;
		}
		if( ! empty( $str ) )
		{
			$m = ( count( $str ) > 1 ? __('Modules','wpadp_lang'): __('Module','wpadp_lang') );
			$h = ( count( $str ) > 1 ? __('have','wpadp_lang'): __('has','wpadp_lang') );
			$str = implode(', ', $str );
			$str = ucwords( $str );
?>
<div class="error fade"><p><b><?php _e('Error: ', 'mof_lang')?></b><?php printf( __('There is no Global Ad set for <strong>%s</strong> position of the site.<br/><br/>
Ads would NOT be shown on your site unless you have set them as Global ad or chosen an Ad via Add/ Edit Post panel.','wpadp_lang'), $str );?></p></div>
<?php
		}
		?>
			</form>

	  <hr class="clear" />
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">
		  <?php $this->wpadp_admin_side(); ?>
	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->



		</div><!-- /wrap --><br/>
	<?php

		wp_adpunch_lite::wpadp_page_footer();
	} 

	/*
	*
	**/
	function wpadp_editcss()
	{
		global $wpdb, $current_user, $wpadp_message;
		get_currentuserinfo();

		if (!current_user_can('manage_options')) wp_die(__('Sorry, but you have no permissions to change settings'));

		$real_file = WPADPL_DIR . 'css/common.css';
		if(isset($_POST['call']) && $_POST['call'] == "save")
		{ 
			check_admin_referer('update_css');

			$wpadp_customcss 					= (isset($_POST['newcontent'])? 	trim( stripslashes( $_POST['newcontent'] ) )	: '' );
			if( ! empty( $wpadp_customcss ) )
			{
				$wpadp_customcss = $wpadp_message->esc_html( $wpadp_message->unicode_escape_sequences( utf8_encode( $wpadp_customcss ) ) );
				$wpadp_customcss = @html_entity_decode( $wpadp_customcss, ENT_QUOTES, get_option( 'blog_charset' ) );
			}

			if(
				 ( file_exists($real_file) && is_writeable($real_file) ) ||
				 ( file_exists( dirname( $real_file ) ) && is_writeable( dirname( $real_file ) ) )
				) {
				$f = fopen($real_file, 'w+');
				fwrite($f, $wpadp_customcss);
				fclose($f);

				$updated = 1;
			}
		}
		$content = file_get_contents( $real_file );
		if( empty( $content ) )
			$content = '/* @WP-AdPunch brought to you by MSolution - http://wpadpunch.com */'."\n\n";
	?>
		<div class="wrap">
		<h2><?php _e('WP-AdPunch', 'wpadp_lang')?></h2>
		<h3><?php _e('Edit CSS')?></h3>
		<?php
		if($updated == 1)
		{
		?>
		<div id="message" class="updated fade"><p><?php _e('File Updated', 'wpadp_lang');?></p></div>
		<?php
		}
		?>
		<form method="post" action="">
		<input type="hidden" name="call" value="save" />
		<?php wp_nonce_field('update_css'); ?>
			<table class="form-table">
			<tbody>
				<tr valign="top">
			<div><textarea cols="70" rows="15" name="newcontent" id="newcontent" tabindex="1" style="width:90%; max-width:800px;"><?php echo $content ?></textarea></div>
				</tr>
			</tbody>
			</table>
		<?php 
			if(
				 ( file_exists($real_file) && is_writeable($real_file) ) ||
				 ( file_exists( dirname( $real_file ) ) && is_writeable( dirname( $real_file ) ) )
				) : ?>
			<p class="submit">
			<input type="submit" class="button button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		<?php else : ?>
			<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
		<?php endif; ?>
		</form>
		</div>
	<br class="clear" />
	<?php
		wp_adpunch_lite::wpadp_page_footer();
	}
}
endif;

global $wp_adpunch_admin;
if( ! $wp_adpunch_admin ) $wp_adpunch_admin = new wp_adpunch_admin();