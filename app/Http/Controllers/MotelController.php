<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\MotelResource;
use App\Http\Requests\StoreMotelRequest;
use App\Http\Requests\UpdateMotelRequest;

class MotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Motel::query();
        if($request->has('name')){
            $query->where('product_name','like','%' . $request->query('name') . '%');
        }
        $limit = $request->has('query') ? $request->query('query'): 5;
        $yards = $query->paginate(intval($limit))->withQueryString();
        return inertia('Motel/Index',[
            'roles' => auth()->user()->getRoleNames(),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
            'user' => auth()->user(),
            'queryLimit' => intval($limit),
            'queryName' => $request->has('name') ? $request->query('name') : null,
            'motels' => $yards
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::table('yards')->insert($request->all());
            return back();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Motel $motel)
    {
        return response()->json($motel);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Motel $motel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Motel $motel)
    {
        try {
            DB::table('yards')->where('id',$motel->id)->update($request->all());
            return back();
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Motel $motel)
    {
        $motel->delete();
        return back();
    }
}
