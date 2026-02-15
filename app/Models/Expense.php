<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model {
    protected $table = 'account_exp_income_detail';
    public $timestamps = false;
    protected $fillable = ['id', 'category_id', 'amount', 'description'];
}
