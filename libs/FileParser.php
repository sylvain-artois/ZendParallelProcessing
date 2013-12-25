<?php
/**
 * 
 */
class FileParser extends ZendX_Console_Process_Unix
{
    const VAR_NAME_FILE_PATH = "FILE_PATH";
    const VAR_NAME_ETAB_NAME = "ETAB_NAME";
    const VAR_NAME_PROCESS_ERROR = "PROCESS_ERROR";
	
    /**
     * @var string
     */
    private $_filePath = "";

    /**
     * 
     * @var string
     */
    private $_etabName = "";

    /**
     * @var phpQueryObject
     */
    private $_oHtml;

    /**
     * The main process method
     */
    protected function _run() {

        $this->_filePath = $this->getVariable( self::VAR_NAME_FILE_PATH );

        if( empty( $this->_filePath ) || ! file_exists( $this->_filePath ) ) {

            $this->setVariable( self::VAR_NAME_PROCESS_ERROR, "File to parse {$this->_filePath} doesn't exist");
            return;
        }
        
        //We do that here, cause loadFile send a warning when its load the file
        set_error_handler(array($this, 'processErrorHandler'));

        $this->loadFile( $this->_filePath );
        $sName = $this->getName();

        if ($sName === "") {
            $this->setVariable(self::VAR_NAME_PROCESS_ERROR, "Etab name is empty");
            return;
        }

        try {
            

            $memcache = memcache_connect("localhost", 11211);
            $memcache->add($sName, $aData, FALSE, 1800);
            $memcache->close();

            $this->setVariable(self::VAR_NAME_ETAB_NAME, $sName);
        }
        catch( Exception $e ) {
            $this->setVariable(self::VAR_NAME_PROCESS_ERROR, sprintf("Error within %s, %s %s %s %s %s", $this->_filePath, $this->_etabName, $e->getCode(), $e->getLine(), $e->getFile(), $e->getMessage()));
        }
    }

    /**
     * Load the html document
     */
    public function loadFile($filePath) {
        try {
            $this->_oHtml = phpQuery::newDocumentFile($filePath);
        } catch (Exception $e) {
            //do nothing
        }
    }

    /**
     * Parse html to get etab name
     */
    public function getName() {
        $sEtabName = $this->_oHtml->find("h1")->eq(0)->text();
        return SString::f($sEtabName)->cleanString()->removeWhitespace()->get();
    }
    
    public function isRunning() {
        $this->_isRunning = (0 == pcntl_waitpid( $this->getPid(), $status, WNOHANG));
	return $this->_isRunning;
    }
    
    /**
    *
    * @param int $errno
    * @param string $errstr
    * @param string $errfile
    * @param int $errline
    * @return void|boolean
    */
    public function processErrorHandler( $errno, $errstr, $errfile, $errline ) {

        if( $errno >= ER )
            throw new Exception( $errstr, $errno );

            /* Don't execute PHP internal error handler */
            return true;
    }

}