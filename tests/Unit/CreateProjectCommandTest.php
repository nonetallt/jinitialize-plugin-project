<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;

class CreateProjectCommandTest extends TestCase
{
    use CleansOutput;

    public function testCreatesProjectFolder()
    {
        $output = $this->outputFolder();

        /* User inputs the project folder path and project name */
        $input = [$output, 'test'];

        $this->runCommand('project:create', [], $input);

        $this->assertTrue(is_dir($output.'/test'));
    }

    public function testPathIsNotAskedIfProvidedAsParameter()
    {
        $output = $this->outputFolder();

        /* User inputs the name of the project */
        $input = ['test'];

        $this->runCommand("project:create --path=$output", [], $input);

        $this->assertTrue(is_dir($output.'/test'));
    }

    public function testExportsVariables()
    {
        $output = $this->outputFolder();

        /* User inputs the project folder path pointing to output */
        $input = [$output, 'test'];

        $this->runCommand('project:create', [], $input);

        $this->assertContainerEquals([
            'name'                => 'test',
            'parent'              => $output,
            'path'                => "$output/test",
            'last_created_folder' => "$output/test",
            'output_path'         => "$output/test/"
        ],
        'project');
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();
    }
}
