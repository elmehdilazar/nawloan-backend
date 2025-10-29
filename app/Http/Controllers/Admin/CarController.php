<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CarsExport;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:cars_read'])->only('index');
        $this->middleware(['permission:cars_create'])->only('create');
        $this->middleware(['permission:cars_update'])->only('edit');
        $this->middleware(['permission:cars_enable'])->only('changeStatus');
        $this->middleware(['permission:cars_disable'])->only('changeStatus');
        $this->middleware(['permission:cars_export'])->only('export');
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
        if ($request->active == 1) {
            $trucks = Car::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->when($request->weight, function ($query) use ($request) {
                return $query->where('weight', 'like', '%' . $request->weight . '%');
            })->select()->inactive()->latest()->orderBy('name_en', 'desc')->paginate(10);
            return view('admin.trucks.index', ['trucks' => $trucks]);
        } elseif ($request->active == 2) {
            $trucks = Car::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->when($request->weight, function ($query) use ($request) {
                return $query->where('weight', 'like', '%' . $request->weight . '%');
            })->select()->active()->latest()->orderBy('name_en', 'desc')->paginate(10);
            return view('admin.trucks.index', ['trucks' => $trucks]);
        } elseif ($request->active == 0) {
            $trucks = Car::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->when($request->weight, function ($query) use ($request) {
                return $query->where('weight', 'like', '%' . $request->weight . '%');
            })->select()->latest()->orderBy('name_en', 'desc')->paginate(10);
            return view('admin.trucks.index', ['trucks' => $trucks]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.trucks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|unique:cars,name_en',
            'name_ar' => 'required|string|unique:cars,name_ar',
            'frames'  => 'required|integer|min:4',
            'weight'  => 'required|numeric|min:0',
            'image'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $request_data = $request->except(['image']);
        $request_data['image'] = 'uploads/cars/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/cars/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/cars/' . $request->image->hashName();
        }
        $truck = Car::create([
            'name_en' => $request_data['name_en'],
            'name_ar' => $request_data['name_ar'],
            'frames'  => (int) $request_data['frames'],
            'weight'  => $request_data['weight'],
            'image'   => $request_data['image'],
            'active'  => (int) $request_data['active'],
        ]);
            $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'truck',
            'link'  => route('admin.trucks.index', [ 'name_en' => $truck->name_en]),
            'target_id' => $truck->name_en,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.trucks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show(Car $car)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $truck=Car::find($id);
        if (!$truck) {
            session()->flash('errors', __('site.car_not_found'));
            return redirect()->route('admin.trucks.index');
        }
        return view('admin.trucks.edit',['truck'=> $truck]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $truck = Car::find($id);
        if (!$truck) {
            session()->flash('errors', __('site.car_not_found'));
            return redirect()->route('admin.trucks.index');
        }
        $request->validate([
            'name_en' => ['required','string', Rule::unique('cars')->ignore($truck->id)],
            'name_ar' => ['required','string', Rule::unique('cars')->ignore($truck->id)],
            'frames'  => 'required|integer|min:4',
            'weight'  => 'required|numeric|min:0',
            'image'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $request_data = $request->except(['image']);
        $request_data['image'] = 'uploads/cars/default.png';
        if ($request->image) {
            if ($truck->image != '' && $truck->image != 'uploads/cars/default.png') {
                if (file_exists(public_path($truck->image))) {
                    unlink(public_path($truck->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/cars/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/cars/' . $request->image->hashName();
        }
        $truck->update([
            'name_en'      =>  $request_data['name_en'],
            'name_ar'   =>  $request_data['name_ar'],
            'image'     =>  $request_data['image'],
            'frames'    =>  $request_data['frames'],
            'weight'    =>  $request_data['weight'],
            'active'    =>  $request_data['active'],
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'truck',
            'link'  => route('admin.trucks.index', ['name_en' => $truck->name_en]),
            'target_id' => $truck->name_en,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.trucks.index');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', $request->query('ids', []));
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, fn($id) => $id > 0));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        // Soft delete selected trucks (removes from listing without breaking FKs)
        \App\Models\Car::whereIn('id', $ids)->delete();
        return back()->with('success', __('site.deleted_success'));
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

        $truck = Car::select()->find($id);
        if (!$truck) {
            session()->flash('errors', __('site.car_not_found'));
            return redirect()->route('admin.trucks.index');
        }
        if ($truck->active == 1) {
            $truck->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'truck',
                'link'  => route('admin.trucks.index', ['name_en' => $truck->name_en]),
                'target_id' => $truck->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));

        } elseif ($truck->active == 0) {
            $truck->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'truck',
                'link'  => route('admin.trucks.index', ['name_en' => $truck->name_en]),
                'target_id' => $truck->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.trucks.index');
    }
    public function getById(Request $request){
        $car=Car::select('id','name_ar','name_en')->find($request->id);
        if(!$car){
            return response()->json([
                'truck' => null,
                'message' => Lang::get('site.car_not_found')
            ]);
        }
        return response()->json([
                'truck' => $car,
                'message' => ''
            ]);
    }
    public function export()
    {
        return Excel::download(new CarsExport,  Lang::get('site.cars') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
