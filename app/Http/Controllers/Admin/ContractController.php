<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Http\Resources\DocumentResource;
use App\Models\Category;
use App\Models\Contract;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContractController extends Controller
{

    private function categories()
    {
        $categories = Category::where('model_type', Contract::class)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }

    public function index(Request $request)
    {
        $search = $request->search;
        $rows = Contract::query();
        if($search){
            $rows = $rows->where('title','LIKE', "%$search%")
                ->orWhere('company','LIKE', "%$search%")
                ->orWhere('tel','LIKE', "%$search%");
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Contract::class));
        $rows = ContractResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Contracts/Index', compact('rows', 'perPages'));
    }

    public function create()
    {
        $years  = years();
        $months = months();
        $days   = days();
        return inertia('Admin/Contracts/Create',compact('years', 'months', 'days'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title'       => 'required|min:3',
                'company'     => 'nullable',
                'cost'        => 'nullable|numeric',
                'terms'       => 'nullable',
                'guarantee'   => 'nullable',
                'tel'         => 'nullable|numeric',
                'description' => 'nullable',
                'start_year'  => 'nullable|numeric',
                'start_month' => 'nullable|numeric',
                'start_day'   => 'nullable|numeric',
                'end_year'    => 'nullable|numeric',
                'end_month'   => 'nullable|numeric',
                'end_day'     => 'nullable|numeric',
                //'status'      => '',
            ],
            [
                'title'   => 'عنوان را به درستی وارد نمایید',
                'cost'    => 'مبلغ را به درستی وارد نمایید',
                'tel'   => 'تلفن تماس را به درستی وارد نمایید',
            ]);
            $start_at = Verta::parse("$request->start_year-$request->start_month-$request->start_day")->toCarbon();
            $end_at = Verta::parse("$request->end_year-$request->end_month-$request->end_day")->toCarbon();
            $contract = Contract::create([
                'title'       => $request->title,
                'company'     => $request->company,
                'cost'        => $request->cost,
                'terms'       => $request->terms,
                'guarantee'   => $request->guarantee,
                'tel'         => $request->tel,
                'description' => $request->description,
                'start_at'    => $start_at,
                'end_at'      => $end_at,
            ]);
            return redirectMessage('success','با موفقیت ثبت شد', null, route('admin.contracts.edit',$contract->id));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function edit(Contract $contract, Request $request)
    {
        $contractOrg = $contract;
        $contract = new ContractResource($contract);
        $years  = years();
        $months = months();
        $days   = days();

        $modelType = Contract::class;
        $documents = DocumentResource::collection($contractOrg->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $categories = $this->categories();
        return inertia('Admin/Contracts/Edit', compact('contract',
        'years', 'months', 'days', 'modelType', 'categories', 'documents'));
    }

    public function update(Contract $contract, Request $request)
    {
        try {
            $request->validate([
                'title'       => 'required|min:3',
                'company'     => 'nullable',
                'cost'        => 'nullable|numeric',
                'terms'       => 'nullable',
                'guarantee'   => 'nullable',
                'tel'         => 'nullable|numeric',
                'description' => 'nullable',
                'start_year'  => 'nullable|numeric',
                'start_month' => 'nullable|numeric',
                'start_day'   => 'nullable|numeric',
                'end_year'    => 'nullable|numeric',
                'end_month'   => 'nullable|numeric',
                'end_day'     => 'nullable|numeric',
                //'status'      => '',
            ],
            [
                'title'   => 'عنوان را به درستی وارد نمایید',
                'cost'    => 'مبلغ را به درستی وارد نمایید',
                'tel'   => 'تلفن تماس را به درستی وارد نمایید',
            ]);
            $start_at = Verta::parse("$request->start_year-$request->start_month-$request->start_day")->toCarbon();
            $end_at = Verta::parse("$request->end_year-$request->end_month-$request->end_day")->toCarbon();
            $contract->update([
                'title'       => $request->title,
                'company'     => $request->company,
                'cost'        => $request->cost,
                'terms'       => $request->terms,
                'guarantee'   => $request->guarantee,
                'tel'         => $request->tel,
                'description' => $request->description,
                'start_at'    => $start_at,
                'end_at'      => $end_at,
            ]);
            return redirectMessage('success','با موفقیت ثبت شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }
}
