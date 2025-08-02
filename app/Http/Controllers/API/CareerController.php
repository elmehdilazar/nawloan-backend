<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
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

class CareerController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        // create read update delete
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $careers = Career::when($request->name_en, function ($query) use ($request) {
            return $query->where('name_en', 'like', '%' . $request->name_en . '%');
        })->when($request->name_ar, function ($query) use ($request) {
            return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
        })->when($request->category_id, function ($query) use ($request) {
            return $query->where('category_id', $request->category_id);
        })->select()->active()->latest()->get();
        foreach ($careers as $key ) {
            $category = $key->category ;
            // $category = $key->user ;
        }
        $categories = Career_category::select('id','category_ar','category_en')->get();
        return $this->sendResponse([$careers,$categories], 'get all career success');

    }

    public function getcategories(){
       $categories = Career_category::select('id','category_ar','category_en')->get();
         return response()->json($categories);
    }

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
        return $this->sendResponse($career, 'add career success');

    }

    public function show($id)
    {
        $career = Career::find($id);
        if (!$career){
            return  response()->json('Error');
        }
        return $this->sendResponse($career, 'get career success');

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
        DB::beginTransaction();
        $career = Career::find($id);
        if (auth()->user()->id !=$career->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
            return response()->json('action is denied');
        }
        if (!$career) {
            return  response()->json('Error');
        }
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
       /* return  response()->json('success',200);*/ /*by mohammed*/
        return $this->sendResponse($career, 'update career success');

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
            return  response()->json('Error');
        }
        DB::beginTransaction();
        if ($career->active == 1) {
            if (auth()->user()->id !=$career->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ');
            }
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

        } elseif ($career->active == 0) {
            if (auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ' );
            }

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
        }
        DB::commit();
        return  response()->json('success',200);
    }


}

