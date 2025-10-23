<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Translation\Translator;
use App\Exports\CareersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Models\Career_category;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CareerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // create read update delete
        $this->middleware(['permission:careers_read'])->only('index');
        $this->middleware(['permission:careers_create'])->only('create');
        $this->middleware(['permission:careers_update'])->only('edit');
        $this->middleware(['permission:careers_enable'])->only('changeStatus');
        $this->middleware(['permission:careers_disable'])->only('changeStatus');
        $this->middleware(['permission:careers_export'])->only('export');
        $this->middleware(['permission:careers_disable'])->only('destroySelected');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (session('success')) {
            toast(session('success'), 'success');
        }
        if($request->has('active')){
            if ($request->active == 0) {

                $careers = Career::when($request->name_en, function ($query) use ($request) {
                    return $query->where('name_en', 'like', '%' . $request->name_en . '%');
                })->when($request->name_ar, function ($query) use ($request) {
                    return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
                })->when($request->category_id, function ($query) use ($request) {
                    return $query->where('category_id', 'like', '%' . $request->category_id . '%');
                })->select()->inactive()->latest()->orderBy('name_en', 'desc')->paginate(10);
                $categories = Career_category::select('id','category_ar','category_en')->get();
                return view('admin.careers.index', ['careers' => $careers ,'categories'=>$categories]);
            } elseif ($request->active == 1) {
                $careers = Career::when($request->name_en, function ($query) use ($request) {
                    return $query->where('name_en', 'like', '%' . $request->name_en . '%');
                })->when($request->name_ar, function ($query) use ($request) {
                    return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
                })->when($request->category_id, function ($query) use ($request) {
                    return $query->where('category_id', 'like', '%' . $request->category_id . '%');
                })->select()->active()->latest()->orderBy('name_en', 'desc')->paginate(10);
                $categories = Career_category::select('id','category_ar','category_en')->get();
                return view('admin.careers.index', ['careers' => $careers ,'categories'=>$categories]);
            } elseif ($request->active == 2) {
                $careers = Career::when($request->name_en, function ($query) use ($request) {
                    return $query->where('name_en', 'like', '%' . $request->name_en . '%');
                })->when($request->name_ar, function ($query) use ($request) {
                    return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
                })->when($request->category_id, function ($query) use ($request) {
                    return $query->where('category_id', 'like', '%' . $request->category_id . '%');
                })->select()->latest()->orderBy('name_en', 'desc')->paginate(10);
               $categories = Career_category::select('id','category_ar','category_en')->get();
                return view('admin.careers.index', ['careers' => $careers ,'categories'=>$categories]);
            }
        }else{
            $careers = Career::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->when($request->category_id, function ($query) use ($request) {
                return $query->where('category_id', 'like', '%' . $request->category_id . '%');
            })->select()->latest()->orderBy('name_en', 'desc')->paginate(10);
           $categories = Career_category::select('id','category_ar','category_en')->get();
            return view('admin.careers.index', ['careers' => $careers ,'categories'=>$categories]);
      

        }

    }


    // private function indexWithoutTrashed(Request $request)
    // {
    //     $carees = Career::when($request->has('name_ar'),function($query)use($request)
    //     {
    //         return $query->where('name_ar','like','%'.$request->name_ar.'%');
    //     })->when($request->has('name_en'),function($query)use($request)
    //     {
    //         return $query->Where('name_en','like','%'.$request->name_en.'%');
    //     })->when($request->has('address_en'),function($query)use($request)
    //     {
    //         return $query->Where('address_en','like','%'.$request->address_en.'%');
    //     })->when($request->has('address_ar'),function($query)use($request)
    //     {
    //         return $query->Where('address_ar','like','%'.$request->address_ar.'%');
    //     })->Where('caregory_id',$request->caregory_id)->
    //         andWhere('user_id',$request->user_id)->withTrashed()->latest()->get();
    //         return $careers ;
    // }

    // private function indexWithTrashed(Request $request)
    // {
    //     $carees = Career::when($request->has('name_ar'),function($query)use($request)
    //     {
    //         return $query->where('name_ar','like','%'.$request->name_ar.'%');
    //     })->when($request->has('name_en'),function($query)use($request)
    //     {
    //         return $query->Where('name_en','like','%'.$request->name_en.'%');
    //     })->when($request->has('address_en'),function($query)use($request)
    //     {
    //         return $query->Where('address_en','like','%'.$request->address_en.'%');
    //     })->when($request->has('address_ar'),function($query)use($request)
    //     {
    //         return $query->Where('address_ar','like','%'.$request->address_ar.'%');
    //     })->Where('caregory_id',$request->caregory_id)->
    //         andWhere('user_id',$request->user_id)->latest()->get();
    //         return $careers ;
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Career_category::all();
        return view('admin.careers.create',['categories'=>$categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CareerRequest $request)
    {

        DB::beginTransaction();
        $career = Career::create([
            'name_en'      =>  $request->name_en,
            'name_ar'      =>  $request->name_ar,
            'address_en'      =>  $request->address_en,
            'address_ar'      =>  $request->address_ar,
            'category_id'      =>  $request->category_id,
            'desc_en'      =>  $request->desc_en,
            'desc_ar'      =>  $request->desc_ar,
            'user_id'        => auth()->user()->id,
        ]);

                $data = [
                'title' => 'add',
                'body' => 'add_body',
                'target' => 'career',
                'link'  => route('admin.careers.index', [ 'name_en' => $career->name_en]),
                'target_id' => $career->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user) {
                Notification::send($user, new LocalNotification($data));
            }
            DB::commit();
            session()->flash('success', __('site.added_success'));
            return redirect()->route('admin.careers.index');



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $career = Career::find($id);
        if (!$career){
            session()->flash('errors', __('site.career_not_found'));
            return redirect()->route('admin.careers.index');
        }
        return view('admin.careers.show',['career'=> $career]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Career_category::all();
        $career=Career::find($id);
        if (!$career) {
            session()->flash('errors', __('site.career_not_found'));
            return redirect()->route('admin.careers.index');
        }
        return view('admin.careers.edit',['career'=> $career , 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(CareerRequest $request,$id)
    {
        // if($request->fails){
        //     return redirect()->back()->withErroe()->withInput($request->all);
        // }
        DB::beginTransaction();
        $career = Career::find($id);
        if (!$career) {
            session()->flash('errors', __('site.career_not_found'));
            return redirect()->route('admin.careers.index');
        }

        // if (!$request->has('active')) {
        //     $request->request->add(['active' => 0]);
        // } else {
        //     $request->request->add(['active' => 1]);
        // }
        $career->update([
            'name_en'      =>  $request->name_en,
            'name_ar'      =>  $request->name_ar,
            'address_en'      =>  $request->address_en,
            'address_ar'      =>  $request->address_ar,
            'catergory_id'      =>  $request->catergory_id,
            'desc_en'      =>  $request->desc_en,
            'desc_ar'      =>  $request->desc_ar,
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'career',
            'link'  => route('admin.careers.index', ['name_en' => $career->name_en]),
            'target_id' => $career->name_en,
            'sender' => auth()->user()->name,
        ];
        DB::commit();
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.careers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function destroy(Car $car)
    {
        //
    }
    public function changeStatus($id)
    {
        $career = Career::select()->find($id);
        if (!$career) {
            session()->flash('errors', __('site.career_not_found'));
            return redirect()->route('admin.careers.index');
        }
        if ($career->active == 1) {
            $career->update(['active' => 0]);
            $data = [
                'title' =>'disable',
                'body' => 'disable_body',
                'target' => 'career',
                'link'  => route('admin.careers.index', ['name_en' => $career->name_en]),
                'target_id' => $career->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($career->active == 0) {
            $career->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'career',
                'link'  => route('admin.careers.index', ['name_en' => $career->name_en]),
                'target_id' => $career->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.careers.index');
    }
    public function getById(Request $request){
        $career=Career::select('id','name_ar','name_en')->find($request->id);
        if(!$career){
            return response()->json([
                'career' => null,
                'message' => Lang::get('site.career_not_found')
            ]);
        }
        return response()->json([
                'career' => $career,
                'message' => ''
            ]);
    }
    public function export()
    {
        return Excel::download(new CareersExport,  Lang::get('site.careers') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }


    public function destroySelected(Request $request)
    {
        $ids = $request->query('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, function ($id) { return $id > 0; }));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        $deleted = Career::whereIn('id', $ids)->delete();
        if ($deleted < 1) {
            return back()->with('error', __('site.no_items_selected'));
        }

        return back()->with('success', __('site.deleted_success'));
    }
}
