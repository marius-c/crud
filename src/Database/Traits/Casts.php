<?php namespace Ionut\Crud\Database\Traits;

trait Casts
{

    public function addCast($key, $cast)
    {
        $this->casts[$key] = $cast;
    }

    public function setCasts($casts)
    {
        $this->casts = $casts;
    }

}