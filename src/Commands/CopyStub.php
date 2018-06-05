<?php

namespace Nonetallt\Jinitialize\Plugin\Project\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use SebastiaanLuca\StubGenerator\StubGenerator;

use Nonetallt\Jinitialize\JinitializeCommand;
use Nonetallt\Jinitialize\JinitializeContainer;

class CopyStub extends JinitializeCommand
{
    private $out;

    protected function configure()
    {
        $this->setName('copy');
        $this->setDescription('Copy file from input to output.');
        $this->setHelp('');
        $this->addArgument('input', InputArgument::REQUIRED, 'The file you want to copy.');
        $this->addArgument('output', InputArgument::REQUIRED, 'Filepath for the new file.');

        $msg = 'Define the replace format where $ is the name of the value';
        $this->addOption('format', 'f', InputOption::VALUE_REQUIRED, $msg, '[$]');

        $msg = 'Replace values that exist in as exported values';
        $this->addOption('exported', 'x', InputOption::VALUE_NONE, $msg);

        $msg = 'Replace values that exist in .env';
        $this->addOption('env', 'e', InputOption::VALUE_NONE, $msg);
    }

    protected function handle($input, $output, $style)
    {
        $format = $input->getOption('format');

        /* Validate format */
        if(strpos($format, '$') === false) {
            $msg = "Invalid format $format, format must contain the character \$";
            $this->abort($msg);
        } 

        $in = $this->inputPath($input);
        $this->out = $this->outputPath($input);

        $stub = new StubGenerator($in, $this->out);
        $stub->render($this->getReplacements($input));

        $output->writeLn("File $in copied to $this->out");

        $this->export('lastCreatedFile', $this->out);
    }

    private function inputPath($input)
    {
        $path = $this->import('project', 'inputPath') ?? '';
        return $path  . $input->getArgument('input');
    }

    private function outputPath($input)
    {
        $path = $this->import('project', 'outputPath') ?? '';
        return $path  . $input->getArgument('output');
    }

    /**
     * Get array containing key value pairs to replace
     */
    private function getReplacements($input)
    {
        $replacements = [];

        /* Get env values */
        if($this->wasOptionPassed($input, 'env')) {
            foreach($_ENV as $key => $value) {
                $replacements[$this->formatReplacement($input, $key)] = $value;
            }
        }

        /* Get exported values */
        if($this->wasOptionPassed($input, 'exported')) {
            foreach($this->getContainerData() as $plugin => $data) {
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
        return str_replace('$', $key, $input->getOption('format'));
    }

    public function revert()
    {
        // Revert changes made by handle if possible
        unlink($this->out);
    }

    public function recommendsExecuting()
    {
        return [
            SetInputPath::class,
            CreateProject::class           
        ];
    }

    public function recommendsRoot()
    {
        // bool, wether command should be executed with administrative priviliges
        return false;
    }
}
