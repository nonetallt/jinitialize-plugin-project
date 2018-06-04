<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;

class CopyStubCommandTest extends TestCase
{
    use CleansOutput;

    private $input;
    private $output;

    public function testCopyWithoutChanges()
    {
        $this->runCommand("project:copy-stub $this->input $this->output");
        $this->assertEquals(file_get_contents($this->input), file_get_contents($this->output));
    }

    public function testCopyWithEnv()
    {
        $expected = $this->expectedFolder('test-copy-with-env');
        $_ENV['variable1'] = 'env1';
        $_ENV['variable2'] = 'env2';

        $this->runCommand("project:copy-stub $this->input $this->output --env");
        $this->assertEquals(file_get_contents($expected), file_get_contents($this->output));
    }

    public function testCopyWithExported()
    {
        $expected = $this->expectedFolder('test-copy-with-exported');

        $container = $this->getApplication()->getContainer()->getPlugin('project')->getContainer();
        $container->set('variable3', 'value3');

        $this->runCommand("project:copy-stub $this->input $this->output --exported");
        $this->assertEquals(file_get_contents($expected), file_get_contents($this->output));
    }

    public function testCopyWithExportedAndEnv()
    {
        $expected = $this->expectedFolder('test-copy-with-exported-and-env');

        $_ENV['variable1'] = 'value1';
        $_ENV['variable2'] = 'value2';
        $container = $this->getApplication()->getContainer()->getPlugin('project')->getContainer();
        $container->set('variable3', 'value3');

        $this->runCommand("project:copy-stub $this->input $this->output --exported");
        $this->assertEquals(file_get_contents($expected), file_get_contents($this->output));
    }

    public function testAbortWhenFormatDoesNotContainDollarSymbol()
    {
        $this->expectException(CommandAbortedException::class);
        $this->runCommand("project:copy-stub $this->input $this->output --format=[]");
    }

    public function testSetFormat()
    {

    }

    public function testCanUseInputPathSetBySetInputPathCommand()
    {

    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();

        $_ENV = ['APP_ENV' => 'testing'];

        $this->input = $this->inputFolder('/copy-test.txt');
        $this->output = $this->outputFolder('/copy-test.txt');
    }
}
