<?php
// autoload magic loads the necessarry class
spl_autoload_register(function ($class_name) {
	$cl = strtolower($class_name);
	$rule = SCRIPT_ABSPATH . "/rules/{$cl}.php";
	$incl = SCRIPT_ABSPATH . "/incl/{$cl}.php";
	if (file_exists($rule)) {
		include $rule;
	} else {
		include $incl;
	}
});
