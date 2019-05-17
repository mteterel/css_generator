<?php

function print_error(string $str)
{
    echo "\e[1;31m[Error]:\e[0;37m $str\n";
}

function print_info(string $str)
{
    echo "\e[1;30m[Info]:\e[0;37m $str\n";
}

function print_ok(string $str)
{
    echo "\e[1;32m[OK]:\e[0;37m $str\n";
}