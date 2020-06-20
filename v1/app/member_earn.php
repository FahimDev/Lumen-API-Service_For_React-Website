<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_earn extends Model
{
    protected $table = 'member-achievement';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}