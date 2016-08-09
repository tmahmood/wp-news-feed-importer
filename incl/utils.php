<?php

class Utils
{
	public static function clean_text($txt)
	{
		$txt = trim($txt);
		$txt = str_replace(array("\t", "\n"), array(' ', ''), $txt);
		return preg_replace('/\s+/', ' ',$txt);
	}


	public static function download($url, $filename, $is_html=true)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if ($is_html) {
			$replace = array('<br>', '<br/>', '<br />');
			$output = str_replace($replace, "\n", $output);
		}
		file_put_contents($filename, $output);
	}

	public static function download_content($url, $is_html=true)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if ($is_html) {
			$replace = array('<br>', '<br/>', '<br />');
			$output = str_replace($replace, "\n", $output);
		}
		if (DEBUG) {
			$filepath = self::get_cache_path($url);
			file_put_contents($filepath, $output);
		}
		return $output;
	}

	public static function get_cache_path($url)
	{
		$filename = md5($url);
		$uobj = parse_url($url);
		$fullpath = "cache/{$uobj['host']}";
		if (!file_exists($fullpath)) {
			mkdir($fullpath, 0777, true);
		}
		return "$fullpath/$filename";
	}

	public static function get_base_url($url)
	{
		$uobj = parse_url($url);
		$stpath = sprintf("%s%s/", IMG_STORE_PATH, $uobj['host']);
		$srpath = sprintf("%s%s/", IMG_SRC_PATH, $uobj['host']);
		return array($stpath, $srpath);
	}


	public static function d($elm, $vd=false)
	{
		echo '<pre>';
		if (is_object($elm)) {
			var_dump($elm);
		} elseif(is_array($elm)) {
			print_r($elm);
		} else {
			print($elm);
		}
		echo '</pre>';
	}

	function upload_to_wordpress($file, $parent_post_id)
	{
		if (!function_exists('wp_upload_bits')) {
			print("Not available");
			return false;
		}
		$filename = basename($file);
		$upload_file = wp_upload_bits($filename, null, file_get_contents($file));
		if (!$upload_file['error']) {
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_parent' => $parent_post_id,
					'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
					'post_content' => '',
					'post_status' => 'inherit'
					);
			$attachment_id = wp_insert_attachment($attachment, $upload_file['file'],
												  $parent_post_id );
			if (!is_wp_error($attachment_id)) {
				require_once(WORDPRESS_PATH . '/wp-admin/includes/image.php');
				$attachment_data = wp_generate_attachment_metadata($attachment_id,
																   $upload_file['file']);
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
				add_post_meta($parent_post_id, 'article_images', $attachment_id, true);
			}
			return $attachment_id;
		}
	}

	function upload_media_sideload($img, $post_id)
	{
		$tmp = download_url( $url );
		if( is_wp_error( $tmp ) ){
			// download failed, handle error
		}
		$desc = "The WordPress Logo";
		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings
		preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return $id;
		}
	}
}


