<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Nonetallt\Jinitialize\Plugin\Project\Paths;

class CreateFolder extends JinitializeCommand
{
    private $folder;

    protected function configure()
    {
        $this->setName('folder');
        $this->setDescription('Create a new folder in the project directory.');
        $this->setHelp('');
        $this->addArgument('folder', InputArgument::REQUIRED, 'Give a name for the new folder');
        $this->addOption('permissions', 'p', InputOption::VALUE_OPTIONAL, 'Give access level for created folder in numeral code. Defaults to 0755');
    }

    protected function handle($input, $output, $style)
    {
        $projectDir = $this->getProjectDir($style);
        $folder = $input->getArgument('folder');
        $mode = $input->getOption('permissions') ?? 0755;

        $this->folder = "$projectDir/$folder";
        $this->createFolder($mode);
    }

    private function getProjectDir($style)
    {
        $projectDir = $this->import('project', 'projectPath');
        if(! is_null($projectDir)) return $projectDir;

        $message = 'Give a root path where the folder should be created';
        $projectDir = $style->ask($message, null, function($value) {
            Paths::validate($value, function($message) {
                $this->abort($message);
            });
            return $value;
        });
        
        return $projectDir;
    }

    private function createFolder($mode) 
    {
        if(! mkdir($this->folder, $mode)) {
            $this->abort("Folder $this->folder could not be created");
        }
    }

    public function revert()
    {
        // Revert changes made by handle if possible
        rmdir($this->folder);
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
