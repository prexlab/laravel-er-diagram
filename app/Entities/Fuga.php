<?php
namespace App\Entities;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Fuga extends BaseEntity implements Transformable
{
    use TransformableTrait;

    protected $guarded = ['id'];

    public function hoges()
    {
        return $this->hasMany('App\Entities\Hoge');
    }
    
    public function piyo()
    {
        return $this->hasOne('App\Entities\Piyo');
    }
}
