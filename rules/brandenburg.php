<?php

class Brandenburg extends JSON
{
	var $feed_url = "https://polizei.brandenburg.de/ipa_api/news/version/1/count/300";
	var $base_url = "https://polizei.brandenburg.de";
	var $category = array(
						"1"   => "Prignitz",
						"2"   => "Ostprignitz-Ruppin",
						"3"   => "Oberhavel",
						"4"   => "Uckermark",
						"5"   => "Havelland",
						"6"   => "Barnim",
						"7"   => "Potsdam-Mittelmark",
						"8"   => "Märkisch-Oderland",
						"9"   => "Teltow-Fläming",
						"10"  => "Dahme-Spreewald",
						"11"  => "Oder-Spree",
						"12"  => "Elbe-Elster",
						"13"  => "Oberspreewald-Lausitz",
						"14"  => "Spree-Neiße",
						"30"  => "Berlin",
						"85"  => "Potsdam (PDM)",
						"101" => "Oder-Spree/Frankfurt am Main (LOS/FFO)",
						"102" => "Brandenburg an der Havel (PM/BRB)",
						"103" => "Cottbus (CBS)",
						"500" => "Überregional",
					);
	var $imgs_sel = '//div[@class="pbb-article-text"]/img';

	function parse(&$post, $item)
	{
		$post->category = $this->category[$item->district];
		$post->title = $item->title;
		$post->link = $this->base_url . $item->url;
		$post->text = Utils::clean_text($item->text);
	}

	function get_items()
	{
		$str = file_get_contents ($this->feed_url);
		$items = json_decode($str);
		return $items->data;
	}

}
