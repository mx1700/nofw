<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/10
 * Time: 16:25
 */

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Console
 */
class TestCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription("Test commend")
            ->addOption("name", null, InputOption::VALUE_OPTIONAL, "you name", "Tom");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption("name");
        $output->writeln("<comment>hello {$name}</comment>");
    }
}