<?php

function man_display_synopsis()
{
    echo "\e[1m" . "NAME\n";
    echo "\e[0m" . "\tcss_generator - sprite generator for HTML use\n\n";
    echo "\e[1m" . "SYNOPSIS\n";
    echo "\e[0m" . "\tcss_generator [OPTIONS]... assets_folder\n\n";
}

function man_display_description()
{
    echo "\e[1m" . "DESCRIPTION\n\n";
    echo "\e[0m" . "\tConcatenate all images inside a folder in one sprite and write a style sheet ready to use.\n";
    echo "\e[0m" . "\tMandatory arguments to long options are mandatory for short options too.\n\n";

    echo "\e[1m" . "\t-r, --recursive\n";
    echo "\e[0m" . "\t\tLook for images into the assets_folder passed as argument and all of its subdirectories.\n\n";

    echo "\e[1m" . "\t-i, --output-image=IMAGE\n";
    echo "\e[0m" . "\t\tName of the generated image. If blank, the default name is « sprite.png ».\n\n";

    echo "\e[1m" . "\t-s, --output-style=STYLE\n";
    echo "\e[0m" . "\t\tName of the generated stylesheet. If blank, the default name is « style.css ».\n\n";
}

function man_display_bonus()
{
    echo "\e[1m" . "BONUS OPTIONS\n\n";

    echo "\e[1m" . "\t-p, --padding=NUMBER\n";
    echo "\e[0m" . "\t\tAdd padding between images of NUMBER pixels.\n\n";

    echo "\e[1m" . "\t-o, --override-size=SIZE\n";
    echo "\e[0m" . "\t\tForce each images of the sprite to fit a size of SIZExSIZE pixels.\n\n";

    echo "\e[1m" . "\t-c, --columns-number=NUMBER\n";
    echo "\e[0m" . "\t\tThe maximum number of elements to be generated horizontally.\n\n";

    echo "\e[1m" . "\t-q, --prefix=STRING\n";
    echo "\e[0m" . "\t\tPreprends CSS classes by STRING followed by a dash.\n\n";
}

function man_display()
{
    man_display_synopsis();
    man_display_description();
    man_display_bonus();
    echo "\e[0m";
}