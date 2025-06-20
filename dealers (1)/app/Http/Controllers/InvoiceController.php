<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Invoice;
use App\Models\SoldPart;
use App\Models\OtherCost;;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\DealerAuthController;
use Illuminate\Support\Facades\Auth;
use App\Models\GRNItem;
use App\Models\GlobalPart;
use App\Models\LocalStock;
use App\Models\VehicleModel;
use App\Http\Controllers\TargetController;




class InvoiceController extends Controller
{
   

    public function create()
{
    $dealerId = Auth::id();  // current logged-in dealer’s ID

    // Fetch distinct parts for this dealer from GRN items, joined with global parts info
    $parts = \App\Models\GRNItem::where('dealer_id', $dealerId)
          ->join('global_parts', 'grn_items.global_part_id', '=', 'global_parts.id')
          ->select('global_parts.id', 'global_parts.part_number', 
                   'global_parts.part_name', 'global_parts.price')
          ->distinct()
          ->get();

    // Pass the parts collection to the Blade view
    return view('invoices.create', compact('parts'));
}

    public function store(Request $request)
    {

        $dealer = Auth::guard('dealer')->user();
        $dealerId = $dealer->id;

        $vehicleNo = trim(strtolower($request->input('vehicle_number')));
        $newOdo = floatval($request->input('odo_value'));

 


        
        $odoCheck = $this->validateAndUpdateOdo($dealerId, $vehicleNo, $newOdo);
        
        if (!$odoCheck['status']) {
            return back()->with('error', $odoCheck['message']);
        }
        
if (!is_array($request->parts)) {
    return back()->withInput()->with('error', 'No parts submitted for the invoice.');
}

foreach ($request->parts as $part) {
    $partNumber = $part['part_number'];
    $requestedQty = $part['quantity'];

    // Get GlobalPart by part number
    $globalPart = GlobalPart::where('part_number', $partNumber)->first();

    if (!$globalPart) {
        return back()->withInput()->with('error', "Part number {$partNumber} not found in global parts.");
    }

    // Now check local stock using global_part_id and dealer_id
    $stock = LocalStock::where('global_part_id', $globalPart->id)
                       ->where('dealer_id', $dealerId)
                       ->first();

    if (!$stock || $stock->quantity < $requestedQty) {
        $availableQty = $stock ? $stock->quantity : 0;
        return back()->withInput()->with('error', "Insufficient stock for part number {$partNumber}. Only {$availableQty} available.");
    }
}


        // Sanitize input first
        $request->merge([
            'parts' => array_filter($request->parts ?? [], function ($item) {
                return isset($item['part_number'], $item['quantity']) && $item['quantity'] > 0;
            }),
            'other_costs' => array_filter($request->other_costs ?? [], function ($item) {
                return isset($item['description']) && $item['description'] !== '';
            }),
        ]);

        
    
        // Validation
        $request->validate([
            'invoice_no' => 'required|unique:invoices',
            'contact_number' => 'required|digits:10',
            'customer_name' => 'required|string|max:255',
            'vehicle_number' => 'required|string|max:20',
            'grand_total' => 'required|numeric|min:0',
            'date' => 'required|date',
            'parts' => 'required|array|min:1',
            'parts.*.part_number' => 'required|string',
            'parts.*.part_name' => 'required|string',
            'parts.*.quantity' => 'required|numeric|min:1',
            'parts.*.unit_price' => 'required|numeric|min:0',
            'parts.*.discount' => 'nullable|numeric|min:0|max:100',
            'parts.*.total' => 'required|numeric|min:0',
            'other_costs' => 'nullable|array',
            'other_costs.*.description' => 'required|string',
            'other_costs.*.price' => 'required|numeric|min:0',
            'vehicle_model' => 'required|string|max:255',
            'dto_value'     => 'nullable|numeric|min:0',
            'dto_type'      => 'nullable|string|max:100',
            'make' => 'required|string|max:255',
            'other_costs.*.discount' => 'nullable|numeric|min:0|max:100',
            'other_costs.*.total' => 'required|numeric|min:0',



        ]);
    
        DB::beginTransaction();
    
        try {
            $existingCustomer = Customer::where('contact_number', $request->contact_number)
                            ->where('dealer_id', $dealerId)
                            ->first();

            if (!$existingCustomer) {
                // Insert into customers table
                Customer::create([
                    'contact_number' => $request->contact_number,
                    'customer_name' => $request->customer_name,
                    'dealer_id'      => $dealerId,
                ]);
            }
    
            // Check if the vehicle exists under this dealer but with a different owner (ownership change case)
            $sameVehicleDiffOwner = CustomerVehicle::where('dealer_id', $dealerId)
                ->where('vehicle_number', $request->vehicle_number)
                ->where('contact_number', '!=', $request->contact_number)
                ->first();

            if ($sameVehicleDiffOwner) {
                // Delete the old row (ownership change)
                $sameVehicleDiffOwner->delete();
            }

            // Now check if the new vehicle+owner+dealer record already exists
            $alreadyExists = CustomerVehicle::where('dealer_id', $dealerId)
                ->where('vehicle_number', $request->vehicle_number)
                ->where('contact_number', $request->contact_number)
                ->first();

            if (!$alreadyExists) {
                // Insert the new ownership or new entry
                CustomerVehicle::create([
                    'dealer_id'      => $dealerId,
                    'vehicle_number' => $request->vehicle_number,
                    'contact_number' => $request->contact_number,
                    'make'           => $request->make,
                    'model'          => $request->vehicle_model,
                    'odo'            => $request->odo_value, 
                    'type'           => $request->odo_type,
                ]);
                
            }


            $invoice = Invoice::create([
                'invoice_no'     => $request->invoice_no,
                'customer_name'  => $request->customer_name,
                'contact_number' => $request->contact_number,
                'make' => $request->make,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_model'  => $request->vehicle_model,
                'odo_value'      => $request->odo_value, 
                'odo_type'       => $request->odo_type, 
                'grand_total'    => $request->grand_total,
                'date'           => $request->date,
                'dealer_id'      => $dealerId,
            ]);


            



            foreach ($request->other_costs ?? [] as $cost) {
                $price = $cost['price'];
                $discount = $cost['discount'] ?? 0;
                $discountAmount = ($price * $discount) / 100;
                $total = $price - $discountAmount;
            
                OtherCost::create([
                    'invoice_no' => $invoice->invoice_no,
                    'description' => $cost['description'],
                    'price' => $price,
                    'discount' => $discount,
                    'total' => $total,
                    'dealer_id' => $dealerId,
                ]);
            }
            

            $totalInvoiceExpense = 0;

            foreach ($request->parts as $part) {
                // Insert sold part
                SoldPart::create([
                    'invoice_no'  => $invoice->invoice_no,
                    'part_number' => $part['part_number'],
                    'part_name'   => $part['part_name'],
                    'quantity'    => $part['quantity'],
                    'unit_price'  => $part['unit_price'],
                    'discount'    => $part['discount'] ?? 0,
                    'total'       => $part['total'],
                ]);

            
                // 1. Calculate part expense
                $globalPart = GlobalPart::where('part_number', $part['part_number'])->first();
                if (!$globalPart) continue;
            
                $remainingQty = $part['quantity'];
                $partExpense = 0;
            
                $grnRows = DB::table('grn_items_duplicate')
                    ->where('dealer_id', $dealerId)
                    ->where('global_part_id', $globalPart->id)
                    ->orderBy('created_at')
                    ->get();
            
                foreach ($grnRows as $row) {
                    if ($remainingQty <= 0) break;

                    $useQty = min($remainingQty, $row->quantity);

                    if ($useQty > 0) {
                        $partExpense += $useQty * $row->grn_unit_price;
                        $remainingQty -= $useQty;

                        DB::table('sold_part_sources')->insert([
                            'invoice_no'    => $invoice->invoice_no,
                            'part_number'   => $part['part_number'],
                            'grn_id'        => $row->id,
                            'quantity_used' => $useQty
                        ]);
                    }
                }

            
                $totalInvoiceExpense += $partExpense;
            
                // 2. Then reduce the stock
                $remainingQty = $part['quantity'];
                foreach ($grnRows as $row) {
                    if ($remainingQty <= 0) break;
            
                    if ($row->quantity <= $remainingQty) {
                        $remainingQty -= $row->quantity;
                       
                        DB::table('grn_items_duplicate')
                        ->where('id', $row->id)
                        ->update([
                            'quantity' => 0,
                            'updated_at' => now()
                        ]);


                    } else {
                        DB::table('grn_items_duplicate')
                            ->where('id', $row->id)
                            ->update([
                                'quantity' => $row->quantity - $remainingQty,
                                'updated_at' => now()
                            ]);
                        $remainingQty = 0;
                    }
                }
            }
            
            // ✅ Only now, after loop finishes
            $invoice->update([
                'total_expense' => $totalInvoiceExpense,
            ]);
                $targetController = new \App\Http\Controllers\TargetController();
                $targetController->updateMonthlyAchievedTarget();
            DB::commit();
    
            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
     
    
    public function index(Request $request)
{
    $dealer = Auth::guard('dealer')->user(); // Get the logged-in dealer

    if (!$dealer) {
        return redirect()->route('login')->with('error', 'Unauthorized');
    }

    $query = Invoice::where('dealer_id', $dealer->id); //  Only dealer's invoices

    if ($request->filled('invoice_no')) {
        $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
    }

    if ($request->filled('vehicle_number')) { //this vehicle number..................
        $query->where('vehicle_number', 'like', '%' . $request->vehicle_number . '%');
    }

    if ($request->filled('date')) {
        $query->whereDate('date', $request->date);
    }

    $invoices = $query->orderBy('date', 'desc')->paginate(10);

    return view('invoices.index', compact('invoices'));
}


public function autocomplete(Request $request)
{
    $dealer = Auth::guard('dealer')->user();

    $results = Invoice::where('dealer_id', $dealer->id)
        ->where('invoice_no', 'like', '%' . $request->term . '%')
        ->pluck('invoice_no');

    return response()->json($results);
}

// Autocomplete for vehicle numbers ....................................
public function autocompleteVehicles(Request $request)
{
    $term = $request->get('term');
    $results = Invoice::where('vehicle_number', 'like', '%' . $term . '%')
        ->pluck('vehicle_number');
    return response()->json($results);
}


public function searchContacts(Request $request)
{
    $dealerId = Auth::guard('dealer')->id();
    $query = $request->q;

    $contacts = DB::table('customers')
        ->leftJoin('customer_vehicles', function ($join) use ($dealerId) {
            $join->on('customers.contact_number', '=', 'customer_vehicles.contact_number')
                 ->where('customer_vehicles.dealer_id', '=', $dealerId);
        })
        ->select(
            'customers.contact_number',
            'customers.customer_name',
            'customer_vehicles.vehicle_number',
            'customer_vehicles.make',
            'customer_vehicles.model'
            


        )
        ->where('customers.dealer_id', $dealerId)
        ->where(function ($q) use ($query) {
            $q->where('customers.contact_number', 'like', "%{$query}%")
              ->orWhere('customers.customer_name', 'like', "%{$query}%");
        })
        ->get();

    return response()->json($contacts);
}



public function searchVehicle(Request $request)
{
    $dealerId = Auth::guard('dealer')->id(); // Get the logged-in dealer ID
    $query = $request->query('q');

    $vehicles = CustomerVehicle::where('dealer_id', $dealerId)
        ->where('vehicle_number', 'like', $query . '%')
        ->get();

    $results = [];

    foreach ($vehicles as $vehicle) {
        // Only get the customer of this dealer
        $customer = Customer::where('dealer_id', $dealerId)
            ->where('contact_number', $vehicle->contact_number)
            ->first();

        $results[] = [
            'vehicle_number'  => $vehicle->vehicle_number,
            'contact_number'  => $vehicle->contact_number,
            'vehicle_model'  => $vehicle->model,
            'vehicle_make'  => $vehicle->make,
            'customer_name'   => $customer ? $customer->customer_name : '',
    
        ];
    }

    return response()->json($results);
}


public function otherCostSuggestions(Request $request)
{
    $q = $request->q;
    $dealerId = Auth::guard('dealer')->id(); // Get logged-in dealer's ID

    $suggestions = DB::table('other_costs')
        ->where('dealer_id', $dealerId)
        ->where('description', 'like', "%$q%")
        ->select('description', 'price')
        ->distinct()
        ->limit(10)
        ->get();

    return response()->json($suggestions);
}



    public function download($invoice_no)
{
    $invoice = \App\Models\Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();

    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
    return $pdf->download('Invoice_' . $invoice->invoice_no . '.pdf');
}


public function preview($invoice_no)
{
    $invoice = Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();
    return view('invoices.preview', compact('invoice'));
}


public function searchDealerParts(Request $request)
{
    $dealer = Auth::guard('dealer')->user();
    if (!$dealer) {
        return response()->json([], 401);
    }

    $dealerId = $dealer->id;
    $query = $request->q;

    // Step 1: Get global_part_ids ONLY used by this dealer in GRNItems
    $dealerPartIds = GRNItem::where('dealer_id', $dealerId)
        ->pluck('global_part_id')
        ->unique()
        ->toArray();
        

    //  DEBUG LOG: Just to verify what IDs you're getting
    // dd($dealerPartIds);

    // Step 2: Only return GlobalParts with those IDs and matching the query
    $matchingParts = GlobalPart::whereIn('id', $dealerPartIds)
        ->where(function ($q) use ($query) {
            $q->where('part_number', 'like', "%{$query}%")
              ->orWhere('part_name', 'like', "%{$query}%");
        })
        ->get();

    // Step 3: Format output
    $result = $matchingParts->map(function ($part) {
        return [
            'part_number' => $part->part_number,
            'part_name' => $part->part_name,
            'price' => $part->price,
        ];
    });

    return response()->json($result);
}



public function getFinanceSummary()
{
    $dealerId = Auth::guard('dealer')->id();

    $income = \App\Models\Invoice::where('dealer_id', $dealerId)
            ->where('status', 'Completed')
            ->sum('grand_total');

    $expense = \App\Models\Invoice::where('dealer_id', $dealerId)
    ->where('status', 'Completed')
    ->sum('total_expense');

    $profit = $income - $expense;

    return response()->json([
        'income' => number_format($income, 2),
        'expense' => number_format($expense, 2),
        'profit' => number_format($profit, 2),
    ]);
}
public function getVehicleModels(Request $request)
{
    $search = $request->input('query');

    $results = \App\Models\VehicleModel::where('vehicle_model', 'LIKE', "%{$search}%")
        ->orWhere('make', 'LIKE', "%{$search}%")
        ->limit(10)
        ->get(['vehicle_model', 'make']);

    return response()->json($results);
}


private function validateAndUpdateOdo($dealerId, $vehicleNo, $newOdo)
{
    $vehicle = \DB::table('customer_vehicles')
        ->where('dealer_id', $dealerId)
        ->where('vehicle_number', $vehicleNo)
        ->select('vehicle_number', 'odo')
        ->first();

    if ($vehicle) {
        $existingOdo = floatval($vehicle->odo);
        $newOdo = floatval($newOdo);

        \Log::info('ODO DEBUG', [
            'dealer' => $dealerId,
            'vehicle' => $vehicleNo,
            'newOdo' => $newOdo,
            'existingOdo' => $vehicle->odo ?? null
        ]);
        

        if ($newOdo < $existingOdo) {
            return [
                'status' => false,
                'message' => 'Invalid odometer reading. Please contact the help desk.'
            ];
        }

        if ($newOdo > $existingOdo) {
            \DB::table('customer_vehicles')
                ->where('dealer_id', $dealerId)
                ->where('vehicle_number', $vehicleNo)
                ->update(['odo' => $newOdo]);
        }
    }

    return ['status' => true]; // Vehicle doesn't exist or odo is valid
}

public function getInvoiceCountForDealer()
{
    $dealerId = Auth::id();

    if (!$dealerId) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $invoiceCount = \App\Models\Invoice::where('dealer_id', $dealerId)
                ->where('status', 'Completed')
                ->count();

    return response()->json(['count' => $invoiceCount]);
}

public function getMonthlyRevenue()
{
    try {
        $dealerId = Auth::id();

    $revenueData = DB::table('invoices')
        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(grand_total) as total'))
        ->where('dealer_id', $dealerId)
        ->where('status', 'Completed')
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy(DB::raw('MONTH(created_at)'))
        ->get();


        return response()->json($revenueData);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Query failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function getLatestInvoices()
{
    $dealerId = Auth::id();

    $latestInvoices = \App\Models\Invoice::where('dealer_id', $dealerId)
        ->where('status', 'Completed')
        ->orderByDesc('created_at')
        ->limit(5)
        ->get(['invoice_no', 'grand_total']);


    return response()->json($latestInvoices);
}

public function getTopSellingParts()
{
    $dealerId = Auth::id();

    $topParts = DB::table('sold_parts')
        ->join('invoices', 'sold_parts.invoice_no', '=', 'invoices.invoice_no')
        ->where('invoices.dealer_id', $dealerId)
        ->where('invoices.status', 'Completed') // ✅ filter only completed invoices
        ->select('sold_parts.part_name', DB::raw('SUM(sold_parts.quantity) as total_quantity'))
        ->groupBy('sold_parts.part_name')
        ->orderByDesc('total_quantity')
        ->limit(5)
        ->get();


    return response()->json($topParts);
}
    
public function fetchLowStockItems(): JsonResponse
{
    $dealerId = Auth::id();

    $items = DB::table('local_stocks')
        ->join('global_parts', 'local_stocks.global_part_id', '=', 'global_parts.id')
        ->where('local_stocks.dealer_id', $dealerId)
        ->where('local_stocks.quantity', '<=', 2)
        ->orderBy('local_stocks.quantity')
        ->limit(5)
        ->select('global_parts.part_name as part_name', 'local_stocks.quantity')
        ->get();

    return response()->json($items);
}

public function cancel($invoice_no)
{
    // Step 1: Mark invoice as Cancelled
    $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();
    $invoice->status = 'Cancelled';
    $invoice->save();

    // Step 2: Restore GRN quantities
    $sources = DB::table('sold_part_sources')
                ->where('invoice_no', $invoice_no)
                ->get();

    foreach ($sources as $source) {
        DB::table('grn_items_duplicate')
            ->where('id', $source->grn_id)
            ->increment('quantity', $source->quantity_used);
    }

        //  Step 3: Update target profit
    app(\App\Http\Controllers\TargetController::class)->updateMonthlyAchievedTarget();

    return redirect()->back()->with('success', 'Invoice cancelled and stock restored.');
}



}