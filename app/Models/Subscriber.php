<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = ['email'];
}
