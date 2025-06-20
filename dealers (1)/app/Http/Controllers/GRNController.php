<?php

namespace App\Http\Controllers;

use App\Models\GlobalPart;
use App\Models\LocalStock;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\GRN;
use App\Models\GRNItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added this line to fix the error

class GRNController extends Controller
{
    public function index()
    {
        $dealer = Auth::guard('dealer')->user();
        $globalParts = GlobalPart::all();
        $localStocks = LocalStock::where('dealer_id', $dealer->id)->get()->keyBy('global_part_id');

        return view('dealer.grn', compact('globalParts', 'localStocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_date' => 'required|date',
            'invoice_number' => 'required|string|max:50',
            'parts.*.global_part_id' => 'required|exists:global_parts,id',
            'parts.*.new_quantity' => 'required|integer|min:1',
            'parts.*.grn_unit_price' => 'required|numeric|min:0',
        ]);
    
        $dealer = Auth::guard('dealer')->user();
        $grnNumber = $request->grn_number ?? $this->generateGRNNumber();
    
        $grn = GRN::create([
            'dealer_id' => $dealer->id,
            'grn_number' => $grnNumber,
            'invoice_number' => $request->invoice_number,
            'grn_date' => $request->grn_date
        ]);
    
        foreach ($request->parts as $part) {
            $localStock = LocalStock::firstOrNew([
                'dealer_id' => $dealer->id,
                'global_part_id' => $part['global_part_id'],
            ]);
    
            GRNItem::create([
                'grn_id' => $grn->id,
                'global_part_id' => $part['global_part_id'],
                'quantity' => $part['new_quantity'],
                'grn_unit_price' => $part['grn_unit_price'],
                'dealer_id' => $dealer->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        return redirect()->route('grn.index')
            ->with('success', 'GRN created successfully!')
            ->with('grn_id', $grn->id);
    }

    private function generateGRNNumber()
    {
        $date = now();
        return 'GRN-' . $date->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }

    public function history()
    {
        $dealer = Auth::guard('dealer')->user();

        $grns = GRN::where('dealer_id', $dealer->id)
            ->with(['items' => function($query) {
                $query->with('globalPart');
            }])
            ->latest()
            ->get();

        return view('dealer.grn_history', compact('grns'));
    }

    public function generatePDF($id)
    {
        $grn = GRN::with(['items.globalPart', 'dealer'])->findOrFail($id);
        $pdf = Pdf::loadView('dealer.grn_pdf', compact('grn'));

        return $pdf->download("GRN_{$grn->grn_number}.pdf");
    }

    public function viewStock(Request $request)
    {
        $dealer = Auth::guard('dealer')->user();
        
        $query = DB::table('grn_items_duplicate')
            ->join('global_parts', 'grn_items_duplicate.global_part_id', '=', 'global_parts.id')
            ->where('grn_items_duplicate.dealer_id', $dealer->id)
            ->select(
                'global_parts.part_number',
                'global_parts.part_name',
                DB::raw('SUM(grn_items_duplicate.quantity) as total_quantity')
            )
            ->groupBy('global_parts.part_number', 'global_parts.part_name');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('global_parts.part_number', 'like', "%$search%")
                  ->orWhere('global_parts.part_name', 'like', "%$search%");
            });
        }
        
        if ($request->has('quantity_filter')) {
            switch ($request->quantity_filter) {
                case 'min':
                    $query->having('total_quantity', '<=', 5);
                    break;
                case 'max':
                    $query->having('total_quantity', '>=', 10);
                    break;
                // 'all' will show all quantities
            }
        }
        
        $stocks = $query->paginate(10);
        
        return view('dealer.stock', compact('stocks'));
    }
    
    public function details(GRN $grn)
    {
        $grn->load(['items.globalPart']);
        
        return response()->json([
            'grn_number' => $grn->grn_number,
            'invoice_number' => $grn->invoice_number,
            'grn_date' => $grn->grn_date->format('d/m/Y'),
            'items' => $grn->items->map(function($item) {
                return [
                    'quantity' => $item->quantity,
                    'grn_unit_price' => $item->grn_unit_price,
                    'global_part' => [
                        'part_number' => $item->globalPart->part_number,
                        'part_name' => $item->globalPart->part_name,
                        'price' => $item->globalPart->price
                    ]
                ];
            })
        ]);
    }
}