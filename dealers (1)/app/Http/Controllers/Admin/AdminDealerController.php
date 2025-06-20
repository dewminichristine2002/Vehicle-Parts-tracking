<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDealerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $dealers = Dealer::withCount(['grns', 'localStocks'])
            ->when($search, function($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_email', 'like', "%{$search}%")
                      ->orWhere('company_mobile', 'like', "%{$search}%")
                      ->orWhere('user_contact', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search]);
            
        return view('admin.dealers', ['dealers' => $dealers, 'search' => $search]);
    }

    public function destroy(Dealer $dealer)
    {
        try {
            // Delete logo if exists
            if ($dealer->company_logo) {
                $path = str_replace('/storage', 'public', $dealer->company_logo);
                Storage::delete($path);
            }
            
            $dealer->delete();
            return redirect()->route('admin.dealers.index')
                ->with('success', 'Dealer deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.dealers.index')
                ->with('error', 'Error deleting dealer: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email,'.$dealer->id,
            'company_email' => 'required|email|unique:dealers,company_email,'.$dealer->id,
            'company_mobile' => 'required|string|max:20',
            'user_contact' => 'required|string|max:20',
        ]);

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($dealer->company_logo) {
                $oldPath = str_replace('/storage', 'public', $dealer->company_logo);
                Storage::delete($oldPath);
            }
            
            $path = $request->file('company_logo')->store('public/logos');
            $validated['company_logo'] = Storage::url($path);
        }

        $dealer->update($validated);

        return redirect()->route('admin.dealers.index')
            ->with('success', 'Dealer updated successfully');
    }

    public function toggleStatus(Dealer $dealer)
    {
        $dealer->is_active = !$dealer->is_active;
        $dealer->save();
        
        $status = $dealer->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.dealers.index')
            ->with('success', "Dealer has been {$status} successfully");
    }
}