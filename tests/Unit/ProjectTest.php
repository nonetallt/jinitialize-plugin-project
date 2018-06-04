<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\PluginContainer;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Plugin\Project\Project;

class ProjectTest extends TestCase
{
    use CleansOutput;

    private $project;
    private $stubsFolder;
    private $libraryRoot;

    public function testCreateStructure()
    {
        $this->project->createStructure([
            'level1' => [
                'level2' => [
                    'level3'
                ]
            ]
        ]);

        $str = str_replace('|', PHP_EOL, 'project|--level1|----level2|------level3|');
        $this->assertEquals($str, (string)$this->project->getFolders());
    }

    /**
     * Remove files generated from stubs from the output folder
     */
    public function setUp()
    {
        $this->cleanOutput();

        /* Create project in the output directory */
        $this->project = new Project($this->outputFolder() . '/project');
        $this->libraryRoot = dirname(dirname(__DIR__));
        $this->stubsFolder = $this->libraryRoot . '/stubs/plugin';
    }

    
}
