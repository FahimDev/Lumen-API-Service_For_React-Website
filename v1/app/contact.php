<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
