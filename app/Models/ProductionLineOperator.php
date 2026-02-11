<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLineOperator extends Model
{
    use HasFactory;

    protected $table = 'production_line_operator';
     protected     $fillable = [
        'line_id',
        'employee_id',
    ];

    public function employees(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    


}
