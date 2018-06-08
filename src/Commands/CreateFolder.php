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

        /* Remove first slash from the folder argument to prevent double slash
            with getProjectDir
         */
        if(starts_with($folder, '/')) $folder = str_after($folder, '/');

        $this->createFolder("$projectDir$folder", $mode, $output);
    }

    private function getProjectDir($style)
    {
        /* Use output path, if that is null, use projectPath instead */
        $projectDir = $this->import('outputPath') ?? $this->import('parent');

        /* If not found, ask user */
        if(is_null($projectDir)) {
            $projectDir = '';
        }

        /* Append trailing slash if missing */
        if(! ends_with($projectDir, '/')) $projectDir .= '/';

        return $projectDir;
    }

    private function createFolder(string $path, $mode, $output) 
    {
        if(file_exists($path)) {
            $this->abort("Folder $path can't be created, path already exists");
        }

        if(! mkdir($path, $mode)) {
            $this->abort("Folder $path could not be created");
        }

        $output->writeLn("Folder created: $path");
        $this->folder = $path;

        $this->export('last_created_folder', $this->folder);
    }

    public function revert()
    {
        // Revert changes made by handle if possible
        if(! is_null( $this->folder )) rmdir($this->folder);
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
