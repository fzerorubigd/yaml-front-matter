<?php

namespace Cybits\Yaml;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FrontMatter, A simple static access
 *
 * @package Cybits\Yaml
 */
class FrontMatter
{
    /**
     * Load a YAML front matter included file (or string)
     *
     * @param string  $input                  Path to a YAML file or a string containing YAML
     * @param string  $separator              front matter separator, default is ---
     * @param Boolean $exceptionOnInvalidType True if an exception must be thrown on invalid types false otherwise
     * @param Boolean $objectSupport          True if object support is enabled, false otherwise
     *
     * @return array
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public static function parse($input, $separator = '---', $exceptionOnInvalidType = false, $objectSupport = false)
    {
        if (strpos($input, "\n") === false && is_file($input)) {
            if (false === is_readable($input)) {
                throw new ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $input));
            }

            $file = $input;
            $input = file_get_contents($file);
        }

        $input = str_replace(array("\r\n", "\r"), "\n", $input);
        $input = explode("\n", $input);
        $first = array_shift($input);
        if (trim($first) != $separator) {
            throw new ParseException("YAML Front matter must begin with $separator");
        }
        // There is a $separator in begin and another in end of front matter
        $yaml = array("---");
        while ($line = array_shift($input)) {
            if (trim($line) == $separator) {
                break;
            }
            $yaml[] = $line;
        }

        $yaml = Yaml::parse(implode("\n", $yaml), $exceptionOnInvalidType, $objectSupport);
        $text = implode("\n", $input);

        return array('yaml' => $yaml, 'text' => $text);
    }

    /**
     * Build YAML front matter included file.
     *
     * @param array   $array                    PHP array
     * @param         $text                     Extra text after front matter
     * @param string  $separator                YAML front matter separator
     * @param integer $inline                   The level where you switch to inline YAML
     * @param integer $indent                   The amount of spaces to use for indentation of nested nodes.
     * @param Boolean $exceptionOnInvalidType   true if an exception must be thrown on invalid types (a PHP resource or object), false otherwise
     * @param Boolean $objectSupport            true if object support is enabled, false otherwise
     *
     *
     * @return string
     */
    public static function dump($array, $text, $separator = "---", $inline = 2, $indent = 4, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        $yaml = Yaml::dump($array, $inline, $indent, $exceptionOnInvalidType, $objectSupport);

        return $separator . PHP_EOL . $yaml . $separator . PHP_EOL . $text;
    }
} 