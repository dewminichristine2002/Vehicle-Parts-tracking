<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class AdminSalesController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildSalesQuery($request);
        $sales = $query->paginate(10);

        // Convert date strings to Carbon instances
        $sales->getCollection()->transform(function ($item) {
            $item->date = \Carbon\Carbon::parse($item->date);
            return $item;
        });

        $dealers = Dealer::orderBy('company_name')->get();

        return view('admin.sales', [
            'sales' => $sales,
            'dealers' => $dealers,
            'search' => $request->all()
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->buildSalesQuery($request);
        $sales = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales_export_'.date('Y-m-d').'.csv"',
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Invoice No',
                'Date',
                'Dealer',
                'Customer',
                'Contact',
                'Vehicle No',
                'Make',
                'Model',
                'Amount'
            ]);

            // CSV data
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->invoice_no,
                    $sale->date ? \Carbon\Carbon::parse($sale->date)->format('d/m/Y') : '',
                    $sale->dealer_name,
                    $sale->customer_name,
                    $sale->contact_number,
                    $sale->vehicle_number,
                    $sale->make,
                    $sale->vehicle_model,
                    number_format($sale->grand_total, 2)
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function buildSalesQuery(Request $request)
    {
        $query = Invoice::with(['dealer'])
            ->select([
                'invoices.*',
                'dealers.company_name as dealer_name'
            ])
            ->join('dealers', 'invoices.dealer_id', '=', 'dealers.id');

        if ($request->filled('dealer_id')) {
            $query->where('invoices.dealer_id', $request->dealer_id);
        }

        if ($request->filled('vehicle_model')) {
            $query->where('invoices.vehicle_model', 'like', '%'.$request->vehicle_model.'%');
        }

        if ($request->filled('date')) {
            $query->whereDate('invoices.date', $request->date);
        }

        return $query->orderBy('invoices.date', 'desc');
    }
}