<?php
require_once "SpriteGenerator.class.php";
require_once "cli_utils.php";
require_once "dir_utils.php";
require_once "str_utils.php";

function collect_png_files(string $path, bool $recursive = false)
{
    $files = my_scandir($path, $recursive);
    $filtered = array();
    foreach ($files as &$f)
        if (strpos(strrev($f), strrev('.png')) === 0)
            array_push($filtered, $f);

    return $filtered;
}

function get_default_args()
{
    return array(
        'recursive' => false,
        'output-image' => 'sprite.png',
        'output-style' => 'style.css',
        'padding' => 0,
        'override-size' => 0,
        'columns-number' => 0,
        'prefix' => '',
        'input-dir' => '.'
    );
}

function parse_all_args(int $argc, array $argv)
{
    $result = get_default_args();
    for ($i = 1; $i < $argc; ++$i)
        $result = array_merge($result, parse_args($argv[$i]));
    return $result;
}

function parse_args(string $arg)
{
    if (str_starts_with_v($arg, '-r', '--recursive'))
        return ['recursive' => true];
    elseif (str_starts_with_v($arg, '-i', '--output-image'))
        return ['output-image' => arg_extract_string($arg, "sprite.png")];
    elseif (str_starts_with_v($arg, '-s', '--output-style'))
        return ['output-style' => arg_extract_string($arg, "style.css")];
    elseif (str_starts_with_v($arg, '-p', '--padding'))
        return ['padding' => arg_extract_integer($arg, 0)];
    elseif (str_starts_with_v($arg, '-o', '--override-size'))
        return ['override-size' => arg_extract_integer($arg, 0)];
    elseif (str_starts_with_v($arg, '-c', '--columns_number'))
        return ['columns-number' => arg_extract_integer($arg, 0)];
    elseif (str_starts_with_v($arg, '-q', '--prefix'))
        return ['prefix' => arg_extract_string($arg, '')];
    else
        return ['input-dir' => $arg];
}

function main(int $argc, array $argv): int
{
    $options = parse_all_args($argc, $argv);
    $input_dir = $options['input-dir'];
    $override_size = $options['override-size'];

    if (!is_dir($input_dir))
    {
        echo "Directory does not exists.\n";
        return 84;
    }

    $files = collect_png_files($input_dir, $options['recursive']);

    if (empty($files))
    {
        echo "No file(s) to process.\n";
        return 84;
    }

    $generator = new SpriteGenerator();
    if ($override_size > 0)
        $generator->set_override_size($override_size, $override_size);
    $generator->set_max_columns($options['columns-number']);
    $generator->set_padding($options['padding']);
    $generator->set_selector_prefix($options['prefix']);

    foreach ($files as &$f)
        $generator->add_image($f);

    $generator->build_sprite($options['output-image']);
    $generator->export_stylesheet($options['output-style']);
    return 0;
}

return main($argc, $argv);
