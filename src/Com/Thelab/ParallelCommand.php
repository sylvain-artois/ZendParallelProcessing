<?php
namespace Com\Thelab;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of ParallelCommand
 *
 * @author S. Artois
 */
class ParallelCommand extends Command {

    /**
     *
     */
    protected function configure() {

        $this
            ->setName( 'parallel:run' )
            ->setDescription( 'Let\'s do something in parallel' )
            ->addArgument(
                'callable',
                InputArgument::REQUIRED,
                'What task to launch'
            )
            ->addOption(
               'process_nbr',
               'p',
               InputOption::VALUE_OPTIONAL,
               'How many fork to launch ?',
               10
            )
        ;
    }

    /**
     * green <info />
     * yellow <comment />
     * cyan background <question />
     * red background <error />
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $callable = $input->getArgument( 'callable' );
        $processNbr = $input->getOption( 'process_nbr' );

        $output->writeln( "callable: <info>$callable</info>, process_nbr: <info>$processNbr</info>" );

        $processRefs = array();

        for( $i = 0 ; $i < $processNbr ; $i++ ) {

            $process = new FileParserUnixProcess();
            $process->setVariable( FileParserUnixProcess::VAR_NAME_CALLABLE, $callable );
            $process->start();

            $processRefs[] = $process;
        }

        $atLeastOneProcessIsRunning = TRUE;
        $parsedError = array();
        $parsedData = array();

        //Loop while a process run
        while( $atLeastOneProcessIsRunning ) {

            foreach( $processRefs as $process ) {

                if( ! is_null( $process ) && $process->isRunning() ) {

                    //wait for process to end
                    sleep(1);
                    break;
                }
            }

            //No running proces, get data
            $atLeastOneProcessIsRunning = FALSE;

            foreach( $processRefs as &$process ) {

                if( is_null( ( $error = $process->getVariable( FileParserUnixProcess::VAR_NAME_PROCESS_ERROR ) ) ) ) {
                    $parsedData[] = $process->getVariable( FileParserUnixProcess::VAR_NAME_ETAB_NAME );
                }
                else {
                    $parsedError[] = $error;
                }

                //allow garbage collection
                $process = NULL;
            }

            echo "\nparsedData";
            var_dump( $parsedData );

            echo "\nparsedError";
            var_dump( $parsedError );
        }
    }
}
