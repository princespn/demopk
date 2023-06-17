<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;

class WithdrawMethod extends Model
{
    use GlobalStatus;

   protected $guarded = ['id'];
    protected $table = "withdraw_methods";

    protected $casts = [
        'user_data' => 'object',
        'user_guards' => 'object',
        'currencies' => 'object',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    
    public function curr()
    {
        return Currency::find($this->currencies)->pluck('currency_code','id','rate');
    }

    public function withdrawMethod(){
        return $this->belongsTo(WithdrawMethod::class, 'method_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'accepted_currency');
    }

    public function scopeMyWithdrawMethod()
    {
        return UserWithdrawMethod::where('user_type',userGuard()['type'])->where('method_id', $this->id)->where('user_id',userGuard()['user']->id)->whereHas('withdrawMethod',function($query){
            $query->where('status',1)->whereJsonContains('user_guards',userGuard()['guard']);
        })->get();
    }
    public function minLimit(): Attribute{
        return new Attribute( 
            get:function(){
               return showAmount($this->withdrawMethod->min_limit / $this->currency->rate, $this->currency);
            }
        );
    }

    public function maxLimit(): Attribute{
        return new Attribute( 
            get:function(){
               return showAmount($this->withdrawMethod->max_limit / $this->currency->rate, $this->currency);
            }
        );
    }


   /* public function defaultcurrency()
    {
        return $this->belongsTo(Currency::class,'accepted_currency');
    }

    public function scopeMyWithdrawMethod()
    {
        return UserWithdrawMethod::where('user_type',userGuard()['type'])->where('method_id', $this->id)->where('user_id',userGuard()['user']->id)->whereHas('withdrawMethod',function($query){
            $query->where('status',1)->whereJsonContains('user_guards',userGuard()['guard']);
        })->get();
    }*/
}
