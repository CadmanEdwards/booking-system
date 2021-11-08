<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Location;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $locations = Location::all();

        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
       
     $relations = [
            'rooms' => \App\Room::get()->pluck('room_name', 'id')
        ];
        
        return view('admin.locations.create',$relations);
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
        if (! Gate::allows('user_create')) {
            return abort(401);
        }

        $location = Location::create($request->all()); 
        $location->rooms()->attach($request->room_id);
        return redirect()->route('admin.locations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $location = Location::findOrFail($id);
        //$location = $location->prepend($location->rooms);
        $relations = [
            'rooms' => \App\Room::get()->pluck('room_name', 'id')
        ];
        return view('admin.locations.edit', compact('location')+$relations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $location = Location::findOrFail($id);
        $location->update($request->all()); 
        $location->rooms()->sync($request->room_id);
        return redirect()->route('admin.locations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $location = Location::findOrFail($id);
        $location->rooms()->detach();
        $location->delete();

        return redirect()->route('admin.locations.index');
    }
}
