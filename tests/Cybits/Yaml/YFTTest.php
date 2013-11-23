<?php

class YFTTest extends \PHPUnit_Framework_TestCase
{
    private $sample;

    public function setUp()
    {
        $this->sample = <<<EOT
---
Key: data
Other: data
---
Ok this is the real file content
EOT;
    }

    public function testFrontMatterString()
    {
        $result = \Cybits\Yaml\FrontMatter::parse($this->sample);
        $this->assertEquals("Ok this is the real file content", $result['text']);
        $this->assertEquals(array('Key' => 'data', 'Other' => 'data'), $result['yaml']);
    }

    public function testDumpString()
    {
        $result = \Cybits\Yaml\FrontMatter::parse($this->sample);
        $result = \Cybits\Yaml\FrontMatter::dump($result['yaml'], $result['text']);
        $this->assertEquals($this->sample, $result);
    }

    public function testFrontMatterFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'yft');
        file_put_contents($file, $this->sample);
        $result = \Cybits\Yaml\FrontMatter::parse($file);
        $result = \Cybits\Yaml\FrontMatter::dump($result['yaml'], $result['text']);
        $this->assertEquals($this->sample, $result);
        @unlink($file);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testInvalid()
    {
        $input = <<<EOT
Not a valid sep
Yaml: data
Yaml2 : data
EOT;
        \Cybits\Yaml\FrontMatter::parse($input);
    }

} 