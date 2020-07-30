<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_url extends Model
{
    protected $table = 'member-url';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $dateFormat = 'U';
}
