<?php

use Crada\Apidoc\Builder;
use Crada\Apidoc\Exception;

include dirname(__FILE__).'/../vendor/autoload.php';


$autoloadFiles = array('Feed.php','User.php', 'Backend.php');
$classes = [];
foreach ($autoloadFiles as $file) {
    $file = dirname(__FILE__).'/../models/'.$file;
    if (is_readable($file)) {
        include_once $file;
        $base = basename($file);
        $classes[] = '\\models\\'.basename($base, ".php");
        //array_push($classes,['\\models\\'.basename($base, ".php")]);
    }

}


$output_dir  = __DIR__.'/docs/';
$output_file = 'api.html'; // defaults to index.html

try {
    ob_start();

    $builder = new Builder($classes, $output_dir, 'EBS API DOCS', $output_file);
    $builder->generate();

    ob_end_clean();

    header("Location: docs/$output_file");
	die();

} catch (Exception $e) {
    echo 'There was an error generating the documentation: ', $e->getMessage();
}
