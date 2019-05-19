<?php

class SpriteGenerator
{
    private $images = array();
    private $override_size = null;
    private $max_columns = 0;
    private $output_width = 0;
    private $output_height = 0;
    private $padding_value = 0;
    private $output_png_path = null;
    private $selector_prefix = null;

    public function add_image(string $path)
    {
        if (!file_exists($path))
            return false;

        $image_size = getimagesize($path);
        array_push($this->images, array(
            'path' => $path,
            'width' => $image_size[0],
            'height' => $image_size[1]
        ));

        return true;
    }

    public function add_images(array $files)
    {
        foreach ($files as &$f)
            $this->add_image($f);
    }

    public function build_sprite(string $filename)
    {
        if (empty($this->images))
            return false;

        $this->compute_positions();
        $output = imagecreatetruecolor($this->output_width, $this->output_height);
        $this->make_transparent($output);

        foreach ($this->images as $img)
        {
            $src_img = imagecreatefrompng($img['path']);
            $dst_size = $this->compute_element_size($img);

            imagecopyresampled($output, $src_img,
                $img['pos_x'], $img['pos_y'],
                0, 0,
                $dst_size[0], $dst_size[1], $img['width'], $img['height']);

            imagedestroy($src_img);
        }

        imagepng($output, $filename);
        imagedestroy($output);
        $this->output_png_path = $filename;
        return true;
    }

    private function make_transparent($image)
    {
        $color = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $color);
        imagesavealpha($image, true);
    }

    private function compute_positions()
    {
        $current_width = $current_height = 0;
        $image_count = count($this->images);

        for ($i = 0; $i < $image_count; ++$i)
        {
            $src_img = &$this->images[$i];
            $dest_size = $this->compute_element_size($src_img);

            if ($this->max_columns > 0 && $i % $this->max_columns === 0)
            {
                $this->output_width = max($current_width, $this->output_width);
                $this->output_height += $current_height;
                $current_width = $current_height = 0;
            }

            $src_img['pos_x'] = $current_width;
            $src_img['pos_y'] = $this->output_height;
            $padding = $this->compute_padding($i, $image_count);
            $current_width += $dest_size[0] + $padding[0];
            $current_height = max($current_height + $padding[1],
                $dest_size[1] + $padding[1]);
        }

        $this->output_width = max($current_width, $this->output_width);
        $this->output_height += $current_height;
    }

    private function compute_element_size(array &$image)
    {
        if (!is_null($this->override_size))
            return $this->override_size;

        return array($image['width'], $image['height']);
    }

    private function compute_padding(int $curr_count, int $total_count)
    {
        $result_x = 0;
        $result_y = 0;
        $col_count = $this->max_columns;

        if ($this->padding_value > 0)
        {
            if (($col_count == 0 && $total_count > $curr_count + 1) ||
                ($col_count > 0 && $curr_count % $col_count < $col_count - 1))
                $result_x = $this->padding_value;

            if ($col_count > 0 && $curr_count < ($total_count - $col_count))
                $result_y = $this->padding_value;
        }

        return array($result_x, $result_y);
    }

    public function export_stylesheet(string $filename)
    {
        if (empty($this->images))
            return false;

        $handle = fopen($filename, "w+");
        foreach ($this->images as $image)
        {
            $image_size = $this->compute_element_size($image);

            fprintf($handle, "%s\n{\n",
                $this->generate_css_selector($image['path']));
            fprintf($handle, "\tdisplay: inline-block;\n");
            fprintf($handle, "\tbackground-image: url('%s');\n",
                $this->output_png_path);
            fprintf($handle, "\tbackground-position: -%dpx -%dpx;\n",
                $image['pos_x'], $image['pos_y']);
            fprintf($handle, "\tbackground-repeat: no-repeat;\n");
            fprintf($handle, "\twidth: %dpx;\n", $image_size[0]);
            fprintf($handle, "\theight: %dpx;\n", $image_size[1]);
            fprintf($handle, "}\n\n");
        }
        fclose($handle);
        return true;
    }

    private function generate_css_selector(string $path)
    {
        $result = '.';
        $info = pathinfo($path);

        if (!empty($this->selector_prefix))
            $result .= $this->selector_prefix . '-';

        $result .= $info['filename'];
        return $result;
    }

    public function set_override_size(int $size)
    {
        $this->override_size = $size > 0 ? array($size, $size) : null;
    }

    public function set_max_columns(int $max)
    {
        $this->max_columns = $max;
    }

    public function set_padding(int $new_padding)
    {
        $this->padding_value = $new_padding;
    }

    public function set_selector_prefix(string $prefix)
    {
        $this->selector_prefix = $prefix;
    }
}
