<?php
require_once "SpriteGenerator.class.php";
require_once "cli_utils.php";
require_once "dir_utils.php";
require_once "str_utils.php";
require_once "manual.php";
require_once "logging.php";

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
        'input-dir' => null,
        'display-help' => false
    );
}

function parse_all_args(int $argc, array $argv)
{
    $result = get_default_args();
    for ($i = 1; $i < $argc; ++$i)
    {
        $arg = $argv[$i];
        if (str_starts_with_v($arg, '-r', '--recursive'))
            $result['recursive'] = true;
        elseif (str_starts_with_v($arg, '-i', '--output-image'))
            $result['output-image'] = arg_extract_string($arg, "sprite.png");
        elseif (str_starts_with_v($arg, '-s', '--output-style'))
            $result['output-style'] = arg_extract_string($arg, "style.css");
        elseif (str_starts_with_v($arg, '-p', '--padding'))
            $result['padding'] = arg_extract_integer($arg, 0);
        elseif (str_starts_with_v($arg, '-o', '--override-size'))
            $result['override-size'] = arg_extract_integer($arg, 0);
        elseif (str_starts_with_v($arg, '-c', '--columns_number'))
            $result['columns-number'] = arg_extract_integer($arg, 0);
        elseif (str_starts_with_v($arg, '-q', '--prefix'))
            $result['prefix'] = arg_extract_string($arg, '');
        elseif ($arg == '--help' || $arg == '-h')
            $result['display-help'] = true;
        else
            $result['input-dir'] = $arg;
    }
    return $result;
}

function validate_opts($options)
{
    if (empty($options['input-dir']))
        return [false, 'Unspecified asset directory.'];
    if (false == is_dir($options['input-dir']))
        return [false, 'Directory does not exists.'];
    if ($options['override-size'] < 0)
        return [false, 'Override SIZE must be positive.'];
    if ($options['padding'] < 0)
        return [false, 'Padding must be a positive value.'];

    return true;
}

function run_generation(array $files, array $options)
{
    $generator = new SpriteGenerator();
    $generator->set_override_size($options['override-size']);
    $generator->set_max_columns($options['columns-number']);
    $generator->set_padding($options['padding']);
    $generator->set_selector_prefix($options['prefix']);
    $generator->add_images($files);
    $generator->build_sprite($options['output-image']);
    $generator->export_stylesheet($options['output-style']);
}

function main(int $argc, array $argv): int
{
    $options = parse_all_args($argc, $argv);
    if ($options['display-help'])
    {
        man_display();
        return 0;
    }

    $validation = validate_opts($options);
    if (false === $validation[0])
    {
        print_error($validation[1]);
        print_info("Manual is available by using the --help parameter.");
        return 84;
    }

    $files = collect_png_files($options['input-dir'], $options['recursive']);
    if (empty($files))
    {
        print_info("No files to process.");
        return 0;
    }

    run_generation($files, $options);
    print_ok("Program completed.");
    return 0;
}

return main($argc, $argv);
