<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Models\Motel;
use App\Models\Booking;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\VoucherResource;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role as ModelsRole;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $motels = Motel::all();
        $query = User::query();
        if($request->has('name')){
            $query->where('name','like','%' . $request->query('name') . '%');
        }
        $limit = $request->has('query') ? $request->query('query'): 5;
        $users = $query->with('yards')->paginate(intval($limit))->withQueryString();
        return inertia('User/Index',[
            'roles' => auth()->user()->getRoleNames(),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
            'queryLimit' => intval($limit),
            'queryName' => $request->has('name') ? $request->query('name') : null,
            'users' => $users,
            'motels' => $motels,
            'theRoles' => Role::all()
        ]);
    }
    public function store(Request $request)
    {

    }
    public function show($id)
    {
        $user = User::with('motels','roles')->findOrFail($id);
        return response()->json($user);
    }
    public function update(UpdateUserRequest $request, User $user, Motel $motel)
    {

        try{
            DB::beginTransaction();
            $user->motels()->sync($motel->id);
            $role = ModelsRole::findOrCreate($request->selectedRole);
            $user->syncRoles($role);
            $user->update($request->validated());
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
    }
    public function destroy(User $user)
    {
        $user->delete();
        return back();
    }

    public function home(Request $request)
    {
        $latestStatusUpdates = DB::table('tractor_status_updates as tsu')
        ->select('tsu.*')
        ->whereRaw('tsu.id IN (SELECT MAX(id) FROM tractor_status_updates GROUP BY tractor_id)');
        $tractors = DB::table('tractors')
        ->leftJoinSub($latestStatusUpdates, 'latest_status', function ($join) {
            $join->on('tractors.id', '=', 'latest_status.tractor_id');
        })
        ->select(
            'tractors.*', 
            'latest_status.status AS updated_status', 
            'latest_status.created_at AS updated_status_date'
        )
        ->where('tractors.user_id',auth()->user()->id)
        ->orderBy('tractors.tractor_number')  // Order by tractor_number
        ->get();
        $now = Carbon::now(env('TIMEZONE'))->format('Y-m-d');
        return inertia('User/Home',[
            'roles' => auth()->user()->getRoleNames(),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
            'rooms' => $tractors,
        ]);
    }
}
