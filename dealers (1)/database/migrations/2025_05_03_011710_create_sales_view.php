<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            CREATE VIEW sales_view AS
            SELECT 
                i.invoice_no as id,
                i.invoice_no,
                i.customer_name,
                i.contact_number,
                i.vehicle_number,
                i.make,
                i.vehicle_model,
                i.date,
                i.grand_total,
                i.dealer_id,
                d.company_name as dealer_name
            FROM invoices i
            JOIN dealers d ON i.dealer_id = d.id
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS sales_view");
    }
};