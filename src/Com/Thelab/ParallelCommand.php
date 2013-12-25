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
            ->setName( 'parallel:do' )
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
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {
        
        $callable = $input->getArgument( 'callable' );
        $processNbr = $input->getOption( 'process_nbr' );
        
        $output->writeln( "callable: $callable, process_nbr: $processNbr" );
    }
}
