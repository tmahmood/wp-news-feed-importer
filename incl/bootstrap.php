<?php
// autoload magic loads the necessarry class
spl_autoload_register(function ($class_name) {
	$cl = strtolower($class_name);
	$rule = "rules/{$cl}.php";
	$incl = "incl/{$cl}.php";
	if (file_exists($rule)) {
		include $rule;
	} else {
		include $incl;
	}
});
