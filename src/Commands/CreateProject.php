<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Nonetallt\Jinitialize\Plugin\Project\Paths;

class CreateProject extends JinitializeCommand
{
    private $path;

    protected function configure()
    {
        $this->setName('create');
        $this->setDescription('Create a new project directory.');
        $this->setHelp('Creates a new project at the given path and initializes project object for other commands.');
        $this->addOption('permissions', 'p', InputOption::VALUE_OPTIONAL, 'Give access level for created folder in numeral code. Defaults to 0755');
    }

    protected function handle($input, $output, $style)
    {
        $defaultPath = $_ENV['DEFAULT_PROJECTS_FOLDER'] ?? null;

        $msg = 'Give a path to where the project folder should be created';
        $path = $style->ask($msg, $defaultPath, function($value) {
            Paths::validate($value, function($message) {
                $this->abort($message);
            });
            return $value;
        });

        $name = $style->ask('Give a name for the new project');
        $permissions = $this->getInput()->getOption('permissions') ?? 0755;
        $this->path = "$path/$name";

        $this->export('projectName', $name);
        $this->export('projectPath', $path);
        $this->export('projectRoot', $this->path);

        /* Create project folder */
        $command = new CreateFolder($this->getPluginName());
        $command->run(new ArrayInput(['folder' => $name, '--permissions' => $permissions]), $output);

        /* Set output directory default to project root */
        $command = new SetOutputPath($this->getPluginName());
        $command->run(new ArrayInput(['path' => $this->import('project', 'projectRoot')]), $output);

        $output->writeLn("Project created: $this->path");
    }

    public function revert()
    {
        // Revert changes made by handle if possible
        rmdir($this->path);
    }

    public function recommendsRunning()
    {
        return [
        ];
    }

    public function recommendsRoot()
    {
        // bool, wether command should be executed with administrative priviliges
        return false;
    }
}
