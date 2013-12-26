<?php
namespace Com\Thelab;

/**
 * Created by PhpStorm.
 * User: media-server
 * Date: 25/12/13
 * Time: 19:08
 */
class FileParserUnixProcess extends \ZendX_Console_Process_Unix {

    const VAR_NAME_FILE_PATH = "FILE_PATH";
    const VAR_NAME_ETAB_NAME = "ETAB_NAME";
    const VAR_NAME_CALLABLE = "CALLABLE";
    const VAR_NAME_PROCESS_ERROR = "PROCESS_ERROR";

    /**
     * @var function
     */
    private $_callable;

    /**
     * @var string
     */
    private $_fileToParse;

    /**
     * This method actually implements the pseudo-thread logic.
     *
     * @return void
     */
    protected function _run() {

        $this->_setAlive();
        echo "\nFileParserUnixProcess::_run called, pid: " . $this->getPid();

        $this->_callable = $this->getVariable( FileParserUnixProcess::VAR_NAME_CALLABLE );
        $this->_fileToParse = $this->getVariable( FileParserUnixProcess::VAR_NAME_FILE_PATH );

        if( is_callable( $this->_callable )  ) {
            $callbackReturn = call_user_func( $this->_callable, $this->_fileToParse );
        }
        else {
            $this->setVariable( self::VAR_NAME_PROCESS_ERROR, "Function to exec can't be found" );
        }

        if( FALSE === $callbackReturn ) {
            $this->setVariable( self::VAR_NAME_PROCESS_ERROR, "Something goes wrong within the callback" );
        }

        $this->_setAlive();
        sleep(1);
    }

    /*public function isRunning() {
        return ( 0 == pcntl_waitpid( $this->getPid(), $status, WNOHANG ) );
    }*/
}
