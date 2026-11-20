<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['icebox_code', 'name', 'section', 'type_label'];

    public function bookings()
    {
        return $this->hasMany(ScheduleBooking::class);
    }

    public function publishedDays()
    {
        return $this->hasMany(PublishedDay::class);
    }
}