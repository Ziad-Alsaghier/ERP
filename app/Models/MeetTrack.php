<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetTrack extends Model
{
    use HasFactory;

    protected $table = 'meet_tracks';

    // Mass-assignable attributes
    protected $fillable = [
        'name',
        'job_title',
        'company_name',
        'company_activites',
        'mobile',
        'email',
        'tech_solutions',
        'interest_level',
        'demo_request',
        'contact_time',
        'contact_method',
    ];
}
