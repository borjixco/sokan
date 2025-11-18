<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingDevController extends Controller
{

    public function index()
    {
        return inertia('Admin/Settings/Dev/Index');
    }

    public function sms()
    {
        $drivers = smsDrivers();
        $driversData = Setting::get('admin.sms');
        $driver = null;
        $token = null;
        if(isset($driversData['active']) ){
            $driver = $driversData['active'];
            $token = $driversData[$driversData['active']]['token'];
        }
        return inertia('Admin/Settings/Dev/Sms', compact('drivers','driver', 'token', 'driversData'));
    }

    public function updateSms(Request $request)
    {
        try {
            $request->validate([
                'driver' => 'required'
            ]);
            $settingObj = new Setting;
            $sms = $settingObj->get('admin.sms');
            $sms[$request->driver] = [
                'driver' => $request->driver,
                'token' => $request->token,
            ];
            $sms['active'] = $request->driver;
            $settingObj->set('admin.sms',$sms);
            return redirectMessage('success','با موفقت ذخیره شد',$sms);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }
    }
}
