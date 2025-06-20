<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\GRN;
use App\Models\GlobalPart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $dealerCount = Dealer::count();
        $grnCount = GRN::count();
        $partCount = GlobalPart::count();

        return view('admin.dashboard', [
            'dealerCount' => $dealerCount,
            'grnCount' => $grnCount,
            'partCount' => $partCount
        ]);
    }

    public function loginSessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->leftJoin('dealers', function($join) {
                $join->on('sessions.user_id', '=', 'dealers.id')
                     ->where('sessions.user_type', '=', 'dealer');
            })
            ->select(
                'sessions.id',
                'sessions.user_id',
                'dealers.name',
                'dealers.company_name',
                'dealers.email',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity'
            )
            ->whereNotNull('sessions.user_id')
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('dealers.id', 'like', '%'.$request->search.'%')
                      ->orWhere('dealers.name', 'like', '%'.$request->search.'%')
                      ->orWhere('dealers.email', 'like', '%'.$request->search.'%');
                });
            })
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate(10);

        return view('admin.login-sessions', ['sessions' => $sessions]);
    }
}