<?php

if( php_uname() !== "linux" ){
    throw new Exception();
}

//Use memcache for inter-process communication
$memcache = memcache_connect( "localhost", 11211 );
$memcache->flush();
$memcache->close();

$helper = new ParserHelper();
$log = $helper->createLogger();
$log->info( "Parser start" );

$parsedData = [];
$parsedError = [];
$processNbr = 10;

$fileList = $helper->getFileList( "/home/user/path_2_data" );

do {
    $fileToHandle = count( $fileList );
    
    //do not create more fork than needed
    if( $processNbr > $fileToHandle ) {
        $processNbr = $fileToHandle;
    }

    //Create $processNbr FileParser
    for( $i = 0 ; $i < $processNbr ; $i++ ) {

        $fileToParse = array_shift( $fileList );
        $log->info( "Create fork that will parse " . $fileToParse );

        $process = new FileParser();
        $process->setVariable( FileParser::VAR_NAME_FILE_PATH, $fileToParse );
        $process->start();

        $processRefs[] = $process;
    }

    $atLeastOneProcessIsRunning = TRUE;

    //Loop while a process run
    while( $atLeastOneProcessIsRunning ) {

        foreach( $processRefs as $process ) {
            
            if( $process->isRunning() ) {
                
                //wait for process to end
                sleep(1);
                break;
            }
        }
        
        //No running proces, get data
        $atLeastOneProcessIsRunning = FALSE;

        foreach( $processRefs as &$process ) {

            if( is_null( ( $error = $process->getVariable( FileParser::VAR_NAME_PROCESS_ERROR ) ) ) ) {
                $parsedData[] = $process->getVariable( FileParser::VAR_NAME_ETAB_NAME );
            }
            else {
                $parsedError[] = $error;
            }

            //allow garbage collection
            $process = NULL;
        }
    }
}
while( ! empty( $fileList ) );
