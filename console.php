<?php
// include the composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

//Update include_path for Zend Framework to work
set_include_path(
  get_include_path() .
  PATH_SEPARATOR . dirname(__FILE__) . "/vendor/zendframework/zf1-extras/library" .
  PATH_SEPARATOR . dirname(__FILE__) . "/vendor/mazelab/zendframework1-min/library" );

// import the Symfony Console Application
use Symfony\Component\Console\Application;
use Com\Thelab\ParallelCommand;

//disable time limit
set_time_limit(0);
date_default_timezone_set( 'Europe/Paris' );

$app = new Application();
$app->add( new ParallelCommand() );
$app->run();

/**
 * @todo extract the function
 * @param $filePath
 */
function Nimp( $filePath ) {
    echo "\nNimp, filepath: $filePath";
}
