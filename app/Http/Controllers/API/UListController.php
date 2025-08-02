<?php

namespace App\Http\Controllers\API;

use App\Models\UList;
use Illuminate\Http\Request;

class UListController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {/**/
        $ulist=UList::with('users')
        ->latest()
        ->get();
        $success['count'] =  $ulist->count();
        $success['UsersLists'] =  $ulist;
        return $this->sendResponse($success, 'Users Lists information.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $ulist = UList::with('users')->find($id);
        $success['UsersList'] =  $ulist;
        return $this->sendResponse($success, 'Users List information.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function edit(UList $uList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UList $uList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function destroy(UList $uList)
    {
        //
    }
}
