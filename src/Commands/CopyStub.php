<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use SebastiaanLuca\StubGenerator\StubGenerator;

use Nonetallt\Jinitialize\JinitializeCommand;

class CopyStub extends JinitializeCommand
{
    private $file;

    protected function configure()
    {
        $this->setName('copy-stub');
        $this->setDescription('Copy a file to the project folder.');
        $this->setHelp('');
        $this->addArgument('input', InputArgument::REQUIRED, 'The file you want to copy.');
        $this->addArgument('output', InputArgument::REQUIRED, 'Filepath for the new file.');

        $msg = 'Define the replace format where $ is the name of the value';
        $this->addOption('format', 'f', InputOption::VALUE_REQUIRED, $msg, '[$]');

        $msg = 'Replace values that exist in as exported values';
        $this->addOption('exported', 'x', InputOption::VALUE_OPTIONAL, $msg);

        $msg = 'Replace values that exist in .env';
        $this->addOption('env', null, InputOption::VALUE_OPTIONAL, $msg);
    }

    protected function handle($input, $output, $style)
    {
        $this->file = $input->getArgument('input');

        $stub = new StubGenerator($this->file, $input->getArgument('output'));
        $stub->render($this->getReplacements($input));

        $this->export('lastCreatedFile', $this->file);
    }

    /**
     * Get array containing key value pairs to replace
     */
    private function getReplacements($input)
    {
        $replacements = [];

        /* Get env values */
        if($input->hasOption('env')) {
            foreach($_ENV as $key => $value) {
                $replacements[$this->formatReplacement($input, $key)] = $value;
            }
        }

        /* Get exported values */
        if($input->hasOption('exported')) {
            foreach($this->getApplication()->getContainer()->getData() as $plugin => $data) {
                foreach($data as $key => $value) {
                    $replacements[$this->formatReplacement($input, "$plugin:$key")] = $value;
                }
            }
        }
        return $replacements;
    }

    /**
     * Get string that should be replaced accoording to current format
     */
    private function formatReplacement($input, $key)
    {
        $format = $input->getOption('format');
        if(strpos($format, '$') === false) $this->abort('Invalid format, format must contain the character $');

        return str_replace('$', $key, $input->getOption('format'));
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
