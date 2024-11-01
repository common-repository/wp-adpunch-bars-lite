<?php
/*
 * class: @wpadp_message
 *
 **/

if(!class_exists('wpadp_message')) :
class wpadp_message extends wp_adpunch_head
{
	var $templates;

	function __construct()
	{

	}

	function get_meta( $barid, $userid )
	{
		global $wpdb;
		return $wpdb->get_row( "SELECT * FROM `".WPADPL_META_TABLE."` WHERE bar_id = ".(int)$barid." AND meta_key = 'message' LIMIT 1", ARRAY_A );
	}

	function delete_meta( $barid, $userid )
	{
		global $wpdb;
		$wpdb->query( "DELETE FROM `".WPADPL_META_TABLE."` WHERE bar_id = ".(int)$barid." AND meta_key = 'message' LIMIT 1" );//userid
		return true;
	}

	function wpadp_main_head()
	{
	}

	/*
	 *
	 * update messages for the given user;
	 *
	 * @param bar ID, messages
	 * @return Meta ID;
	 */
	function update_feed( $barid, $userid, $messages )
	{
		global $wpdb;

		$all_items = array();

		if( ! empty( $messages ) )
		{
		foreach($messages as $mes )
		{
			$mes 	= trim( stripslashes( $mes ) );
			if( empty( $mes ) ) continue;

			$mes 	= $this->esc_html( $this->unicode_escape_sequences( utf8_encode( $mes ) ) );

			$all_items[] = array( 
				'msg' => @htmlentities( $mes, ENT_QUOTES | ENT_SUBSTITUTE, get_option('blog_charset'), false ), 
				'link' => '',
				'cnt' => 0,
				'unqid' => md5( $mes )
			);
		}}
		$ms = array();
		$ms['bar_id']		= $barid;
		$ms['user_id'] 		= $userid;
		$ms['thisepxires']	= 0;
		$ms['epxiresat'] 		= date("Y-m-d H:i:s", time() + ( WPADPL_MSG_CACHE_TIME * 60 * 60 ) );
		$ms['meta_key'] 		= 'message';
		$ms['meta_value'] 	= '';
		$ms['meta_values']	= addslashes( maybe_serialize( $all_items ) );
		$meta_id = 0;

		$meta_id = $wpdb->get_var( "SELECT id FROM `".WPADPL_META_TABLE."` WHERE bar_id = ".$barid." AND meta_key = 'message' LIMIT 1" );
		if( ! empty( $meta_id ) )
		{
			$wpdb->update( WPADPL_META_TABLE, $ms, array( 'id' => $meta_id ) );
		} 
		else
		{
			$wpdb->insert( WPADPL_META_TABLE, $ms );
			$meta_id = $wpdb->insert_id;
		}
		return $meta_id;
	}
}
endif;


global $wpadp_message;
if(empty($wpadp_message)) $wpadp_message = new wpadp_message();