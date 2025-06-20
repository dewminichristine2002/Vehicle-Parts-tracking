<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalPart;
use Illuminate\Http\Request;

class AdminPartController extends Controller
{
    public function index(Request $request)
    {
        $parts = GlobalPart::when($request->search, function($query) use ($request) {
                $search = $request->search;
                $query->where('part_number', 'like', '%'.$search.'%')
                    ->orWhere('part_name', 'like', '%'.$search.'%');
            })
            ->paginate(10);

        return view('admin.parts', ['parts' => $parts]);
    }

    public function update(Request $request, GlobalPart $part)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|max:255',
            'part_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $part->update($validated);

        return redirect()->route('admin.parts.index')
            ->with('success', 'Part updated successfully');
    }
}