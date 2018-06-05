<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;

class CreateFolderCommandTest extends TestCase
{
    use CleansOutput;

    public function testCreateFolder()
    {
        $output = $this->outputFolder('test');
        $this->runCommand("project:folder $output");

        $this->assertTrue(is_dir($output));
    }

    public function testCreateMultilevelFolder()
    {
        $output = $this->outputFolder('test');
        $this->runCommand("project:folder $output");

        /* Create another folder in the created folder */
        $output = $this->outputFolder('test/another');
        $tester = $this->runCommand("project:folder $output");

        $this->assertTrue(is_dir($output));
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();
    }
}
