<?php

function my_scandir(string $path, bool $recursive = false)
{
    $result = array();
    $handle = opendir($path);
    while ($file = readdir($handle))
    {
        $full_path = $path . '/' . $file;

        if ($recursive && is_dir($file) &&
            $file !== '.' && $file !== '..')
            $result = array_merge($result, my_scandir($full_path));
        else
            array_push($result, $full_path);
    }
    closedir($handle);
    return $result;
}