<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'date', 'hours_worked', 'comments'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
