<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SetOutputPath extends JinitializeCommand
{
    private $path;

    protected function configure()
    {
        $this->setName('set-output-path');
        $this->setDescription('Set the base path to use for output.');
        $this->setHelp('The path should be pointed at a valid folder, a trailing slash will be automatically appended to the path if missing.');
        $this->addArgument('path', InputArgument::REQUIRED, 'The folder path you want to use.');
    }

    protected function handle($input, $output, $style)
    {
        $this->path = $input->getArgument('path');

        /* Check that the path is a valid folder */
        if(! is_dir($this->path)) $this->abort("Cannot set output path $this->path, not a folder");

        /* Append trailing slash if missing */
        if(! ends_with($this->path, '/')) $this->path .= '/';

        $output->writeLn("Output path set: $this->path");

        $this->export('outputPath', $this->path);
    }

    public function revert()
    {
        // Revert changes made by handle if possible
    }

    public function recommendsRoot()
    {
        // bool, wether command should be executed with administrative priviliges
        return false;
    }
}
