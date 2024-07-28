<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\TractorStatusUpdate;

class TrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Room::query();
        if($request->has('name')){
            $query->where('name','like','%' . $request->query('name') . '%');
        }
        $limit = $request->has('query') ? $request->query('query'): 5;
        $tractors = $query->where('user_id',auth()->user()->id)->paginate(intval($limit))->withQueryString();
       
        return inertia('Track/Index',[
            'queryLimit' => intval($limit),
            'queryName' => $request->has('name') ? $request->query('name') : null,
            'rooms' => $tractors,
        ]);
    }
    public function activity($id){
        $activities = TractorStatusUpdate::where('user_id',auth()->user()->id)->where('tractor_id',$id)->get();
        $grouped = $activities->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
        });
        return inertia('Track/Activity',[
            'activities' => $grouped->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
