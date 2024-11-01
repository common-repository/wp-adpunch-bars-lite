<?php
/*
 * @ class - wpadp_bar
 *
 **/

if(!class_exists( 'wpadp_bar' )):
class wpadp_bar extends wp_adpunch_head
{
	var $ads2show;
	var $theme_height;

	/*
	*
	**/
	function __construct()
	{
		$this->ads2show = array();
		$this->theme_height = 0;

		add_action( 'template_redirect'		, array( &$this, 'wpadp_bar_init'		)); // lets cook//
		add_action( 'wpadp_stylescripts'		, array( &$this, 'wpadp_stylescripts'	), 11 ); // lets serveCSS // attach to main CSS axn//
		add_action( 'wp_footer'				, array( &$this, 'wpadp_showads' 		)); // lets serveADS
		add_action( 'after_setup_theme'		, array( &$this, 'wpadp_theme_setup' 	)); // admin bar CSS

		add_action( 'admin_head'			, array( &$this, 'wpadp_admin_header'	));

		add_action( 'wp_ajax_wpadp_addsocial'	, array( &$this, 'wpadp_addsocial'		));
		add_action( 'wp_ajax_wpadp_removesocial'	, array( &$this, 'wpadp_removesocial'	));
	}

	/*
	 * ============== front end ==============
	**/
	/*
	*
	**/
	function wpadp_stylescripts( $script_type )
	{
		global $wpdb, $wpadp_message;

		if( is_admin() || is_feed() )
			return;

		if( $script_type == 'css' )
		{
			if( ! empty( $_GET['topad'] ) )
			{
				$topad = (int) trim( strip_tags( $_GET['topad'] ) );
				if( $topad > 0 )
					$topad = $wpdb->get_row("SELECT * FROM `".WPADPL_ADS_TABLE."` WHERE id = ". (int)$topad ." AND enabled=1", ARRAY_A );

				$pos = $bpos = $posb = $bposb = '';
				if( ! empty( $topad ) )
				{
					$topad['settings'] = maybe_unserialize( $topad['settings'] );

					$topad['settings']['wpadp_height']  = ( $topad['settings']['wpadp_height'] < 32 ? 32 : $topad['settings']['wpadp_height'] );

					if( $topad['settings']['wpadp_pos'] == 'topf' ){
						$pos = 'position:fixed;top:0;left:0;width:100%;';
						$bpos = 'position:fixed;top:0;'.$topad['settings']['wpadp_bpos'].':30px; width:auto; height: auto;';
					}else if( $topad['settings']['wpadp_pos'] == 'topfl' ){
						$pos = 'position:absolute;top:0;left:0;width:100%;';
						$bpos = 'position:absolute;top:0;'.$topad['settings']['wpadp_bpos'].':30px; width:auto; height: auto;';
					}

//--------------------- TOP CSS --------------------- //
?>
/* ---- WP-AdPunch brought to you by MSolution - http://wpadpunch.com  ---- */
/* @ top ad */
* html #wpadpbartop{overflow:hidden!important;}
#wpadpbarbartop{ width:100%; overflow:hidden;}
#wpadpbartop *{position:static;text-transform:none;letter-spacing:normal; line-height:1;}

#wpadpbartop{ margin:0; padding:0; <?php echo $pos;?>; display: block; min-height:32px; height:<?php echo $topad['settings']['wpadp_height'];?>px; color: <?php echo $topad['settings']['wpadp_txtc'];?>;}

#wpadpbartop a, #wpadpbartop a:hover,#wpadpbartop a img,#wpadpbartop a img:hover
{color: <?php echo $topad['settings']['wpadp_txtc'];?>;outline:none;border:none;text-decoration:none;background:none;}

#wpadpbartop a img{vertical-align:bottom;}
#wpadpbartop em, #wpadpbartop i{ font-style:italic;}

#wpadpbartop{direction:ltr; min-height:32px; height:<?php echo $topad['settings']['wpadp_height'];?>px; min-width: 600px; width:100%;z-index:99991;background-color:<?php echo $topad['settings']['wpadp_bgc'];?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);background-image:-moz-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);
	background-image:-o-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $topad['settings']['wpadp_bdc'];?>),to(<?php echo $topad['settings']['wpadp_bgc'];?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);background-image:linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);}

#wpadp_socialtop{ margin:0 10px; padding:0; list-style-type: none; float:left; overflow:hidden;}
#wpadp_socialtop li {margin:0; padding:0 5px 0 0; float:left;line-height:<?php echo $topad['settings']['wpadp_height'];?>px;}
#wpadp_socialtop li a{margin:0; padding:0;}
#wpadp_socialtop li#wpadp_socialheadtop{ margin:0; padding:0 10px 0 0; }
#wpadp_socialtop img.wpadp_socialicon{margin:0;width:21px;vertical-align:middle;}

#wpadp_messagestop{ margin:0 0 0 10px; min-width:80%; width:auto; overflow:hidden; display:inline-block; float:left; position:absolute; top:0;}

.wpadp_tickerbartop{margin:0; padding:0; display: block; float:left; text-align:center; white-space:nowrap; overflow:hidden;
	line-height:<?php echo $topad['settings']['wpadp_height'];?>px !important; min-height:32px; height:<?php echo $topad['settings']['wpadp_height'];?>px; width:80%;
	font-family: inherit; font-size: 100%; font-style: inherit; font-weight: inherit; outline: 0px none; vertical-align: baseline;}

#wpadphandlebartop{ <?php echo $bpos;?>; text-align:center; padding:5px 15px; font-family: inherit; font-size: 100%; font-style: inherit; font-weight: bold; 
	outline: 0px none; color:<?php echo $topad['settings']['wpadp_txtc'];?>; z-index:99994; background-color:<?php echo $topad['settings']['wpadp_bgc'];?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px); background-image:-moz-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px);
	background-image:-o-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px); background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $topad['settings']['wpadp_bdc'];?>),to(<?php echo $topad['settings']['wpadp_bgc'];?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px); background-image:linear-gradient(bottom,<?php echo $topad['settings']['wpadp_bdc'];?>,<?php echo $topad['settings']['wpadp_bgc'];?> 5px); 
	border-bottom-right-radius: 6px; -moz-border-radius-bottomright: 6px; -webkit-border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; -moz-border-radius-bottomleft: 6px; -webkit-border-bottom-left-radius: 6px; }

#wpadphandlebartop a{outline:none;border:none;text-decoration:none;background:none;font-family: inherit; font-size: 100%; font-style: inherit; font-weight: bold; 
	outline: 0px none; color:<?php echo $topad['settings']['wpadp_txtc'];?>;}
#wpadphandlebartop a:hover{text-decoration:underline;}

body.admin-bar #wpadpbartop { top:<?php echo WPADPL_ADMINBAR_HEIGHT;?>px; }
body.admin-bar #wpadphandlebartop { top:<?php echo WPADPL_ADMINBAR_HEIGHT;?>px; }

#wpadp_closetop{ margin:0 0 0 10px; padding:0 30px 0 10px;position:absolute; top:5px; right:0; z-index:9999991;background-color:<?php echo $topad['settings']['wpadp_bgc'];?>;}
#wpadp_closetop a{ font:bold 12px/16px sans-serif;color:<?php echo $topad['settings']['wpadp_txtc'];?>;}
#wpadp_closetop a:hover{text-decoration:underline;color:<?php echo $topad['settings']['wpadp_txtc'];?>;}
#wpadplogotop{ margin-left:10px; float:left; height:26px; width:92px;}
#wpadplogotop a{ display:block; height:26px; width:92px;}
#wpadpbartop .wpadp_inline_block{ margin: 0 10px;padding:0; display:inline-block; width:auto;line-height:<?php echo $topad['settings']['wpadp_height'];?>px; }
@media screen and (max-width: 782px) { 
	#wpadpbartop{ min-height:60px; height:<?php echo ( $topad['settings']['wpadp_height']*2 );?>px; min-width: 300px;}
	body.admin-bar #wpadpbartop { top:<?php echo WPADPL_ADMINBAR_HEIGHT2;?>px; }
	body.admin-bar #wpadphandlebartop{  top:<?php echo WPADPL_ADMINBAR_HEIGHT2;?>px; }
	#wpadp_socialtop{ display:block; clear:right;}
	#wpadp_messagestop{ margin-left:10px; width:95%; display:block; float:none;top:<?php echo $topad['settings']['wpadp_height'];?>px;}
	.wpadp_tickerbartop{text-align:left; height:<?php echo $topad['settings']['wpadp_height'];?>px;}
}
@media screen and (max-width: 600px) { 
	#wpadpbartop{   height:<?php echo ( $topad['settings']['wpadp_height']*3); ?>px; position:absolute;}
	#wpadp_socialtop{ display:block ;float:none;}
	#wpadp_socialheadtop { display:none }
	#wpadp_messagestop{ overflow:visible;}
	.wpadp_tickerbartop{text-align:left; white-space:normal; overflow:visible; line-height:1.5!important;}
}
<?php
if( ! empty( $topad['settings']['wpadp_customcss'] ) )
{
	echo html_entity_decode( $topad['settings']['wpadp_customcss'], ENT_QUOTES | ENT_SUBSTITUTE, get_option('blog_charset'));
	echo "\n";
}
echo "/* --------------------- /TOP CSS --------------------- */ \n";

//--------------------- /TOP CSS --------------------- //
				}
			}
		}

		if( $script_type == 'js' )
		{
			$img = 'data:image/png;base64,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';

			?>
			if( typeof jQuery == "function" ) {
			jQuery(document).ready( function($){
			<?php
			// cook JS to output//
			// -------------------- top ad -------------------- //
			if( ! empty( $_GET['topad'] ) )
			{
			?>
				if( $("#wpadpbartop").length > 0 ){
					$( "body" ).prepend( $("#wpadphandlebartop") );
					$( "body" ).prepend( $("#wpadpbartop") );
				}

				$("#wpadplogotop").append('<a href="http://wpadpunch.com/" title="WP-AdPunch - Powerful Notification Bar for your website." target="_blank"></a>');
				$("#wpadplogotop a").append('<img src="<?php echo $img;?>" border="0"/>');
				$("#wpadphandlebartop").click( function() {
					if( $("#wpadpbartop").is(":hidden") ) {
						$("#wpadpbartop").slideDown('slow');
						$("#wpadpbartop *").css('display', '');

						$(".wpadp_tickerbartop").css('display', 'none');
						$("#wpadp_tickerbartop-0").css('display', ''); 
						$("#wpadphandlebartop").slideUp('slow');
						wpadp_createCookie('wpadptop', 'show');

					} else {
						$("#wpadpbartop").slideUp();
						$("#wpadpbartop *").css('display', 'none');
						$(".wpadp_tickerbartop").css('display', 'none');
						$("#wpadphandlebartop").slideDown('slow');
						wpadp_createCookie('wpadptop', 'hide');
					}
				});

				$("#wpadp_closetop").click( function() {
					$("#wpadphandlebartop").click();
				});
			/* ----------- */
			<?php
			}
			?>
			/* ----------- */
				;function wpadp_createCookie(key,value) {
					var date = new Date();
					date.setTime(date.getTime()+(1*24*60*60*1000));
					var expires = " expires="+date.toGMTString();
					document.cookie = key+"="+value+";"+expires+"; path=/";
				}

				;function wpadp_getCookie( key )
				{
					var i,x,y,cookies = document.cookie.split(";");
					for (i=0;i<cookies.length;i++)
					{
						x=cookies[i].substr(0,cookies[i].indexOf("="));
						x=x.replace(/^\s+|\s+$/g,"");
						if (x==key) {
							return unescape( cookies[i].substr(cookies[i].indexOf("=")+1) );
						}
					}
				};
			});
			}
		<?php
			/* ----------- ----------- ----------- */
		}
	}

	/*
	*
	**/
	function wpadp_theme_setup()
	{
		if( ! is_admin_bar_showing() )
		{
			add_action('wp_head', array( &$this, 'wpadp_adminbar_css' ) );
		}
		else if (function_exists('is_admin_bar_showing') && function_exists('add_theme_support'))
		{
			add_theme_support( 'admin-bar', array( 'callback' => array( &$this, 'wpadp_adminbar_css' ) ) );
		}
	}

	/*
	*
	**/
	function wpadp_adminbar_css()
	{
		if( empty( $this->theme_height ) )
			$ht = 0;
		else
			$ht = ( $this->theme_height < 32? 32 : $this->theme_height );

		$ht2 = $ht * 2;
		if( is_admin_bar_showing() )
		{
			$ht += WPADP_ADMINBAR_HEIGHT;
			$ht2 += WPADP_ADMINBAR_HEIGHT2;
		} 
		else if( ! isset( $this->theme_height ) || $this->theme_height = 0 )
			return false;
	?>
<style type="text/css">
	html { margin-top: <?php echo $ht;?>px;}
	* html body { margin-top: <?php echo $ht;?>px; }

@media screen and (max-width: 782px) { 
	html { margin-top: <?php echo $ht2;?>px;}
	* html body { margin-top: <?php echo $ht2;?>px; }
}
</style>
	<?php
	}

	/*
	*
	**/
	function wpadp_bar_getcontent( $wpadp_bar )
	{
		global $wpadp_message;
		//$tweets[] = array( 'msg' => @htmlentities( $res->text ), 'cnt' => 0, 'unqid' => md5( $mes ) );
		//$wpadp_bar['settings']['wpadp_showtcount'] = $wpadp_bar['settings']['wpadp_showrcount'] = $wpadp_bar['settings']['wpadp_showpcount']=100;

		if( $wpadp_bar['settings']['wpadp_showm'] == 1 )
		{
			$meta_info = $wpadp_message->get_meta_by_id( $wpadp_bar['settings']['wpadp_mid'] );

			$epxiresat = $meta_info['epxiresat'];
			if( ! empty( $meta_info ) )
				$meta_info = maybe_unserialize( stripslashes( $meta_info['meta_values'] ) );
			if( ! empty( $meta_info ) ){
			foreach( $meta_info as $i => $v )
			{
				$alltext[] 	= array_merge( $meta_info[$i], array( 'mod'=>'msg', 'pid'=>$wpadp_bar['settings']['wpadp_mid'] ) ); //, 'epxiresat'=>$epxiresat ) );
			}}
		}

			$cnt_arr = array();
			foreach( $alltext as $id => $val )
			{
				$cnt_arr[$val['cnt']] = $id;
			}

			ksort( $cnt_arr );
			$cnt_arr = reset( $cnt_arr );

			$alltext = array( 0 => $alltext[$cnt_arr] );

		return $alltext;
	}

	/*
	*
	**/
	function wpadp_bar_getHTML( $wpadp_bar )
	{
		global $wpadp_message;

		$pos = ( in_array( $wpadp_bar['settings']['wpadp_pos'], array( 'topf', 'topfl' ) )? 'top': 'bottom' );

		if( $pos == 'top' )
			$this->theme_height = $wpadp_bar['settings']['wpadp_height'];

		$alltext = $this->wpadp_bar_getcontent( $wpadp_bar );

		$butmessage = $wpadp_bar['settings']['wpadp_butmessage'];
		if( empty( $butmessage ) )
			$butmessage = __('Show Offer','wpadp_lang');

		$butmessage = html_entity_decode( $butmessage, ENT_QUOTES | ENT_SUBSTITUTE, get_option('blog_charset'));
		//echo base64_encode( file_get_contents( WPADPL_URL.'img/lite-logo.png' ) );

		ob_start();
		?>
		<div id="wpadpbar<?php echo $pos;?>" class="wpadpbar<?php echo $pos;?> wpadpbar_<?php echo $wpadp_bar['settings']['wpadp_pos'];?>">
		<!-- 	WP-AdPunch brought to you by MSolution - http://wpadpunch.com  -->
		<div id="wpadpbarbar<?php echo $pos;?>">
			<?php 
			echo '<div id="wpadplogo'.$pos.'"></div>';
			$socials = $wpadp_bar['settings']['wpadp_social'];
			if( ! empty(  $socials ) && count( $socials ) > 0 )
			{
				echo '<ul id="wpadp_social'.$pos.'">'."\n";

				if( ! empty( $wpadp_bar['settings']['wpadp_socialhead'] ) )
					echo '<li id="wpadp_socialhead'.$pos.'">'.$wpadp_bar['settings']['wpadp_socialhead'].': </li>'."\n";

				$wpadp_social_profiles = get_user_meta( $wpadp_bar['user_id'], '_wpadp_social_profiles', true );
				if( ! empty( $wpadp_social_profiles ) ) {
				foreach( $wpadp_social_profiles as $ref )
				{
					if( $socials && in_array( $ref['pname'], $socials ) !== false )
					{
						$alt = str_replace(array('http://','https://','www.'), '',$ref['url']);
						echo '<li><a href="'.$ref['url'].'" target="_blank">';
						echo '<img class="wpadp_socialicon" src="'.$ref['icon'].'" border="0" alt="'.$alt.'" title="'.$alt.'"/></a></li>';
					}
				}}
				echo '</ul>';
			}

			$style = '';
			echo '<div id="wpadp_messages'. $pos.'">'."\n";
			if( ! empty( $alltext ) ) 
			{
			foreach( $alltext as $i => $txt )
			{
				$txt['msg'] = @html_entity_decode( $txt['msg'], ENT_QUOTES | ENT_SUBSTITUTE, get_option( 'blog_charset' ) );
				$txt['link'] = @html_entity_decode( $txt['link'], ENT_QUOTES | ENT_SUBSTITUTE, get_option( 'blog_charset' ) );
				echo '<div id="wpadp_tickerbar'.$pos.'-'.$i.'" class="wpadp_tickerbar'.$pos.'" '.$style.'>';
				if( ! empty( $txt['link'] ) )
					echo '<span><a href="'.$txt['link'].'" title="'.$txt['msg'].'" '.( $txt['mod'] != 'post'? ' target="_blank"':'').'>'.$txt['msg'].'</a></span>';
				else
				{
					// simply content//
					echo $txt['msg'];
				}
				echo '</div>'."\n";

				$style = ' style="display:none" ';
			}}
			echo '</div>';
			echo '<span id="wpadp_close'.$pos.'"><a href="javascript:;">'. __('[X]', 'wpadp_lang').'</a></span>';
		?>
		</div>
		<!-- 	/WP-AdPunch brought to you by MSolution - http://wpadpunch.com  -->
		</div>
		<div id="wpadphandlebar<?php echo $pos ?>" style="display:none"><a href="javascript:;"><?php echo $butmessage?></a></div>

		<?php

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	function wpadp_showads()
	{
		global $wp_query;

		if( is_admin() || is_feed() )
			return;

		if( is_singular() )
		{
			$thePostID = ( $wp_query->post->ID? $wp_query->post->ID: 0 );

			$disabled = get_post_meta( $thePostID, '_wpadp_disableall', true );
			if( $disabled == 'on' )
				return;
		}

		if( ! empty( $this->ads2show['topad'] ) )
		{
			echo $this->ads2show['topad_html'];
		}
		if( ! empty( $this->ads2show['bottomad'] ) )
		{
			echo $this->ads2show['bottomad_html'];
		}
	}

	/*
	 *
	 * init all ads to be shown on this page/
	 *
	**/
	function wpadp_bar_init()
	{
		global $wpdb, $wp_query, $wpadp_message, $current_user;
		get_currentuserinfo();

		if( is_admin() || is_feed() )
			return;

		$thePostID = 0;
		if( is_singular() )
		{
			$thePostID = ( $wp_query->post->ID? $wp_query->post->ID: 0 );

			$disabled = get_post_meta( $thePostID, '_wpadp_disableall', true );
			if( $disabled == 'on' )
				return;

			// disabled // ad assigned to this post // global ( -ignore postpage ) // 
			$topad 	= get_post_meta( $thePostID, '_wpadp_topad', true );
			if( ! empty( $topad ) && $topad != 'none' ) 
			{
				$this->ads2show['topad'] = $wpdb->get_row("SELECT * FROM `".WPADPL_ADS_TABLE."` WHERE id = ". (int)$topad ." AND enabled=1", ARRAY_A );
				if( ! empty( $this->ads2show['topad'] ) )
					$this->ads2show['topad']['settings'] = maybe_unserialize( $this->ads2show['topad']['settings'] );
			}
		}

		//get globals//
		if( empty( $this->ads2show['topad'] ) || empty( $this->ads2show['bottomad'] ) )
		{
			$possibles = array( 'bar-top', 'bar-bottom', 'slide-top', 'slide-bottom' );
			$ads = $wpdb->get_results("SELECT * FROM `".WPADPL_ADS_TABLE."` WHERE `globalad` IN ('". implode( "','", $possibles ) ."') AND enabled=1 ORDER BY adtype", ARRAY_A );

			foreach( $ads as $ad )
			{
				$ad['settings'] = maybe_unserialize( $ad['settings'] );
				$pos = ( in_array( $ad['settings']['wpadp_pos'], array( 'topf', 'topfl' ) )? 'top': 'bottom' );

				if( $thePostID > 0 )
				{
					$ipp = explode( ",", $ad['settings']['wpadp_ipp'] );
					if( in_array( $thePostID, $ipp ) !== false )
						continue;
				}

				if( empty( $this->ads2show['topad'] ) && $pos == 'top' && $topad != 'none' )
					$this->ads2show['topad'] = $ad;
			}
		}

		// create the bar here itself.// so we know what CSS to output//
		if( ! empty( $this->ads2show['topad'] ) )
			$this->ads2show['topad_html'] = $this->wpadp_bar_getHTML( $this->ads2show['topad'] );
			if( empty( $this->ads2show['topad_html'] ) )
			{
				unset( $this->ads2show['topad'] );
				unset( $this->ads2show['topad_html'] );
			}

		add_filter( 'wpadp_stylescript_css', array( &$this, 'wpadp_stylescript_addcss'	));

		return true;
	}

	function wpadp_stylescript_addcss( $css = array() )
	{
		if( ! empty( $this->ads2show['topad'] ) )
			$css[] = 'topad='.$this->ads2show['topad']['id'];

		return $css;
	}

	/*
	* // ============ Admin end ============== //
	**/

	/*
	*
	**/
	function wpadp_admin_header()
	{
		if( is_admin() && strpos( $_GET['page'], 'wpadp' ) !== false )
		{
			wp_enqueue_script( 'common' );
			//wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
			wp_enqueue_script( 'jscolor', WPADPL_URL . 'js/jscolor/jscolor.js' );

		?>
<style type="text/css">
.postbox .handlediv:before {right:12px;font:400 20px/1 dashicons;speak:none;display:inline-block;top:0;position:relative;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;text-decoration:none!important;content:'\f142';padding:8px 10px;}
.postbox.closed .handlediv:before {content: '\f140';}
.wrap h2:before {content: "\f204";display: inline-block;-webkit-font-smoothing: antialiased;font: normal 29px/1 'dashicons';vertical-align: middle;margin-right: 0.3em;}
</style>
	<script type="text/javascript">
		//<![CDATA[
	if( typeof jQuery == "function") {
		jQuery(document).ready( function($) {
			var ajax_nonce 		= '<?php echo wp_create_nonce( 'wpadp_ajax' ); ?>';
			var wpadp_plugin_url 	= '<?php echo WPADPL_URL;?>';
			var site_url 		= '<?php echo site_url(); ?>';
			var ajaxurl 		= '<?php echo admin_url('admin-ajax.php') ?>';

			// close postboxes that should be closed
			$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
			// postboxes setup
			try{
			if(postboxes)
				postboxes.add_postbox_toggles( 'wpadp_options' );
			} catch(e){}
			$("#wpadp_butmessagetable tr td").click(function(){
				$("#wpadp_butmessage").val(this.childNodes[0].innerHTML);
				$.each( $("#wpadp_butmessagetable tr td"), function( k, v ) {
					$(this).removeClass("wpadp_butpress");
				});
				$(this).addClass("wpadp_butpress");
			}); 
			$("#wpadp_butmessage").focus( function(){
				$.each( $("#wpadp_butmessagetable tr td"), function( k, v ) {
					$(this).removeClass("wpadp_butpress");
				});
			});

			htactiveind = htactiveinc = false;
			$(".wpadp_increasebut").mousedown( function(event) {
				event.preventDefault();
				if( htactiveind ) clearInterval( htactiveind );

				idd = $(this).attr("id").split("-")[1];
				idd = "#wpadp_"+idd+"txt";

				minvalue = $(idd).attr("minvalue");
				maxvalue = $(idd).attr("maxvalue");
				hasauto = $(idd).attr("hasauto");

				aa = $(idd).val(); 
				if( aa == "undefined" || aa == "auto" || isNaN( aa ) || aa == "" ) { aa = minvalue; }
				aa = parseInt( aa, 10 );
				if( aa == "undefined" || aa == "auto" || isNaN( aa ) ){ aa = minvalue; }
				if( aa < minvalue ){ aa = minvalue; }
				aa = parseInt( aa, 10 );

				if( aa < maxvalue ) {
					$(idd).val(( ++aa ));
					htactiveinc = setInterval( function(){ if( aa < maxvalue ) { $(idd).val( ( ++aa )) } }, 100);
				}
			} );
			$(".wpadp_increasebut").mouseup( function(event) {
				if( htactiveinc ) clearInterval( htactiveinc );
			});
			$(".wpadp_increasebut").mouseout( function(event) {
				if( htactiveinc ) clearInterval( htactiveinc );
			});

			$(".wpadp_decreasebut").mousedown( function(event) {
				event.preventDefault();

				if( htactiveinc ) clearInterval( htactiveinc );

				idd = $(this).attr("id").split("-")[1];
				idd = "#wpadp_"+idd+"txt";

				minvalue = $(idd).attr("minvalue");
				maxvalue = $(idd).attr("maxvalue");
				hasauto = $(idd).attr("hasauto");

				aa = $(idd).val(); 
				if( aa == "undefined" || aa == "auto" || isNaN( aa ) || aa == ""){ aa = minvalue; }
				aa = parseInt( aa, 10 );
				if( aa == "undefined" || aa == "auto" || isNaN( aa ) ) { aa = minvalue;}

				if( aa > minvalue ) {
					$(idd).val( ( --aa ) );
					htactiveind = setInterval( function(){ if( aa > minvalue ) { $(idd).val( ( --aa )) } else if( aa <= minvalue && hasauto == "yes" ){ $(idd).val('auto');} else {$(idd).val(minvalue);}}, 100 );
				} else if( aa <= minvalue && hasauto == "yes" ) {
					$(idd).val('auto'); 
				} else {
					$(idd).val(minvalue);
				}
			} );
			$(".wpadp_decreasebut").mouseup( function(event) {
				if( htactiveind ) clearInterval( htactiveind );
			});
			$(".wpadp_decreasebut").mouseout( function(event) {
				if( htactiveind ) clearInterval( htactiveind );
			});
			$("#wpadp_showm").click(function(){
				checkd = $(this).is(":checked");
				if( checkd ) 
					$("#wpadp_showm_2").css("display", "");
				else
					$("#wpadp_showm_2").css("display", "none");
			});
			$("#wpadp_showt, #wpadp_showr, #wpadp_showp").click(function(){
				$(this).prop( "checked", false );
				alert('Please upgrade to use this feature.');
			});
			$("#wpadp_messageadd").click( function(){
				alert('Please upgrade to use this feature.');
			});
			$("#wpadp_pos2, #wpadp_pos3, #wpadp_pos4" ).click( function(){
				$("#wpadp_pos1").prop( "checked", true );
				alert('Please upgrade to use this feature.');
			});
			$("#wpadp_disp2" ).click( function(){
				$("#wpadp_disp1").prop( "checked", true );
				alert('Please upgrade to use this feature.');
			});
			/* ================================================== */
		var addcampid = 0;
		$("#wpadp_add_social").click( function() {
			$("#wpadp_add_social_div").fadeIn(300);
			var popMargTop = ($("#wpadp_add_social_div").height() + 140) / 2; 
			var popMargLeft = ($("#wpadp_add_social_div").width() + 24) / 2; 
			$("#wpadp_add_social_div").css({ 'margin-top' : -popMargTop, 'margin-left' : -popMargLeft });
			$('body').append('<div id="mask"></div>');
			$('#mask').fadeIn(300);

			$("#wpadp_socialname").val('');
			$("#wpadp_socialicon").val('');
			$("#wpadp_socialurl").val('');
			$("#wpadp_saddajax").hide();
			$('#wpadp_sresult').html('');
			$('#wpadp_serr').html( '' );

			return false;
		});

		/* When clicking on the button close or the mask layer the popup closed */
		$('a#wpadp_sclose, #mask').live('click', function() { 
			$('#mask , #wpadp_add_social_div').fadeOut(300 , function() {
				$('#mask').remove();  
			}); 
			return false;
		});

		$("#wpadp_add_social_form").submit( function(event){
			event.preventDefault();

			if( $("#wpadp_socialname").val() == '' ) {
				$('#wpadp_serr').html('Please enter a name for the profile..');
				$("#wpadp_socialname").focus();
				return false;
			}

			if( $("#wpadp_socialicon").val() == '' ) {
				$('#wpadp_serr').html('Please enter a short URL..');
				$("#wpadp_socialicon").focus();
				return false;
			}
			if( $("#wpadp_socialurl").val() == '' ) {
				$('#wpadp_serr').html('Please enter the destination URL..');
				$("#wpadp_socialurl").focus();
				return false;
			}
			var data = {
				action	: 'wpadp_addsocial',
				socialname	: $("#wpadp_socialname").val(),
				socialicon	: $("#wpadp_socialicon").val(),
				socialurl	: $("#wpadp_socialurl").val(),
				_ajax_nonce	: ajax_nonce
			};

			$("#wpadp_saddajax").show();
			$('#wpadp_sresult').html('');
			$('#wpadp_serr').html( '' );
			var myajax = jQuery.post( ajaxurl, data, function(response) {
				$("#wpadp_saddajax").hide();
				if( response != '-1' && ! response.err )
				{
					$('#wpadp_sresult').html(response.msg);
					$('#wpadp_serr').html('');
					$('#social_profiles').html( response.table );
					$("#wpadp_socialname").val('');
					$("#wpadp_socialicon").val('');
					$("#wpadp_socialurl").val('');
					window.setTimeout( function(){
						$('#wpadp_sresult').html('');
						$('#wpadp_serr').html('');
						$("#mask").click();
					}, 2000 );
				}
				else
				{
					$('#wpadp_sresult').html('');
					$('#wpadp_serr').html('Error<!-- code: '+response.err+' -->: '+ response.msg );
				}
			}, "json");
			$(window).unload( function() { myajax.abort(); } );
		});
		/* ================================================== */
		stillthere = false;
		mytime = null;

		function wpadp_removefloat( camp_id )
		{
			mytime = window.setTimeout( function(){ 
				if( stillthere == false ){
					$("#wpadp_add_share_div").removeClass('wpadp_add_sharefloat');
				}
				else
				{
					window.clearTimeout( mytime );
					wpadp_removefloat( camp_id );
				}
			}, 1000 );
		}

		$("#wpadp_add_share").click( function(){
			$("#wpadp_add_share_div").addClass('wpadp_add_sharefloat');
			stillthere = true;
			wpadp_removefloat();
		});

		$("#wpadp_add_share_div").mouseleave( function(){
			stillthere = false;
		});

		$("#wpadp_add_share_div").mouseenter( function(){
			stillthere = true;
		});

		$("#wpadp_add_share").mouseleave( function(){
			stillthere = false;
		});
		$("#wpadp_add_share_div a").click( function() {
			$("#wpadp_socialicon").val( $(this).find('img').attr('src') );
			$("#wpadp_add_share_div").removeClass('wpadp_add_sharefloat');
		});
		$(".wpadp_socialremove").live( 'click',  function(){
			id = $(this).attr("id").split("-")[1];

			xx = confirm("<?php _e("Are you sure you wish to delete this.\\nThere is no undo, click 'Cancel' to stop",'wpadp_lang');?>");
			if( xx == false )
				return false;

			var data = {
				action	: 'wpadp_removesocial',
				socialname	: id,
				_ajax_nonce	: ajax_nonce
			};

			$("#wpadp_sremoveajax").show();
			$('#wpadp_srresult').html('');
			$('#wpadp_srerr').html( '' );
			var myajax = jQuery.post( ajaxurl, data, function(response) {
				$("#wpadp_sremoveajax").hide();
				if( response != '-1' && ! response.err )
				{
					$('#wpadp_srresult').html(response.msg);
					$('#wpadp_srerr').html('');
					$('#social_profiles').html( response.table );
					window.setTimeout( function(){
						$('#wpadp_srresult').html('');
						$('#wpadp_srerr').html('');
					}, 2000 );
				}
				else
				{
					$('#wpadp_srresult').html('');
					$('#wpadp_srerr').html('Error<!-- code: '+response.err+' -->: '+ response.msg );
				}
			}, "json");
			$(window).unload( function() { myajax.abort(); } );
		});

		});
		}
		//]]>
	</script>
		<?php 
		}
	}

	/*
	*
	**/
	function wpadp_addsocial()
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		if(!is_user_logged_in())  
		{
			$out = array();
			$out['msg'] = __('User not logged in','wpadp_lang');
			$out['err'] = __LINE__;
			header( "Content-Type: application/json" );
			echo json_encode( $out );
			die();
		}
		check_ajax_referer( "wpadp_ajax" );

		if(!defined('DOING_AJAX')) define('DOING_AJAX', 1);
		set_time_limit(60);

		$social = array();
		$social['pname']	= trim( esc_attr( strip_tags( $_POST['socialname'] ) ) );
		$social['icon']	= trim( esc_attr( strip_tags( $_POST['socialicon'] ) ) );
		$social['url']	= trim( esc_attr( strip_tags( $_POST['socialurl'] ) ) );

		$social['icon'] = $this->is_valid_img( $social['icon'] );
		if( ! $social['icon'] )
		{
			$out = array();
			$out['msg'] = sprintf( __('Please Enter a valid Icon image URL<!-- %s -->','wpadp_lang'), $social['icon'] );
			$out['err'] = __LINE__;
			header( "Content-Type: application/json" );
			echo json_encode( $out );
			die();
		}

		$social['url'] = $this->is_valid_url( $social['url'] );
		if( ! $social['url'] )
		{
			$out = array();
			$out['msg'] = sprintf( __('Please Enter a valid URL<!-- %s -->','wpadp_lang'), $social['url'] );
			$out['err'] = __LINE__;
			header( "Content-Type: application/json" );
			echo json_encode( $out );
			die();
		}
		$wpadp_social_profiles = get_user_meta( $current_user->ID, '_wpadp_social_profiles', true );
		$wpadp_social_profiles[] = $social;
		$newarr = array();
		foreach( $wpadp_social_profiles as $ref )
		{
			if( trim( $ref['pname'] ) == "" ) continue;

			$newarr[] = $ref;
		}

		$wpadp_social_profiles = $newarr;

		update_user_meta( $current_user->ID, '_wpadp_social_profiles', $wpadp_social_profiles );

		$out = array();
		$out['msg'] = '.';
		$out['table'] = $this->wpadp_social_table();
		header( "Content-Type: application/json" );
		echo json_encode( $out );
		die();
	}

	/*
	*
	**/
	function wpadp_removesocial()
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		if(!is_user_logged_in())  
		{
			$out = array();
			$out['msg'] = __('User not logged in','wpadp_lang');
			$out['err'] = __LINE__;
			header( "Content-Type: application/json" );
			echo json_encode( $out );
			die();
		}
		check_ajax_referer( "wpadp_ajax" );

		if(!defined('DOING_AJAX')) define('DOING_AJAX', 1);
		set_time_limit(60);

		$pname	= trim( esc_attr( strip_tags( $_POST['socialname'] ) ) );

		$wpadp_social_profiles = get_user_meta( $current_user->ID, '_wpadp_social_profiles', true );

		$newarr = array();
		foreach( $wpadp_social_profiles as $ref )
		{
			if( strcmp( strtolower( $ref['pname'] ), strtolower( $pname ) ) == 0 ) continue;
			if( trim( $ref['pname'] ) == "" ) continue;

			$newarr[] = $ref;
		}

		$wpadp_social_profiles = $newarr;
		update_user_meta( $current_user->ID, '_wpadp_social_profiles', $wpadp_social_profiles );

		$out = array();
		$out['msg'] = '.';
		$out['table'] = $this->wpadp_social_table();
		header( "Content-Type: application/json" );
		echo json_encode( $out );
		die();
	}

	/*
	*
	**/
	function wpadp_social_table( $obj = array() )
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		ob_start();
		?>
		 <table class="widefat">
		 <thead>
		 <tr>
			<th scope="row" style="width:50px!important;"><strong>#</strong></th>
			<th style="width:100px!important;"><strong><?php _e( 'Name', 'wpadp_lang' ); ?></strong></th>
			<th style="width:350px!important;"><strong><?php _e( 'Profile', 'wpadp_lang' ); ?></strong></th>
			<th style="width:50px!important;"><strong>#</strong></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$wpadp_social_profiles = get_user_meta( $current_user->ID, '_wpadp_social_profiles', true );
		if( ! empty( $wpadp_social_profiles ) )
		{
		foreach( $wpadp_social_profiles as $ref )
		{
			$checked = '';
			if( $obj && in_array( $ref['pname'], $obj ) !== false )
				$checked = " checked='checked'";
		?>
		<tr>
			<td scope="row"><input type="checkbox" name="wpadp_social[]" value="<?php echo $ref['pname']; ?>" <?php echo $checked; ?>/></td>
			<td><?php echo $ref['pname']; ?></td>
			<td><a href="<?php echo $ref['url']; ?>" target="_blank"><img src="<?php echo $ref['icon'];?>" border="0" width="24"/>
				 <span style="line-height:2;font-weight:bold;vertical-align:top;"><?php echo str_replace( array('https://', 'http://', 'www.'), '', $ref['url'] ); ?></span></a></td>
			<td><a href="javascript:;" id="wpadp_socialremove-<?php echo $ref['pname']; ?>" class="wpadp_socialremove">
					<img src="<?php echo WPADPL_URL;?>/img/delete.gif" alt="delete"/></a></td>
		</tr>
		<?php
		} }
		else
		{
		?>
		<tr>
			<th colspan="4"><?php _e('~~ No Social Profiles for this user. ~~ ','wpadp_lang'); ?></th>
		</tr>
		<?php
		}
		?>
		</tbody>
	  </table>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/*
	* 
	**/
	function wpadp_addbar()
	{
		global $wpdb, $current_user, $wpadp_message;
		get_currentuserinfo();

		if (!current_user_can( 'manage_options' )) wp_die(__( 'Sorry, but you have no permissions to change settings.' ));

		$form 	= 'add';
		$bar_id 	= 0; 
		$id = 0;

		$adtype	= 'bar';
		$wpadp_bar				= array();

		if(isset($_REQUEST['call2']) && trim( $_REQUEST['call2'] ) == 'edit' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
		{
			$id = (int) trim( strip_tags( $_REQUEST['id'] ) );
			if( $id > 0 )
			{
				$form = 'edit';
				$wpadp_bar = $wpdb->get_row("SELECT * FROM `".WPADPL_ADS_TABLE."` WHERE `id` = ".$id." ", ARRAY_A );//userid
				if( ! empty( $wpadp_bar ) )
				{
					$wpadp_bar['settings'] = maybe_unserialize( $wpadp_bar['settings'] );

					$wpadp_meta					= array();
					// messages
					if( $wpadp_bar['settings']['wpadp_showm'] == 1 )
					{
						$meta_info = $wpadp_message->get_meta_by_id( $wpadp_bar['settings']['wpadp_mid'] );
						if( ! empty( $meta_info ) )
							$meta_info = maybe_unserialize( stripslashes( $meta_info['meta_values'] ) );

						$wpadp_bar['settings']['wpadp_message'] = array();
						if( ! empty( $meta_info ) ){
						foreach( $meta_info as $i => $v )
						{
							$v = @html_entity_decode( $v['msg'], ENT_QUOTES | ENT_SUBSTITUTE, get_option( 'blog_charset' ) );
							$wpadp_bar['settings']['wpadp_message'][] 	= trim( stripslashes( $v ) );
						}}
					}
					// twitter
					// only edit last (int)x tweets
					// RSS
					// only show url & number
					// POSTS
					// only show whose and number
				}
			}
		}

		if( $id == 0 && isset( $_POST['call'] ) && $_POST['call'] == "save" )
		{
			$wpadp_bar_cnt = $wpdb->get_var("SELECT count( * ) as cnt FROM `".WPADPL_ADS_TABLE."` WHERE 1" );//userid
			if( $wpadp_bar_cnt >= 5 )
			{
				$_POST['call'] = "save_not";
				$error = sprintf(__('You can only create 5 Ads with the Lite version, Please <a href="http://wpadpunch.com/" target ="_blank"><b>upgrade to enjoy ALL features.</b></a>.<!-- %s -->','wpadp_lang'), $wpadp_bar_cnt );
			}
		}

		if( isset( $_POST['call'] ) && $_POST['call'] == "save" )
		{ 
			check_admin_referer( 'add-wpadp' );

			$wpadp_bar				= array();

			$wpadp_bar['title']		= (isset($_POST['wpadp_name'])? 		esc_html( trim( stripslashes( $_POST['wpadp_name'] ) ) )	: 'Campaign Name' );
			$wpadp_bar['user_id']		= $current_user->ID;
			$wpadp_bar['showon']		= '';
			$wpadp_bar['adtype']		= $adtype;
			$wpadp_bar['globalad']		= (isset($_POST['wpadp_global'])?  		1 : 0 );
			$wpadp_bar['enabled']		= (isset($_POST['wpadp_enable'])? 	 	1 : 0 );

			$wpadp_bar['settings']['wpadp_ipp'] 		= (isset($_POST['wpadp_ipp'])? 		esc_html( trim( stripslashes( $_POST['wpadp_ipp'] ) ) )	: '' );

			if( ! empty( $wpadp_bar['settings']['wpadp_ipp'] ) )
			{
				$ipp = array();
				$the_slug = explode(",", $wpadp_bar['settings']['wpadp_ipp'] );

				foreach( $the_slug as $slug )
				{
					$slug = trim( $slug );
					if( ! is_numeric( $slug ) )
					{
						$args=array(
						  'name' => $slug,
						  'post_type' => 'post',
						  'post_status' => 'publish',
						  'showposts' => 1
						);
						$my_posts = get_posts($args);
						if( empty( $my_posts ) )
						{
							$args=array(
							  'name' => $slug,
							  'post_type' => 'page',
							  'post_status' => 'publish',
							  'showposts' => 1
							);
							$my_posts = get_posts($args);
						}
						if( ! empty( $my_posts ) )
							$ipp[] = $my_posts[0]->ID;
					}
					else
						$ipp[] = $slug;
				}
				$wpadp_bar['settings']['wpadp_ipp'] 	= implode( ",", $ipp );
			}

			$wpadp_bar['settings']['wpadp_bgc'] 		= (isset($_POST['wpadp_bgc'])? 		'#'.esc_attr( trim( stripslashes( $_POST['wpadp_bgc'] ), '#' ) )	: '' );
			$wpadp_bar['settings']['wpadp_bdc'] 		= (isset($_POST['wpadp_bdc'])? 		'#'.esc_attr( trim( stripslashes( $_POST['wpadp_bdc'] ), '#' ) )	: '' );
			$wpadp_bar['settings']['wpadp_txtc'] 		= (isset($_POST['wpadp_txtc'])? 		'#'.esc_attr( trim( stripslashes( $_POST['wpadp_txtc']), '#' ) )	: '' );
			$wpadp_bar['settings']['wpadp_height'] 		= (isset($_POST['wpadp_height'])? 		(int)esc_attr( trim( stripslashes( $_POST['wpadp_height'] ) ) )	: '32' );

			$wpadp_bar['settings']['wpadp_pos'] 		= (isset($_POST['wpadp_pos'])? 		esc_html( trim( stripslashes( $_POST['wpadp_pos'] ) ) )	: '' );
			$wpadp_bar['settings']['wpadp_pos'] 		= (in_array( $wpadp_bar['settings']['wpadp_pos'], array( 'topf', 'topfl', 'bottomf', 'bottomfl' ) )? $wpadp_bar['settings']['wpadp_pos']: 'topf' );
			$wpadp_bar['settings']['wpadp_pos'] 		= 'topf';

			if( $wpadp_bar['globalad'] == 1 )
			{
				$pos = ( in_array( $wpadp_bar['settings']['wpadp_pos'], array( 'topf', 'topfl' ) )? 'top': 'bottom' );
				$wpdb->query( "UPDATE `".WPADPL_ADS_TABLE."` SET globalad = '' WHERE `globalad` IN ( 'bar-".$pos."', 'slide-".$pos."' )" );

				$wpadp_bar['globalad'] = $wpadp_bar['adtype'] .'-'. $pos;
			}
			else
				$wpadp_bar['globalad'] = '';

			$wpadp_bar['settings']['wpadp_butmessage']= (isset($_POST['wpadp_butmessage'])? 	esc_html( trim( stripslashes( $_POST['wpadp_butmessage'] ))): '' );
			$wpadp_bar['settings']['wpadp_bpos'] 	= (isset($_POST['wpadp_bpos'])? 		esc_html( trim( stripslashes( $_POST['wpadp_bpos'] ) ) )	: '' );
			$wpadp_bar['settings']['wpadp_bpos'] 	= ($wpadp_bar['settings']['wpadp_bpos'] == 'left'?'left':'right');
			$wpadp_bar['settings']['wpadp_showm'] 	= (isset($_POST['wpadp_showm'])? 		1: 0);
			$wpadp_bar['settings']['wpadp_showt'] 	= 0;
			$wpadp_bar['settings']['wpadp_showtcount']= 0;
			$wpadp_bar['settings']['wpadp_showr'] 	= 0;
			$wpadp_bar['settings']['wpadp_rss_url']	= '';
			$wpadp_bar['settings']['wpadp_showrcount']= 0;
			$wpadp_bar['settings']['wpadp_showp'] 	= 0;
			$wpadp_bar['settings']['wpadp_posttype'] 	= 'all';
			$wpadp_bar['settings']['wpadp_showpcount']= 0;
			$wpadp_bar['settings']['wpadp_disp'] 	= 'dispall';
			$wpadp_bar['settings']['wpadp_socialhead']= (isset($_POST['wpadp_socialhead'])? 	esc_html( trim( stripslashes( $_POST['wpadp_socialhead'] ) ) )	: '' );

			$wpadp_customcss 					= (isset($_POST['wpadp_customcss'])? 	trim( stripslashes( $_POST['wpadp_customcss'] ) )	: '' );
			$wpadp_customcss 					= $this->esc_html( $this->unicode_escape_sequences( utf8_encode( $wpadp_customcss ) ) );

			$wpadp_bar['settings']['wpadp_customcss'] = @htmlentities( $wpadp_customcss, ENT_QUOTES | ENT_SUBSTITUTE, get_option('blog_charset'), false );

			if(isset( $_POST['wpadp_social'] ) ) 
			{
				$wpadp_bar['settings']['wpadp_social'] = array();
				foreach( $_POST['wpadp_social'] as $i => $v )
					$wpadp_bar['settings']['wpadp_social'][] 	= esc_attr( trim( stripslashes( $v ) ) );
			}

			$set_tmp = $wpadp_bar['settings'];

			$wpadp_bar['settings'] = maybe_serialize( $wpadp_bar['settings'] );

			$updated = false;
			$bar_id = 0;
			if( $id > 0 )
			{
				$wpdb->update( WPADPL_ADS_TABLE, $wpadp_bar, array( 'id' => $id ) );
				$updated = true;
				if( ! $wpdb->last_error ) {
					$bar_id = $id;
					$result = sprintf(__('AdPunch bar has been updated, Please <a href="%s">go back to List</a>.','wpadp_lang'), admin_url( 'admin.php?page=wpadp_list' ) );
				} else {
					$error = sprintf(__('Error in updating: %s.','wpadp_lang'), $wpdb->last_error );
				}
			}

			if( $updated == false )
			{
				$wpdb->insert( WPADPL_ADS_TABLE , $wpadp_bar );
				if( $wpdb->insert_id ) {
					$bar_id = $id = $wpdb->insert_id;
					$result = sprintf(__('AdPunch bar has been added, Please <a href="%s">go back to List</a>.','wpadp_lang'), admin_url( 'admin.php?page=wpadp_list' ) );
				} else {
					$error = sprintf(__('Error in updating: %s.','wpadp_lang'), $wpdb->last_error );
				}
			}

			// -========================================- //

			$wpadp_bar['settings'] = $set_tmp;

			$wpadp_meta	= array();
			if( $bar_id > 0 )
			{
				$_REQUEST['id'] = $bar_id;

				// messages
				global $wpadp_message;
				if( $wpadp_bar['settings']['wpadp_showm'] == 1 && $wpadp_message && method_exists( 'wpadp_message', 'update_feed' ) ) 
				{
					$wpadp_bar['settings']['wpadp_mid'] = $wpadp_message->update_feed( $bar_id, $current_user->ID, $_POST['wpadp_message'] );
				}
				else
					$wpadp_message->delete_meta( $bar_id, $current_user->ID );

				$wpadp_bar['settings'] = maybe_serialize( $wpadp_bar['settings'] );

				$wpdb->update( WPADPL_ADS_TABLE, $wpadp_bar, array( 'id'=> $bar_id ) );

				$wpadp_bar['settings'] = maybe_unserialize( $wpadp_bar['settings'] );

				$wpadp_bar				= array();
			}
		}

if( ! isset( $wpadp_bar['wpadp_enable'] ) ) 			$wpadp_bar['wpadp_enable'] = 1;
if( ! isset( $wpadp_bar['settings'] ) )				$wpadp_bar['settings'] = array();
if( ! isset( $wpadp_bar['settings']['wpadp_height'] ) ) 	$wpadp_bar['settings']['wpadp_height']		= '32'; 
if( ! isset( $wpadp_bar['settings']['wpadp_pos'] ) ) 		$wpadp_bar['settings']['wpadp_pos']			= 'topf'; 
if( ! isset( $wpadp_bar['settings']['wpadp_bpos'] ) ) 	$wpadp_bar['settings']['wpadp_bpos']		= 'right'; 
if( ! isset( $wpadp_bar['settings']['wpadp_posttype'] ) )	$wpadp_bar['settings']['wpadp_posttype']		= 'user'; 
if( ! isset( $wpadp_bar['settings']['wpadp_disp'] ) ) 	$wpadp_bar['settings']['wpadp_disp']		= 'dispall';
if( ! isset( $wpadp_bar['settings']['wpadp_socialhead'] ) )	$wpadp_bar['settings']['wpadp_socialhead']	= 'Follow Us';

//echo "<pre>";print_r( $wpadp_bar );echo "</pre>";//

		?>
	<div class="wrap">
	<h2><?php _e( 'WP-AdPunch Lite', 'wpadp_lang' )?></h2>
	<h3><?php echo ( $form == 'add'? __( 'Add: WP-AdPunch ', 'wpadp_lang' ): __( 'Edit: WP-AdPunch ', 'wpadp_lang' ) ); echo ucfirst( $adtype );?></h3>
<?php

if($result)
{
?>
<div id="message" class="updated fade"><p><?php echo $result?></p></div>
<?php
}
if($error)
{
?>
<div class="error fade"><p><b><?php _e('Error: ', 'wpadp_lang')?></b><?php echo $error;?></p></div>
<?php
}
?>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">
	  <form method="post" id="wpadp_addbar" name="wpadp_addbar">
	<?php 
		wp_nonce_field( 'add-wpadp' ); 
		if( $form == 'edit' )
		{
			echo '<input type="hidden" name="id" value="'.$id.'"/>'."\n";
			echo '<input type="hidden" name="call2" value="edit"/>'."\n";
		}
	?>
	<input type="hidden" name="call" value="save"/>
	    <div id="genopdiv" class="postbox"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'General Settings:', 'wpadp_lang' ); ?></span></h3>
	      <div class="inside">
			  <table class="form-table">
				<tbody>
				<tr>
				  <th scope="row"><label for="wpadp_name"><?php _e( 'Campaign Name', 'wpadp_lang' );?></label>
				<?php wp_adpunch_lite::wpadp_help( __( 'Give this campaign a unique name', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" class="regular-text" name="wpadp_name" id="wpadp_name" value="<?php echo $wpadp_bar['title'] ?>" />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_enable"><?php _e( 'Campaign Enabled', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Check this to enable this campaign', 'wpadp_lang' ) ); ?></th>
				  <td><input type="checkbox" name="wpadp_enable" id="wpadp_enable" value="1" <?php checked( $wpadp_bar['enabled'], "1" ); ?> />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_global"><?php _e( 'Mark as Global', 'wpadp_lang' ); ?></label>
					<?php wp_adpunch_lite::wpadp_help( __( 'Please check to mark this ad as Global.<br/>Ads Marked as global will appear on the whole site.<br/> 
					If you wish to show a different Ad on a specific page/ Post, you can do so on the edit Post page.', 'wpadp_lang' ) ); ?></th>
				  <td><input type="checkbox" name="wpadp_global" id="wpadp_global" value="1" <?php if( $wpadp_bar['globalad'] != "" ){ echo " checked='checked' ";}; ?> />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_ipp"><?php _e( 'Ignore Post & Pages', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Posts & Pages that you do not wish the Toolbar to appear.<br/> Comma seperated (id, slug or name)<br/>e.g. &#034;about-us, contact-us&#034;', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" class="regular-text" name="wpadp_ipp" id="wpadp_ipp" value="<?php echo $wpadp_bar['settings']['wpadp_ipp']; ?>" />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_bgc"><?php _e( 'Background color', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'The Background color for the toolbar', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" class="color" name="wpadp_bgc" id="wpadp_bgc" value="<?php echo $wpadp_bar['settings']['wpadp_bgc']; ?>" />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_bdc"><?php _e( 'Border Color', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'The Border color for the toolbar ', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" class="color" class="regular-text color" name="wpadp_bdc" id="wpadp_bdc" value="<?php echo $wpadp_bar['settings']['wpadp_bdc']; ?>" />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_txtc"><?php _e( 'Text Color', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'The Text color for the toolbar ', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" class="color" name="wpadp_txtc" id="wpadp_txtc" value="<?php echo $wpadp_bar['settings']['wpadp_txtc']; ?>" />
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_heighttxt"><?php _e( 'Bar Height', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Height of the toolbar<br/>Long press + &amp; - buttons', 'wpadp_lang' ) ); ?></th>
				  <td><input type="text" name="wpadp_height" id="wpadp_heighttxt" minvalue="32" maxvalue="300" hasauto="no" value="<?php echo $wpadp_bar['settings']['wpadp_height'] ?>" class="regular-text" style="width:50px"/> px 
					&nbsp;<input type="button" name="wpadp_increasebut" class="wpadp_increasebut" id="wpadp_butin-height" value=" + "/>&nbsp;
						<input type="button" name="wpadp_decreasebut" class="wpadp_decreasebut" id="wpadp_butde-height" value=" - "/>

				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_pos"><?php _e( 'Position', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Choose if you wish the toolbar to be shown on top or bottom of the page<br/>
					Fixed: The toolbar will always be visible at the top of the page even after scrolling.<br/>Float: The toolbar will scroll out of view when the page is scrolled.', 'wpadp_lang' ) ); ?></th>
				  <td><table border="0" width="100%" cellpadding="3"><tr><td>
					<label><input type="radio" name="wpadp_pos" id="wpadp_pos1" value="topf" <?php checked( $wpadp_bar['settings']['wpadp_pos'], "topf" ); ?> /> Top [ Fixed ]</label><br/>
					<label><input type="radio" name="wpadp_pos" id="wpadp_pos2" value="topfl" <?php checked( $wpadp_bar['settings']['wpadp_pos'], "topfl" ); ?> /> Top [ Float ]</label><br/>
					</td> <td>
					<label><input type="radio" name="wpadp_pos" id="wpadp_pos3" value="bottomf" <?php checked( $wpadp_bar['settings']['wpadp_pos'], "bottomf" ); ?> /> Bottom [ Fixed ]</label><br/>
					<label><input type="radio" name="wpadp_pos" id="wpadp_pos4" value="bottomfl" <?php checked( $wpadp_bar['settings']['wpadp_pos'], "bottomfl" ); ?> /> Bottom [ Float ]</label><br/>
					</td></tr></table>
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_butmessage"><?php _e( 'Button Message', 'wpadp_lang' ); ?></label>
					<?php wp_adpunch_lite::wpadp_help( __( 'Choose button or specify text which will be shown in the pull down button<br/>You can use HTML or choose from buttons given below<br/>e.g. &lt;strong&gt;Todays Offer&lt;/strong&gt;.', 'wpadp_lang' ) ); ?></th>
				  <td><textarea name="wpadp_butmessage" id="wpadp_butmessage" cols="80" rows="4"><?php echo @html_entity_decode( stripslashes( $wpadp_bar['settings']['wpadp_butmessage'] ), ENT_QUOTES, get_option( 'blog_charset' ) );?></textarea><br/>
					<table id="wpadp_butmessagetable" border="1"><tr>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr1.png';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr2.png';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr3.jpg';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr4.png';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr5.png';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr6.png';?>"/></a>&nbsp;</td>
					<td><a href="javascript:;"><img alt="" src="<?php echo WPADPL_URL.'img/arrow/arr7.png';?>"/></a>&nbsp;</td>
					</tr></table>
				  </td>
				</tr>
				<tr>
				  <th scope="row"><label for="wpadp_pos"><?php _e( 'Button Position', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Specify which side of the bar  the pull-down button will be shown', 'wpadp_lang' ) ); ?></th>
				  <td><label>Left <input type="radio" name="wpadp_bpos" id="wpadp_bpos1" value="left" <?php checked( $wpadp_bar['settings']['wpadp_bpos'], "left" ); ?> /></label>&nbsp;&nbsp;&nbsp;
					<label>Right <input type="radio" name="wpadp_bpos" id="wpadp_bpos2" value="right" <?php checked( $wpadp_bar['settings']['wpadp_bpos'], "right" ); ?> /></label>
				  </td>
				</tr>
			  </table>
	      </div>
	    </div>

	    <div id="messagediv" class="postbox <?php echo ( $form == 'add' ? 'closed':''); ?>"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Messages', 'wpadp_lang' ); ?></span></h3>
	      <div class="inside">
			<!-- ======== messages ========== -->
			  <table class="form-table">
				<tr><th scope="row" colspan="2" style="border-bottom:1px solid #ccc;"><span style="font-weight:bold; font-size:15px;"><?php _e( 'Messages:', 'wpadp_lang' ); ?></span>
				<?php wp_adpunch_lite::wpadp_help( __( 'You can choose one or combination of modes for showing messages<br/>between custom messages, RSS &amp; Twitter.', 'wpadp_lang' ) ); ?><br/></th></tr>

			  </table>
			<table class="form-table">
				<tr><th scope="row" colspan="2" class="wpadp_thhead"><label><input type="checkbox" name="wpadp_showm" id="wpadp_showm" value="1" <?php checked( $wpadp_bar['settings']['wpadp_showm'], "1" ); ?> /> &nbsp;<?php _e( 'Display Custom Messages', 'wpadp_lang' ); ?></label></th></tr>
			  </table>
			<table class="form-table" id="wpadp_showm_2" <?php if( $wpadp_bar['settings']['wpadp_showm'] != "1" ) { ?> style="display:none"<?php } ?>>
				<tr><th scope="row"><label for="wpadp_message"><?php _e( 'Message:', 'wpadp_lang' ); ?></label><input type="hidden" name="wpadp_messagecount" id="wpadp_messagecount" value="0"/></th>
				  <td id="wpadp_messagecol">
					<div id="wpadp_messagediv-0"><label>Message:
						<br/><textarea name="wpadp_message[]" id="wpadp_message-0" cols="50" rows="3" style="width:100%"><?php echo esc_attr( stripslashes( $wpadp_bar['settings']['wpadp_message'][0] ) ); ?></textarea></label></div>
				  </td>
				</tr>
				<tr><th scope="row"> </th>
				  <td>
					<a href="javascript:;" id="wpadp_messageadd">Add Message [ + ]</a>&nbsp;
				  </td>
				</tr>
			</table>
 
			<!-- ======== twitter ========== -->
			  <table class="form-table">
				<tr><th scope="row" colspan="2" class="wpadp_thhead"><label><input type="checkbox" name="wpadp_showt" id="wpadp_showt" value="1" <?php checked( $wpadp_bar['settings']['wpadp_showt'], "1" ); ?> /> &nbsp;<?php _e( 'Display Twitter Messages', 'wpadp_lang' ); ?></label></th></tr>
			</table>
			<!-- ======== RSS ========== -->
			  <table class="form-table">
				<tr><th scope="row" colspan="2" class="wpadp_thhead"><label><input type="checkbox" name="wpadp_showr" id="wpadp_showr" value="1" <?php checked( $wpadp_bar['settings']['wpadp_showr'], "1" ); ?> /> &nbsp;<?php _e( 'Display RSS Feed', 'wpadp_lang' ); ?></label></th></tr>
			</table>
			<!-- ======== POSTS ========== -->
			  <table class="form-table">
				<tr><th scope="row" colspan="2" class="wpadp_thhead"><label><input type="checkbox" name="wpadp_showp" id="wpadp_showp" value="1" <?php checked( $wpadp_bar['settings']['wpadp_showp'], "1" ); ?> /> &nbsp;<?php _e( 'Display Latest Posts', 'wpadp_lang' ); ?></label></th></tr>
			</table>
			<!-- ======== display option ========== -->
			  <table class="form-table">
				<tr><th scope="row" colspan="2" style="font-weight:bold; font-size:13px;border-bottom:1px solid #ccc;"><?php _e( 'Display Options:', 'wpadp_lang' ); ?></th></tr>
				  <th scope="row"><label for="wpadp_disp"><?php _e( 'Display Message Option', 'wpadp_lang' ); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Display All: display all messages one after the other;<br/>Rotate all: Works like Banner rotation', 'wpadp_lang' ) ); ?></th>
				  <td><label>Display All <input type="radio" name="wpadp_disp" id="wpadp_disp1" value="dispall" <?php checked( $wpadp_bar['settings']['wpadp_disp'], "dispall" ); ?> /></label>&nbsp;
					<label>Rotate All <input type="radio" name="wpadp_disp" id="wpadp_disp2" value="disprot" <?php checked( $wpadp_bar['settings']['wpadp_disp'], "disprot" ); ?> /></label>
				  </td>
				</tr>
			</table><br/>
	      </div>
	    </div>
	    <div id="socialdiv" class="postbox  <?php echo ( $form == 'add' ? 'closed':''); ?>"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Social Icons', 'wpadp_lang' ); ?></span></h3>
	      <div class="inside">
			  <table class="widefat">
				<tr>
				  <th scope="row"><label for="wpadp_socialhead"><?php _e( 'Social Headline', 'wpadp_lang' ); ?></label>
				<?php wp_adpunch_lite::wpadp_help( sprintf( __( 'Social Headline [Follow Us]<br/><br/>E.g. <strong style="line-height:2;font-weight:bold;vertical-align:top;">Follow Us</strong>
					<img alt="" src="%s"> <img alt="" src="%s"> <img alt="" src="%s">' ), WPADPL_URL.'img/social/fb.png', WPADPL_URL.'img/social/twtr.png', WPADPL_URL.'img/social/digg.png') ); ?></th>
				  <td><input type="text" name="wpadp_socialhead" id="wpadp_socialhead" value="<?php echo $wpadp_bar['settings']['wpadp_socialhead'] ?>" class="regular-text"/></td>
				</tr>
				  <tr><th scope="row" colspan="2"><label for="wpadp_social"><?php printf( __( 'Social profiles for user: <strong><em>%s</em></strong>','wpadp_lang' ), ucfirst( $current_user->data->display_name ) );?></th></tr>
				  <tr><td colspan="2">
			<!-- ======== Social option ========== -->
			<div id="social_profiles">
				<?php echo $this->wpadp_social_table( $wpadp_bar['settings']['wpadp_social'] );?>
			</div>
			<input type="button" name="wpadp_add_social" id="wpadp_add_social" value="Add Social profile"/>
			<img src="<?php echo admin_url( 'images/wpspin_light.gif' );?>" style="display:none" id="wpadp_sremoveajax" border="0"/>
			<span class="wpadp_result" id="wpadp_srresult">&nbsp;</span><span class="wpadp_err" id="wpadp_srerr"></span>
			<!-- ======== /Social option ========== -->
				</td></tr>
			  </table>

	      </div>
	    </div>
	    <div id="customcssdiv" class="postbox <?php echo ( $form == 'add' ? 'closed':''); ?>"><div class="handlediv" title="<?php _e( 'Click to toggle', 'wpadp_lang' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Custom CSS', 'wpadp_lang' ); ?></span></h3>
	      <div class="inside">
			  <table class="form-table">
				<tr>
				  <th scope="row" colspan="2"><label for="wpadp_customcss"><?php _e( 'Custom CSS', 'wpadp_lang' ); ?></label></th></tr>
				<tr>
				  <td colspan="2"><textarea name="wpadp_customcss" id="wpadp_customcss" rows="4" cols="80" style="width:100%"><?php echo stripslashes( $wpadp_bar['settings']['wpadp_customcss'] ); ?></textarea>
				</td>
				</tr>
			  </table>
	      </div>
	    </div>
		<p>
		  <input type="submit" name="wpadp_save" id="wpadp_save" value="<?php printf( __( 'Save AdPunch %s', 'wpadp_lang' ), ucfirst( $adtype ) ); ?>" class="button button-primary" />
		</p>
	  </form>

	  <hr class="clear" />
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">
		  <?php wp_adpunch_admin::wpadp_admin_side(); ?>
	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->

	<!-- --=================-- -->
	<div id="wpadp_add_social_div" style="display:none">
	<a href="#" id="wpadp_sclose"><img src="<?php echo WPADPL_URL ?>img/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>

	<form name="wpadp_add_social_form" id="wpadp_add_social_form" action="" method="post">
	 <table class="form-table">
		<tr><th scope="row" colspan="2"><?php printf( __( 'Add Social Profile for: %s', 'wpadp_lang' ), $current_user->display_name ); ?></th></tr>
		<tr>
		<td><label for="wpadp_socialname"><?php _e('Name','wpadp_lang'); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Give this profile a unique name', 'wpadp_lang' ) );?></td><td> <input class="regular-text" type="text" name="wpadp_socialname" id="wpadp_socialname" value=""/></td>
		</tr><tr>
		<td><label for="wpadp_socialicon"><?php _e('Icon','wpadp_lang'); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Choose a Icon image for this profile<br/>Either enter URL of an online image, or click the image on the side to use one.', 'wpadp_lang' ) );?></td><td> <input class="regular-text" type="text" name="wpadp_socialicon" id="wpadp_socialicon" value=""/>
		<?php echo $this->socialpicker();?>
		</td>
		</tr><tr>
		<td><label for="wpadp_socialurl"><?php _e('URL','wpadp_lang'); ?></label><?php wp_adpunch_lite::wpadp_help( __( 'Enter Profile URL.<br/>e.g. http://twitter.com/msolution' ) );?></td><td> <input class="regular-text" type="text" name="wpadp_socialurl" id="wpadp_socialurl" value=""/></td>
		</tr>
		<tr><td colspan="3"><input class="button button-primary" type="submit" name="submit" value="<?php _e('Add Profile', 'wpadp_lang' );?>"/>
		<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" style="display:none" id="wpadp_saddajax" border="0"/>
		<span class="wpadp_result" id="wpadp_sresult">&nbsp;</span><span class="wpadp_err" id="wpadp_serr"></span>
		</td></tr>
	</table>
	</form>
	</div>
	</div><!-- /wrap -->

	<!-- ==================== -->
	<?php
		wp_adpunch_lite::wpadp_page_footer();
	}

	/*
	*
	**/
	function socialpicker()
	{
		$url = '';
		ob_start();
	?>
	<a href="javascript:;" id="wpadp_add_share" title="<?php _e('Share','wpadp_lang');?>"><img src="<?php echo WPADPL_URL?>img/share.png" border="0" alt="" width="16" /></a>&nbsp;

	<div id="wpadp_add_share_div" style="display:none">
		<a href="javascript:;" title="Blogger"><img alt="" src="<?php echo WPADPL_URL;?>img/social/blogger.png"></a>
		<a href="javascript:;" title="Delicious"><img alt="" src="<?php echo WPADPL_URL;?>img/social/delcis.jpg"></a>
		<a href="javascript:;" title="Deviantart"><img alt="" src="<?php echo WPADPL_URL?>img/social/deviantart.png"></a>
		<a href="javascript:;" title="Digg"><img alt="" src="<?php echo WPADPL_URL?>img/social/digg.png"></a>

		<a href="javascript:;" title="Facebook"><img alt="" src="<?php echo WPADPL_URL?>img/social/fb.png"></a>
		<a href="javascript:;" title="Flickr"><img alt="" src="<?php echo WPADPL_URL?>img/social/flickr.png"></a>
		<a href="javascript:;" title="Foursquare"><img alt="" src="<?php echo WPADPL_URL?>img/social/foursquare.png"></a>
		<a href="javascript:;" title="Google+"><img alt="" src="<?php echo WPADPL_URL?>img/social/g+.png"></a>

		<br/>
		<a href="javascript:;" title="Github"><img alt="" src="<?php echo WPADPL_URL?>img/social/github.png"></a>
		<a href="javascript:;" title="linkedIn"><img alt="" src="<?php echo WPADPL_URL?>img/social/in.png"></a>
		<a href="javascript:;" title="Instagram"><img alt="" src="<?php echo WPADPL_URL?>img/social/instgram.png"></a>
		<a href="javascript:;" title="MySpace"><img alt="" src="<?php echo WPADPL_URL;?>img/social/mspc.png"></a>

		<a href="javascript:;" title="Pinterest"><img alt="" src="<?php echo WPADPL_URL;?>img/social/pinterest.png"></a>
		<a href="javascript:;" title="Reddit"><img alt="" src="<?php echo WPADPL_URL;?>img/social/redt.png"></a>
		<a href="javascript:;" title="RSS"><img alt="" src="<?php echo WPADPL_URL;?>img/social/rss.gif"></a>
		<a href="javascript:;" title="Skype"><img alt="" src="<?php echo WPADPL_URL;?>img/social/skype.png"></a>
		<br/>

		<a href="javascript:;" title="SoundCloud"><img alt="" src="<?php echo WPADPL_URL;?>img/social/soundcloud.png"></a>
		<a href="javascript:;" title="StumbleUpon"><img alt="" src="<?php echo WPADPL_URL;?>img/social/stmbl.png"></a>
		<a href="javascript:;" title="Tumblr"><img alt="" src="<?php echo WPADPL_URL;?>img/social/tumblr.png"></a>
		<a href="javascript:;" title="Twitter"><img alt="" src="<?php echo WPADPL_URL?>img/social/twtr.png"></a>

		<a href="javascript:;" title="Vimeo"><img alt="" src="<?php echo WPADPL_URL?>img/social/vimeo.png"></a>
		<a href="javascript:;" title="Vine"><img alt="" src="<?php echo WPADPL_URL?>img/social/vine.png"></a>
		<a href="javascript:;" title="Yelp"><img alt="" src="<?php echo WPADPL_URL?>img/social/yelp.png"></a>
		<a href="javascript:;" title="Youtube"><img alt="" src="<?php echo WPADPL_URL?>img/social/youtube.png"></a>
	</div>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
endif;

global $wpadp_bar;
if( ! $wpadp_bar ) $wpadp_bar = new wpadp_bar();