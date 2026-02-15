<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Admission extends Model {
    protected $table = 'admissions';
    public $timestamps = false; 
    protected $fillable = ['reg_no', 'student_name', 'class_id', 'admission_date'];
}
