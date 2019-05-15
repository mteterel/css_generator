<?php

function str_starts_with(string &$str, string $substr)
{
    if (strpos($str, $substr) === 0)
        return true;

    return false;
}

function str_ends_with(string &$str, string $substr)
{
    if (strpos($str, $substr) === (strlen($str) - strlen($substr)) - 1)
        return true;

    return false;
}

function str_starts_with_v(string &$str, string ...$substr)
{
    foreach ($substr as &$s)
        if (str_starts_with($str, $s))
            return true;

    return false;
}