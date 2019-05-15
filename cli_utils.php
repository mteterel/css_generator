<?php

function arg_extract_integer(string $arg, $default)
{
    $result = arg_extract_string($arg, $default);
    if ($result === $default)
        return $default;
    if (false == is_numeric($result))
        return $default;
    return intval($result);
}

function arg_extract_string(string $arg, $default)
{
    $equals_pos = strpos($arg, '=');
    if (false === $equals_pos)
        return $default;

    $value = substr($arg, $equals_pos + 1);
    return $value;
}