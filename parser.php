<?php
include_once "incl/bootstrap.php";

$parser = $argv[1];
$feed = new BlogFeed($parser);
$feed->parse();
print_r($feed->posts);
