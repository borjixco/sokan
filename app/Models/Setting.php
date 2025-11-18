<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $fillable = ['key', 'value'];

    public static function set($key,$value)
    {
        $value = is_array($value) ? json_encode($value) : $value;
        $setting = Setting::where('key',$key)->first();
        if($setting){
            $setting->update(['value' => $value]);
        }
        else{
            Setting::create(['key' => $key, 'value' => $value]);
        }
    }

    public static function get($key)
    {
        if(is_array($key)){
            $settings = Setting::whereIn('key',$key)->pluck('value','key');
            $result = $settings ?? null;
        }
        else{
            $setting = Setting::where('key',$key)->first();
            $result = $setting ? $setting->value : null;
        }
        return json_decode($result,true) ?? $result;
    }

    public static function remove($key)
    {
        return Setting::where('key',$key)->first()->delete();
    }
}
