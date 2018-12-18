<?php
namespace App\Entities;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Hoge extends BaseEntity implements Transformable
{
    use TransformableTrait;

    protected $guarded = ['id'];

    public function fuga()
    {
        return $this->belongsTo('App\Entities\Fuga');
    }
}
