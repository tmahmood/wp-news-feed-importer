<?php

class Utils
{
	public static function clean_text($txt)
	{
		$txt = trim($txt);
		$txt = str_replace(array("\t", "\n"), array(' ', ''), $txt);
		return preg_replace('/\s+/', ' ',$txt);
	}


	public static function download($url, $filename)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$replace = array('<br>', '<br/>', '<br />');
		$output = str_replace($replace, "\n", $output);
		file_put_contents($filename, $output);
	}
}

