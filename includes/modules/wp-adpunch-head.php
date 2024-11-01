<?php
/*
 * class: @wp_adpunch_head
 *
 **/

class wp_adpunch_head
{
	var $http_code;
	var $http_type;
	var $http_info;
	var $http_url;
	var $http_error;
	var $http_error_line;

	function __construct()
	{
		$this->http_code 	= '';
		$this->http_type 	= '';
		$this->http_info 	= '';
		$this->http_url 	= '';
		$this->http_error	= '';
		$this->http_error_line  = '';
	}


	function get_meta_by_id( $metaid )
	{
		global $wpdb;
		$info = $wpdb->get_row( "SELECT * FROM `".WPADPL_META_TABLE."` WHERE id = ".(int)$metaid." LIMIT 1", ARRAY_A );
		return $info;
	}

	function update_meta_cnt( $alltext )
	{
		global $wpdb;
		$info = $wpdb->get_row( "SELECT * FROM `".WPADPL_META_TABLE."` WHERE id = ".$alltext['pid']." LIMIT 1", ARRAY_A );

		if( ! empty( $info ) )
			$info = maybe_unserialize( stripslashes( $info['meta_values'] ) );

		if( ! empty( $info ) ){
		foreach( $info as $i => $v )
		{
			if( strcmp( $alltext['unqid'], $v['unqid'] ) == 0 )
			{
				$info[$i]['cnt']++;
				break;
			}
		}}
		$info	= addslashes( maybe_serialize( $info ) );

		$wpdb->update( WPADPL_META_TABLE, array( 'meta_values' => $info ), array( 'id' => $alltext['pid'] ) );

		return true;
	}

	// converts a simple cml onject to array() and then sends the value back;
	function obj_2_array($obj)
	{
		if(gettype($obj) == "object")
		{
			$obj = (array)$obj;
			$obj = $obj[0];
		}
		return $obj;
	}

	/* returns content/ content_type/ header_code */
	function fetch_content( $url, $getwhat='content' )
	{
		$this->http_code 	= '';
		$this->http_type 	= '';
		$this->http_info 	= '';
		$this->http_url 	= '';
		$this->http_error	= '';
		$this->http_error_line  = '';

		$query			= "";
		$urlarray 			= parse_url ($url);
		if($urlarray === false)
			return array(false, "Invalid URL.");

		if ( empty ($urlarray['path'] )) $urlarray['path'] = '/';
		if ( !empty ($urlarray['query'])) $query 	= "?".$urlarray['query'];

		$final_url	= $urlarray['scheme']."://".$urlarray["host"].$urlarray['path'].$query;
		$host = $urlarray["host"];

		$header[] = "Host: $host";
		$header[] = "Mime-Version: 1.0";
		$header[] = "Referer: ".$host; //$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$header[] = "Accept: */*";
		$header[] = "Expect: ";
		$header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0";
		$header[] = "Connection: close \r\n";

		if ( function_exists('curl_init') )
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $final_url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 25);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$out = curl_exec ($ch);

			$this->http_code 	= curl_getinfo( $ch, CURLINFO_HTTP_CODE);
			$this->http_type 	= curl_getinfo( $ch, CURLINFO_CONTENT_TYPE);
			$this->http_url 	= curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
			$this->http_info 	= curl_getinfo( $ch );

			if (curl_errno($ch)) {
				$this->http_error = sprintf(__("%d: Unable to contact server - URL: %1s: Last URL: %2s; Err: %3s; Err Msg: %4", 'mof_lang'), __LINE__, $url, $this->http_url, curl_errno($ch), curl_error($ch));
				curl_close ($ch); 
				return false;
			}
			if ( empty($out)) {
				$this->http_error = sprintf(__("%d: cURL Error - Status: %1s; ContentType: %2s; for url: %3s", 'mpp_lang'), __LINE__, $this->http_code, $this->http_type, $url );
				curl_close ($ch); 
				return false;
			}
			if($ch) curl_close ($ch);
		} 
		else 
		{
			$old_ua = @ ini_get('user_agent');
			@ ini_set('user_agent', "Mozilla/5.0 (Windows NT 5.1; rv:19.0) Gecko/20100101 Firefox/19.0 (Compatible google:msolution)" );
			@ ini_set( 'allow_url_fopen', '1');

			$context_options 					= array ();
			$context_options['http']['method'] 		= 'GET';
			$context_options['http']['header'] 		= ( implode( "\r\n", $headers ) )."\r\n";

			$out =  file_get_contents($url, false, stream_context_create($context_options) );
			@ ini_set('user_agent', $old_ua);

			$this->http_url 	= $url;

			if ( empty($ret) ) 
			{
				$this->http_error = sprintf(__("%d: Unable to contact server - Please check if fopen is enabled; %1s;", 'mpp_lang'), __LINE__, $url );
				return false;
			}
		}

		if( $getwhat=='content' && $this->http_code == 200 )
		{
			$this->http_error_line  = __LINE__;
			return $out;
		}
		else if( $getwhat=='content' && $this->http_code != 200 )
		{
			$this->http_error_line  = __LINE__;
			return false;
		}
		else if( $getwhat=='img' && $this->http_code == 200 ) {
			if( stripos( $this->http_type, "image/" ) !== false )
			{
				$this->http_error_line  = __LINE__;
				return $this->http_url;
			}
			else
			{
				$this->http_error_line  = __LINE__;
				return false;
			}
		}
		else if( $getwhat=='img' && $this->http_code != 200 )
		{
			$this->http_error_line  = __LINE__;
			return false;
		}
		else if( $getwhat=='url' && $this->http_code == 200 )
		{
			$this->http_error_line  = __LINE__;
			return $this->http_url;
		}
		else if( $getwhat=='url' && $this->http_code != 200 )
		{
			$this->http_error_line  = __LINE__;
			return false;
		}
	}

	function is_valid_img( $img )
	{
		return $this->fetch_content( $img, 'img' );
	}

	function is_valid_url( $url )
	{
		return $this->fetch_content( $url, 'url' );
	}

	// override wordpress esc_html function with addslashes added.//
	function esc_html($str)
	{
		return addslashes( esc_html( $str ) );
	}

	//escaping chars that utf8_encode can not/// something....
	function unicode_escape_sequences($str){
		$working = json_encode($str);
		$working = preg_replace('/\\\u([0-9a-z]{4})/', '&#x$1;', $working);
		return json_decode($working);
	}
}

if( ! function_exists( 'log_error' ) ) {
function log_error( $barid, $line, $function, $mod, $msg )
{
	if( isset( $_GET['v'] ) )
		echo  "Error: ".$barid.';;'.$line.';;'.$function.';;'.$mod.';;'.$msg;
}
}
if( ! function_exists( '_p' ) ) {
function _p( $a, $exit=false )
{
 echo "<pre>";print_r( $a );echo "</pre>";
 if( $exit != false ) exit;
}
}