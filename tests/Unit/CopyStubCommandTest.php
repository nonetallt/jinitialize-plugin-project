<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;

class CopyStubCommandTest extends TestCase
{
    use CleansOutput;

    public function testCopyWithoutChanges()
    {
        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';

        $this->runCommand("project:copy-stub $input $output");

        $this->assertEquals(file_get_contents($input), file_get_contents($output));
    }

    public function testCopyWithEnv()
    {
        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';
        $expected = $this->expectedFolder('test-copy-with-env');

        $_ENV['variable1'] = 'env1';
        $_ENV['variable2'] = 'env2';

        $this->runCommand("project:copy-stub $input $output --env");

        $this->assertEquals(file_get_contents($expected), file_get_contents($output));
    }

    public function testCopyWithExported()
    {
        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';
        $expected = $this->expectedFolder('test-copy-with-exported');

        $container = $this->getApplication()->getContainer()->getPlugin('project')->getContainer();
        $container->set('variable3', 'value3');

        $this->runCommand("project:copy-stub $input $output --exported");

        $this->assertEquals(file_get_contents($expected), file_get_contents($output));
    }

    public function testCopyWithExportedAndEnv()
    {
        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';
        $expected = $this->expectedFolder('test-copy-with-exported-and-env');

        $_ENV['variable1'] = 'value1';
        $_ENV['variable2'] = 'value2';
        $container = $this->getApplication()->getContainer()->getPlugin('project')->getContainer();
        $container->set('variable3', 'value3');

        $this->runCommand("project:copy-stub $input $output --exported");

        $this->assertEquals(file_get_contents($expected), file_get_contents($output));
    }

    public function testAbortWhenFormatDoesNotContainDollarSymbol()
    {
        $this->expectException(CommandAbortedException::class);

        $input = $this->inputFolder() . '/copy-test.txt';
        $output = $this->outputFolder() . '/copy-test.txt';

        $this->runCommand("project:copy-stub $input $output --format=[]");
    }

    public function testSetInputPath()
    {

    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();

        $_ENV = ['APP_ENV' => 'testing'];
    }
}
