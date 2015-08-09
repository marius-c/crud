<?php namespace Ionut\Crud;

class Html
{

    static public function attr($attr)
    {
        $output = '';
        foreach ($attr as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $output .= $name.' ';
                }
            } else {
                if (!is_array($value)) {
                    $output .= sprintf('%s="%s"', $name, $value);
                }
            }
        }

        return $output;
    }
} 