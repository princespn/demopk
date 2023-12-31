<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\UserPartials;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable{
    
    use HasApiTokens, HasFactory, UserPartials, Searchable;

    protected $table = "agents";
    
    protected $casts = [ 
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    protected $guarded = [];

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class,'user_id')->where('user_type','AGENT')->whereHas('currency',function($q){
            $q->where('status',1);
        })->with('currency');
    }

    public function qrCode()
    {
        return $this->hasOne(QRcode::class,'user_id')->where('user_type','AGENT');
    }

    public function login_logs()
    {
        return $this->hasMany(UserLogin::class,'agent_id');
    }
   
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'user_id')->orderBy('transactions.id','desc')->where('user_type','AGENT');
    }


    public function deposits()
    {
        return $this->hasMany(Deposit::class,'user_id')->where('status','!=',0)->where('user_type','AGENT');
    }
   
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class,'user_id')->where('status','!=',0)->where('user_type','AGENT');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('ev', 1)->where('sv', 1);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', 0);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', 0);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', 0);
    }
    public function scopeEmailVerified($query)
    {
        return $query->where('ev', 1);
    }

    public function scopeSmsVerified($query)
    {
        return $query->where('sv', 1);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', 0);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', 2);
    }

    public function scopeWithBalance($query){ 
        return $query->whereHas('wallets', function($wallet){
            $wallet->where('balance','>', 0);
        });
    }

    public function getDeviceTokens(){
        return $this->hasMany(DeviceToken::class, 'user_id');
    }

    public function deviceTokens(){
        return $this->getDeviceTokens()->where(function ($query){
            $query->where('user_type', 'AGENT');
        })->get();
    }
}
