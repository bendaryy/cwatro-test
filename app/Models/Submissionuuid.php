<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submissionuuid extends Model
{
    protected $table="receiptsubmission";
     protected $casts = [
        'jsondata' => 'json'
    ];
    use HasFactory;
}
