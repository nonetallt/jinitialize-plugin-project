<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;

class CreateFolderCommandTest extends TestCase
{
    use CleansOutput;

    public function testCreateFolder()
    {
        $output = $this->outputFolder();

        /* User inputs the project folder path pointing to output */
        $input = [$output];

        $this->runCommand('project:folder test', [], $input);

        $this->assertTrue(is_dir($output.'/test'));
    }

    public function testCreateMultilevelFolder()
    {
        $output = $this->outputFolder();
        $input = [$output];
        $this->runCommand('project:folder test', [], $input);

        /* Create another folder in the created folder */
        $input = [$output.'/test'];
        $this->runCommand('project:folder another', [], $input);

        $this->assertTrue(is_dir($output.'/test/another'));
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();
    }
}
