<?php namespace Ionut\Crud\Table\ColumnView;

use Ionut\Crud\Table\Column;

class FileColumnView extends AbstractColumnView {

    public function format($value)
    {
        $file = json_decode($value);
        if(!$file) {
            return;
        }

        return '<img src="'.$file->preview.'" width=100 height=80>';
    }
}