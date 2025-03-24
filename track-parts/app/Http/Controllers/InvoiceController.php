<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\SoldPart;
use App\Models\OtherCost;
use App\Models\BatchPart;
use App\Models\VehiclePart;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function create()
    {
        $parts = BatchPart::select('part_number', 'part_name')->distinct()->get();
        return view('invoices.create', compact('parts'));
    }

    public function store(Request $request)
    {
        // 🧪 Remove this after debugging
        // dd($request->all());
    
        // ✅ Sanitize input first (clean up empty entries)
        $request->merge([
            'parts' => array_filter($request->parts ?? [], function ($item) {
                return isset($item['part_number'], $item['quantity']) && $item['quantity'] > 0;
            }),
            'other_costs' => array_filter($request->other_costs ?? [], function ($item) {
                return isset($item['description']) && $item['description'] !== '';
            }),
        ]);
    
        // ✅ Validate after cleaning
        $request->validate([
            'invoice_no' => 'required|unique:invoices',
            'customer_name' => 'required',
            'discount' => 'required|numeric|min:0|max:100',
            'grand_total' => 'required|numeric|min:0',
            'date' => 'required|date',
            'parts' => 'required|array|min:1',
            'other_costs' => 'nullable|array',
        ]);
    
        DB::beginTransaction();
    
        try {
            // 🔸 Save main invoice
            $invoice = Invoice::create([
                'invoice_no' => $request->invoice_no,
                'customer_name' => $request->customer_name,
                'discount' => $request->discount,
                'grand_total' => $request->grand_total,
                'date' => $request->date,
            ]);
    
            // 🔹 Save sold parts
            foreach ($request->parts as $part) {
                SoldPart::create([
                    'invoice_no' => $invoice->invoice_no,
                    'part_number' => $part['part_number'],
                    'part_name' => $part['part_name'],
                    'quantity' => $part['quantity'],
                    'unit_price' => $part['unit_price'],
                    'total' => $part['total'],
                ]);
            }
    
            // 🔹 Save other costs (if any)
            if (!empty($request->other_costs)) {
                foreach ($request->other_costs as $cost) {
                    OtherCost::create([
                        'invoice_no' => $invoice->invoice_no,
                        'description' => $cost['description'],
                        'price' => $cost['price'],
                    ]);
                }
            }
    
            DB::commit();
    
            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    
    public function index()
    {
        $invoices = \App\Models\Invoice::orderByDesc('date')->get();
        return view('invoices.index', compact('invoices'));
    }

   

    public function download($invoice_no)
{
    $invoice = \App\Models\Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();

    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
    return $pdf->download('Invoice_' . $invoice->invoice_no . '.pdf');
}

public function show($invoice_no)
{
    $invoice = Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();
    return view('invoices.show', compact('invoice'));
}
public function preview($invoice_no)
{
    $invoice = Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();
    return view('invoices.preview', compact('invoice'));
}



    
}
