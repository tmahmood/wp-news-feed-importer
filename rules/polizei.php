<?php

class Polizei extends RDF
{
	var $feed_url = array(
			"http://www.polizei.bayern.de/lka/polizei.rss",
			"http://www.polizei.bayern.de/verwaltungsamt/polizei.rss",
			"http://www.polizei.bayern.de/bepo/polizei.rss",
			"http://www.polizei.bayern.de/muenchen/polizei.rss",
			"http://www.polizei.bayern.de/niederbayern/polizei.rss",
			"http://www.polizei.bayern.de/oberbayern_nord/polizei.rss",
			"http://www.polizei.bayern.de/oberbayern/polizei.rss",
			"http://www.polizei.bayern.de/oberfranken/polizei.rss",
			"http://www.polizei.bayern.de/oberpfalz/polizei.rss",
			"http://www.polizei.bayern.de/schwaben/polizei.rss",
			"http://www.polizei.bayern.de/schwaben_sw/polizei.rss",
			"http://www.polizei.bayern.de/unterfranken/polizei.rss",
			);
	var $category = array(
			"lka" => "München (Bayerisches Landeskriminalamt)",
			"verwaltungsamt" => "München (Polizeiverwaltungsamt)",
			"bepo" => "München (Bayerische Bereitschaftspolizei)",
			"muenchen" => "München (Polizei)",
			"niederbayern" => "Straubing (Polizeipräsidium Niederbayern)",
			"oberbayern_nord" => "Ingolstadt (Polizeipräsidium Oberbayern Nord)",
			"oberbayern" => "Rosenheim (Polizeipräsidiums Oberbayern Süd)",
			"oberfranken" => "Bayreuth (Polizeipräsidium Oberfranken)",
			"oberpfalz" => "Regensburg (Polizeipräsidium Oberpfalz)",
			"schwaben" => "Augsburg (Polizeipräsidium Schwaben Nord)",
			"schwaben_sw" => "Kempten (Polizeipräsidium Schwaben Süd/West)",
			"unterfranken" => "Würzburg (Polizeipräsidiums Unterfranken)",
			);
	var $text_cnt = '';

	function get_content($xpath)
	{
		$text = array();
		$elm = $xpath->query('//p[@class="inhaltText"]')->item(0);
		$text[] = $elm->nodeValue;
		while ($elm = $elm->nextSibling) {
			$t = trim($elm->nodeValue);
			if ($t != '') {
				$text[] = $t;
			}
		}
		return implode("\n\n", $text);
	}

	function get_image($xpath)
	{
		return "";
	}

	function get_missing_title($xpath, &$post)
	{
		$post->title = $xpath->query('//h1')->item(0)->nodeValue;
	}

	function get_missing_date($xpath, &$post)
	{
		$date = explode(',', $xpath->query('//p[@class="inhaltTextDatum"]')
				->item(0)->nodeValue);
		$post->date = $date[0];
	}


	function get_missing_category($xpath, &$post)
	{
		$segs = explode('/', $post->link);
		$keys = array_keys($this->category);
		foreach ($segs as $seg){
			if (in_array($seg, $keys)) {
				$post->category = $this->category[$seg];
				break;
			}
		}
	}
}
