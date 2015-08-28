<?php namespace Ionut\Crud;

class Compatibility
{

    protected $laravelVersion;

    public function __construct($laravelVersion)
    {
        $this->laravelVersion = $laravelVersion;
    }

}