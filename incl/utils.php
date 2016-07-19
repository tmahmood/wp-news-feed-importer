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
		if (DEBUG) {
			$filepath = self::get_cache_path($url);
			if (file_exists($url)) {
				return file_get_contents($filepath);
			}
		}
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

}

