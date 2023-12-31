<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    public function fullname(): Attribute
    {
        return new Attribute(
            get:fn () => $this->name,
        );
    }

    public function username(): Attribute
    {
        return new Attribute(
            get:fn () => $this->email,
        );
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == 0){
                $html = '<span class="badge badge--success">'.trans("Open").'</span>';
            }
            elseif($this->status == 1){
                $html = '<span class="badge badge--primary">'.trans("Answered").'</span>';
            }
    
            elseif($this->status == 2){
                $html = '<span class="badge badge--warning">'.trans("Customer Reply").'</span>';
            }
            elseif($this->status == 3){
                $html = '<span class="badge badge--dark">'.trans("Closed").'</span>';
            }
            return $html;
        });
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'user_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'user_id', 'id');
    }

    public function supportMessage(){
        return $this->hasMany(SupportMessage::class);
    }

    public function getUser(): Attribute{
        return new Attribute(
            get:function(){
                if ($this->user_type == 'USER'){
                    $user = $this->user;
                }
                elseif($this->user_type == 'AGENT'){
                    $user = $this->agent;
                }
                elseif($this->user_type == 'MERCHANT'){
                    $user = $this->merchant;
                }

                return @$user;
            },
        );
    }

    public function goToUserProfile(): Attribute{ 
        return new Attribute( 
            get:function(){
                if($this->user_type == 'USER'){
                    $html = route('admin.users.detail', $this->user_id);
                }
                elseif($this->user_type == 'AGENT'){
                    $html = route('admin.agents.detail', $this->user_id);
                }
                elseif($this->user_type == 'MERCHANT'){
                    $html = route('admin.merchants.detail', $this->user_id);
                }
              
                return @$html;
            },
        );
    }
    

}
