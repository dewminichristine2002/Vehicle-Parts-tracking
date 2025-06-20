<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GRN;
use Illuminate\Http\Request;

class AdminGRNController extends Controller
{
    public function index(Request $request)
    {
        $grns = GRN::with(['dealer', 'items.globalPart'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('dealer', function($q) use ($request) {
                    $q->where('id', 'like', '%'.$request->search.'%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.grns', ['grns' => $grns]);
    }
}