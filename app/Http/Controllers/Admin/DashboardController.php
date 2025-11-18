<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChargeResource;
use App\Models\Charge;
use App\Models\Contract;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Transfer;
use App\Models\Unit;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function redirectToDashboard()
    {
        return redirect()->route('admin.dashboard');
    }

    public function index()
    {
        Cache::flush();
        $thisMonth = verta()->format('F');
        return inertia('Admin/Dashboard/Index', compact('thisMonth'));
    }

    public function statsUnits()
    {
        return $this->getDashboardStat('units', function () {

            $counts = Unit::selectRaw("
                COUNT(CASE WHEN operation = 'EMPTY' THEN 1 END) as empty_count,
                COUNT(CASE WHEN operation IN ('RENTED', 'OWNER_OPERATED') THEN 1 END) as rented_owner_count
            ")->first();

            return ['empty' => $counts->empty_count, 'notEmpty' => $counts->rented_owner_count];
        }, '1001');
    }

    public function statsOwners()
    {
        return $this->getDashboardStat('owners', function () {

            $counts = Owner::selectRaw("
                COUNT(CASE WHEN status = 'CURRENT' THEN 1 END) as current,
                COUNT(CASE WHEN status = 'ARCHIVE' THEN 1 END) as archive
            ")->first();

            return ['current' => $counts->current, 'archive' => $counts->archive];
        }, '1002');
    }

    public function statsOccupants()
    {
        return $this->getDashboardStat('occupants', function () {
            $counts = Occupant::selectRaw("
                COUNT(CASE WHEN status = 'CURRENT' THEN 1 END) as current,
                COUNT(CASE WHEN status = 'ARCHIVE' THEN 1 END) as archive
            ")->first();
            return ['current' => $counts->current, 'archive' => $counts->archive];
        }, '1003');
    }

    public function statsContracts()
    {
        return $this->getDashboardStat('contracts', function () {
            $count = Contract::count();
            return ['total' => $count];
        }, '1004');
    }

    public function chartTransfers()
    {

        return $this->getDashboardStat('transfers', function () {
            $now = Verta::now();
            $startDate = $now->copy()->subMonths(11)->startMonth()->toCarbon();
            $endDate = $now->copy()->endMonth()->toCarbon();

            $transfers = Transfer::whereBetween('doing_at', [$startDate, $endDate])
                ->select('doing_at', 'type')
                ->get();

            $months = [];
            for ($i = 0; $i < 12; $i++) {
                $target = $now->copy()->subMonths(11 - $i);
                $key = $target->format('Y-m');
                $label = $target->format('F Y');

                $months[$key] = [
                    'label' => $label,
                    'rent_count' => 0,
                    'sale_count' => 0,
                ];
            }

            foreach ($transfers as $transfer) {
                $verta = Verta::instance($transfer->doing_at);
                $key = $verta->format('Y-m');

                if (!isset($months[$key])) {
                    continue;
                }

                if ($transfer->type === 'RENT') {
                    $months[$key]['rent_count']++;
                } elseif ($transfer->type === 'SALE') {
                    $months[$key]['sale_count']++;
                }
            }

            $months = collect(array_values($months));
            return ['months' => $months];
        }, '1005');


    }

    public function chartCharges()
    {

        return $this->getDashboardStat('charges', function () {
            $start = verta()->startMonth();
            $end = verta()->endMonth();
            $totals = DB::table('charges')
                ->whereBetween('created_at', [$start->toCarbon(), $end->toCarbon()])
                ->select(
                    DB::raw("CASE
            WHEN status = 'PAID' THEN 'paid'
            WHEN status IN ('UNPAID', 'NOT_SEND', 'SENDING', 'OVERDUE') THEN 'unpaid'
            ELSE 'other'
        END as status_group"),
                    DB::raw("SUM(amount) as total_amount")
                )
                ->groupBy('status_group')
                ->pluck('total_amount', 'status_group');

            $paid = isset($totals['paid']) ? (int)$totals['paid'] : 0;
            $unpaid = isset($totals['unpaid']) ? (int)$totals['unpaid'] : 0;
            $total = $paid + $unpaid;
            return ['paid' => $paid, 'unpaid' => $unpaid, 'total' => $total];
        }, '1006');

    }

    public function statsBill()
    {
        return $this->getDashboardStat('bills', function () {
            $start = verta()->startMonth();
            $end = verta()->endMonth();
            $totals = DB::table('bills')
                ->whereBetween('created_at', [$start->toCarbon(), $end->toCarbon()])
                ->select(
                    DB::raw("CASE
            WHEN status = 'PAID' THEN 'paid'
            WHEN status IN ('UNPAID', 'NOT_SEND', 'SENDING', 'OVERDUE') THEN 'unpaid'
            ELSE 'other'
        END as status_group"),
                    DB::raw("SUM(amount) as total_amount")
                )
                ->groupBy('status_group')
                ->pluck('total_amount', 'status_group');

            $paid = isset($totals['paid']) ? (int)$totals['paid'] : 0;
            $unpaid = isset($totals['unpaid']) ? (int)$totals['unpaid'] : 0;
            $total = $paid + $unpaid;
            return ['paid' => $paid, 'unpaid' => $unpaid, 'total' => $total];
        }, '1007');


    }

    public function reportsCharges()
    {

        $rows = Charge::with(['unit','user']);
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Charge::class));
        $rows = ChargeResource::collection($rows);
        return responseJSon('success', '', compact('rows'));
    }

    public function getDashboardStat(string $key, \Closure $callback, string $errorCode)
    {
        try {
            $value = Cache::get("admin.dashboard.$key", []);
            if (!empty($value)) {
                return responseJSon('success', '', $value);
            }

            // اجرای callback برای ساختن مقدار
            $data = $callback();
            Cache::put("admin.dashboard.$key", $data, now()->addMinutes(15));
            //sleep(2);
            return responseJSon('success', '', $data);

        } catch (\Exception $e) {
            return responseJSon('error', "خطا در فراخوانی سرویس ($errorCode)");
        }
    }

}
