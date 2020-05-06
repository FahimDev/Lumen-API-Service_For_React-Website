<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class services extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
