<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Nonetallt\Jinitialize\Plugin\Project\Paths;

class CopyFile extends JinitializeCommand
{
    private $file;

    protected function configure()
    {
        $this->setName('copy');
        $this->setDescription('Copy a file to the project folder.');
        $this->setHelp('');
        $this->addArgument('input', InputArgument::REQUIRED, 'The file you want to copy.');
        $this->addArgument('output', InputArgument::REQUIRED, 'Filepath for the new file.');

        $msg = 'Give access level for the the copied file in numeral code. Defaults to 0664';
        $this->addOption('permissions', 'p', InputOption::VALUE_OPTIONAL, $msg);
    }

    protected function handle($input, $output, $style)
    {
        $this->file = $input->getArgument('input');

        $content = file_get_contents($this->file);
        file_put_contents($input->getArgument('output'), $content);

        $this->export('lastCreatedFile', $this->file);
    }

    public function revert()
    {
        // Revert changes made by handle if possible
        unlink($this->file);
    }

    public function recommendsExecuting()
    {
        return [
            CreateProject::class           
        ];
    }

    public function recommendsRoot()
    {
        // bool, wether command should be executed with administrative priviliges
        return false;
    }
}
