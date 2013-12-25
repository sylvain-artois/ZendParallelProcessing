<?php
// include the composer autoloader
require_once __DIR__ . '/vendor/autoload.php'; 

// import the Symfony Console Application 
use Symfony\Component\Console\Application; 
use Com\Thelab\ParallelCommand;

set_time_limit(0);
date_default_timezone_set( 'Europe/Paris' ); 

$app = new Application();
$app->add( new ParallelCommand() );
$app->run();
