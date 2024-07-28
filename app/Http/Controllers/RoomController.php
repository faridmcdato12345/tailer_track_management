<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\StoreRepairRoomRequest;
use App\Models\Booking;
use App\Models\TractorStatusUpdate;
use Carbon\Carbon;

class RoomController extends Controller
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
       
        return inertia('Room/Index',[
            'queryLimit' => intval($limit),
            'queryName' => $request->has('name') ? $request->query('name') : null,
            'rooms' => $tractors,
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
    public function store(StoreRoomRequest $request)
    {
        try {
            auth()->user()->tractors()->create($request->validated());
            return back();
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return response()->json($room);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        try {
            DB::beginTransaction();
            $room->update($request->all());
            $bookings = Booking::where('room_id',$room->id)->where('status','checked_in')->get();
            foreach ($bookings as $booking) {
                if($booking->check_out_date !== $request->manual_check_out){
                    $booking->update(['manual_check_out' => $request->manual_check_out, 'status' => 'checked_out']);
                }else{ 
                    $booking->update(['status' => 'checked_out', 'manual_check_out' => $request->manual_check_out]);
                }
            }
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return back();
    }

    public function repair(StoreRepairRoomRequest $request)
    {
        try {
            DB::beginTransaction();
            auth()->user()->repair_rooms()->create([
                'start_of_repair' => Carbon::parse($request->start_of_repair)->format('Y-m-d'),
                'end_of_repair' => Carbon::parse($request->end_of_repair)->format('Y-m-d'),
                'availability_date' => Carbon::parse($request->availability_date)->format('Y-m-d'),
                'room_id' => $request->room_id,
            ]);
            Room::where('id',$request->room_id)->update(['status' => 'Out of Service']);
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
    }

    public function checkoutUpdate(Request $request, Room $room)
    {
        try {
            $now = Carbon::now(env('TIMEZONE'));
            DB::beginTransaction();
            $room->update($request->all());
            $bookings = Booking::where('room_id',$room->id)->where('status','checked_in')->get();
            foreach ($bookings as $booking) {
                if($booking->check_out_date === $now->format('Y-m-d')){
                    $booking->update(['status' => 'checked_out','manual_check_out' => $now->format('Y-m-d')]);
                }
            }
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }
    public function updateViaForm(Request $request, Room $room)
    {
        try {
            DB::beginTransaction();
            DB::table('tractors')->where('id', $room->id)->update($request->all());
            TractorStatusUpdate::create([
                'user_id' => auth()->user()->id,
                'tractor_id' => $room->id,
                'status' => $request->status
            ]);
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }

    public function availableRooms()
    {
        return response()->json(Room::where('status','Available')
        ->where('user_id',auth()->user()->id)
        ->whereColumn('capacity_status','!=','maximum_capacity')
        ->orderBy('room_number','asc')->get());
    }

    public function roomVoucherDetails(Room $room)
    {
        $bookings = Booking::where('room_id',$room->id)
                            ->where('status','checked_in')
                            ->whereNull('manual_check_out')
                            ->get();
        return response()->json($bookings);
    }
}
