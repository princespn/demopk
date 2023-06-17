<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;

class Gateway extends Model
{

    //use GlobalStatus;

    protected $casts = [
        'status' => 'string',
        'code' => 'string',
        'extra' => 'object',
        'input_form'=> 'object',
        'user_guards' => 'object',
        'currencies' => 'object',
        'supported_currencies'=>'object'

    ];


protected $casts = [
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function currency()
    {
        return Currency::find($this->currencies)->pluck('currency_code','id');
    }


    public static function changeStatus($id, $column = 'Status')
    {
        $modelName = get_class();

        $query     = $modelName::findOrFail($id);
        if ($query->status == 1) {
            $query->status = 0;
        } else {
            $query->status = 1;
        }
        $message       = $column. ' changed successfully';

        $query->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }


    public function currencies()
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function singleCurrency()
    {
        return $this->hasOne(GatewayCurrency::class, 'method_code', 'code')->orderBy('id','desc');
    }

    public function scopeCrypto()
    {
        return $this->crypto == 1 ? 'crypto' : 'fiat';
    }

    public function scopeAutomatic($query)
    {
        return $query->where('code', '<', 1000);
    }

    public function scopeManual($query)
    {
        return $query->where('code', '>=', 1000);
    }
}
