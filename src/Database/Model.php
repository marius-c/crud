<?php namespace Ionut\Crud\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Ionut\Crud\Database\Traits\Casts;
use Ionut\Crud\Database\Traits\PropsKeeper;

class Model extends BaseModel
{
    use Casts;
    use PropsKeeper;

    protected $guarded = [];

    protected $casts = [];

}