<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishedDay extends Model
{
    protected $fillable = ['facility_id', 'date', 'is_published'];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
    ];
}