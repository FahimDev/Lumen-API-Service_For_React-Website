<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class about extends Model
{
    protected $table = 'about';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
