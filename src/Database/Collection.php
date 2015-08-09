<?php namespace Ionut\Crud\Database;

class Collection extends \Illuminate\Database\Eloquent\Collection
{

    public function htmlOptions($key = 'title', $selected = null)
    {
        $options = $this->options($key);

        return implode(array_map(function ($v, $k) use ($selected) {
            if ($k == $selected) {
                return '<option value="'.$k.'" selected>'.$v.'</option>';
            }

            return '<option value="'.$k.'">'.$v.'</option>';
        }, $options, array_keys($options)));
    }

    public function options($title = 'title', $key = 'id')
    {
        $notNull = function ($v) {
            return $v != '';
        };

        return array_combine(
            $this->map(function ($v) use ($key) {
                return $v->$key;
            })->filter($notNull)->toArray(),
            $this->map(function ($v) use ($title) {
                return $v->{$title};
            })->filter($notNull)->toArray()
        );
    }
}