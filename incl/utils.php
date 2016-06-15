<?php

class Utils
{
	public static function clean_text($txt)
	{
		$txt = trim($txt);
		$txt = str_replace(array("\t", "\n"), array(' ', ''), $txt);
		return preg_replace('/\s+/', ' ',$txt);
	}
}

