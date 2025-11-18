<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Jobs\SendSmsForEventJob;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $rows = Event::with('user');
        if($search){
            $rows = $rows
                ->where('title','LIKE',"%$search%")
                ->orWhere('location','LIKE',"%$search%")
                ->orWhere('short_description','LIKE',"%$search%")
                ->orWhere('description','LIKE',"%$search%");
        }
        $rows = $rows->orderByDesc('created_at')->paginate(auth()->user()->perPage('admin',Event::class));
        $rows = EventResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Events/Index', compact('rows', 'perPages'));
    }

    public function create(Request $request)
    {
        $years   = years(true);
        $months  = months();
        $days    = days();
        $hours   = hours();
        $minutes = minutes();
        return inertia('Admin/Events/Create', compact('years', 'months', 'days', 'hours', 'minutes'));
    }

    public function review(Request $request)
    {
        try {
            $request->validate([
                'title'    => 'required|string|min:3',
                'location' => 'required|string|min:5',
                'year'     => 'required|numeric',
                'month'    => 'required|numeric',
                'day'      => 'required|numeric',
                'hour'     => 'required|numeric',
                'min'      => 'required|numeric',
            ],
                [
                    'title'    => 'عنوان را به درستی وارد نمایید',
                    'location' => 'مکان را به درستی وارد نمایید',
                    'year'     => 'سال را به درستی وارد نمایید',
                    'month'    => 'ماه را به درستی وارد نمایید',
                    'day'      => 'روز را به درستی وارد نمایید',
                    'hour'     => 'ساعت را به درستی وارد نمایید',
                    'min'      => 'دقیقه را به درستی وارد نمایید',
                ]);
            $eventAt = "{$request->year}/{$request->month}/{$request->day}";
            $min = strlen($request->min) === 1 ? '0'.$request->min : $request->min;
            $hour = "$request->hour:$min";
            $text = <<<EOT
با سلام رویداد $request->title در تاریخ $eventAt و ساعت $hour برگزار می شود.
$request->short_description
مکان برگزاری: $request->location
EOT;

        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }

        return redirectMessage('success',null,['sms' => $text]);
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $request->validate([
                'title'    => 'required|string|min:3',
                'location' => 'required|string|min:5',
                'year'     => 'required|numeric',
                'month'    => 'required|numeric',
                'day'      => 'required|numeric',
                'hour'     => 'required|numeric',
                'min'      => 'required|numeric',
            ],
            [
                'title'    => 'عنوان را به درستی وارد نمایید',
                'location' => 'مکان را به درستی وارد نمایید',
                'year'     => 'سال را به درستی وارد نمایید',
                'month'    => 'ماه را به درستی وارد نمایید',
                'day'      => 'روز را به درستی وارد نمایید',
                'hour'     => 'ساعت را به درستی وارد نمایید',
                'min'      => 'دقیقه را به درستی وارد نمایید',
            ]);
            $eventAt = verta()->parse("{$request->year}-{$request->month}-{$request->day} {$request->hour}:{$request->min}")->toCarbon();
            $event = Event::add([
                'user_id'           => $user->id,
                'title'             => $request->title,
                'location'          => $request->location,
                'event_date'        => $eventAt,
                'short_description' => $request->short_description,
                'description'       => $request->description,
            ]);


            $users = User::query()
            ->with(['roles', 'owners', 'occupants'])
            ->whereHas('roles', function ($q) {
                // بررسی نقش‌های مختلف
                $q->whereIn('name', ['supervisor', 'employee', 'occupant', 'owner', 'operator', 'admin', 'superadmin']);
            })
            ->where(function ($query) {
                // بررسی نقش‌های occupant و owner با وضعیت current
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['occupant', 'owner']);
                    })
                        ->where(function ($subQuery) {
                            // وضعیت current برای occupant و owner
                            $subQuery->whereHas('occupants', function ($q) {
                                $q->where('status', 'current');
                            })
                                ->orWhereHas('owners', function ($q) {
                                    $q->where('status', 'current');
                                });
                        });
                })
                // سایر نقش‌ها که وضعیت current ندارند
                ->orWhere(function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['supervisor', 'employee', 'operator', 'admin', 'superadmin']);
                    });
                });
            });

            $count = $users->count();
            // استفاده از chunk برای پردازش دسته‌ای رکوردها
            $users->chunk(100, function ($users) {
                // پردازش هر دسته از رکوردها (به تعداد 100 در هر بار)
                foreach ($users as $user) {
                    // عملیات مورد نظر را برای هر کاربر انجام دهید
                    // برای مثال: ارسال جاب به صف یا پردازش
                    dispatch(new SendSmsForEventJob($user));
                }
            });


            return redirectMessage('success',"رویداد ایجاد شد و $count رکورد در صف اجرا می باشد");

        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }
}
