<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;

class CopyFileCommandTest extends TestCase
{
    use CleansOutput;

    public function testCopyFile()
    {
        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';

        $this->runCommand("project:copy $input $output");

        $this->assertEquals(file_get_contents($input), file_get_contents($output));
    }

    public function testSetInputPath()
    {

    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();
    }
}
