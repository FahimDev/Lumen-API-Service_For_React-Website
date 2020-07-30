<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_work extends Model
{
    protected $table = 'member-workhistory';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $dateFormat = 'U';
}