<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\DealerAuthController;
use App\Models\Target;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;

class TargetController extends Controller
{
    public function index()
    {
        $targets = Target::where('dealer_id', auth()->id())
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();

        return view('targets.index', compact('targets'));
    }

    public function create()
    {
        return view('targets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'target_amount' => 'required|numeric|min:0',
        ]);

        Target::create([
            'year' => $request->year,
            'month' => $request->month,
            'target_amount' => $request->target_amount,
            'dealer_id' => auth()->id(), // Attach logged-in dealer
        ]);

        return redirect()->route('targets.index')->with('success', 'Target created successfully.');
    }

    public function edit(Target $target)
    {
        if ($target->dealer_id !== auth()->id()) {
            abort(403); // Forbidden
        }

        return view('targets.edit', compact('target'));
    }

    public function update(Request $request, Target $target)
    {
        if ($target->dealer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'target_amount' => 'required|numeric|min:0',
        ]);

        $target->update($request->only(['year', 'month', 'target_amount']));

        return redirect()->route('targets.index')->with('success', 'Target updated successfully.');
    }

    public function destroy(Target $target)
    {
        if ($target->dealer_id !== auth()->id()) {
            abort(403);
        }

        $target->delete();

        return redirect()->route('targets.index')->with('success', 'Target deleted.');
    }

    public function updateMonthlyAchievedTarget()
{
    $dealerId = Auth::id();
    $now = Carbon::now();
    $year = $now->year;
    $month = $now->month;

    $income = Invoice::where('dealer_id', $dealerId)
        ->where('status', 'Completed')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('grand_total');


    $expense = Invoice::where('dealer_id', $dealerId)
        ->where('status', 'Completed')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('total_expense');


    $profit = $income - $expense;

    $target = Target::where('dealer_id', $dealerId)
        ->where('year', $year)
        ->where('month', $month)
        ->first();

    if ($target) {
        $target->achieved_amount = $profit;
        $target->save();
    }

    // No return
}

public function getTargetAchievementPercentageApi()
{
    $dealerId = auth()->guard('dealer')->id();
    $now = \Carbon\Carbon::now();

    $target = \App\Models\Target::where('dealer_id', $dealerId)
        ->where('year', $now->year)
        ->where('month', $now->month)
        ->first();

    $percentage = 0;

    if ($target && $target->target_amount > 0) {
        $percentage = ($target->achieved_amount / $target->target_amount) * 100;
    }

    return response()->json([
        'percentage' => round($percentage, 2)
    ]);
}

}
