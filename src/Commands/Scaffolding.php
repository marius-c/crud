<?php namespace Ionut\Crud\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Scaffolding extends Command
{
    protected function configure()
    {
        $this
            ->setName('scaffolding')
            ->addArgument(
                'table',
                InputArgument::REQUIRED,
                'The name of the table'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('table');
        $class_name = $this->camelCase($name);

        $template = file_get_contents(__DIR__.'/Templates/Scaffolding.php');
        $template = str_replace([
            'CLASS_NAME',
            'TABLE_NAME'
        ],
            [
                $class_name,
                $name
            ],
            $template);

        $app_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/app';
        file_put_contents($file = $app_path.'/Admin/Crud/Scaffolding/'.$class_name.'.php', $template);
        $output->writeln("Successfuly created $file");
    }

    public function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9'.implode("", $noStrip).']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return ucfirst($str);
    }
}


