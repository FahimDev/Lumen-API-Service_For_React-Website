<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_edu extends Model
{
    protected $table = 'member-academic';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
