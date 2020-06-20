<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_hobby extends Model
{
    protected $table = 'member-hobby';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
