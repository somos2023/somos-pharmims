<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Http\Resources\OrderSalesResource;
use App\Http\Resources\TransactionSalesResource;
use Illuminate\Support\Facades\Auth;
use DB;

class ReportController extends Controller
{
    public function getOrderSalesData(Request $request)
    {
        $userID = Auth::user()->id;

        $salesData = [];
        $filtered = [];
        if(isset($request['start_date']) || isset($request['end_date'])) {
            $vdata = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|date_range:start_date',
            ], [
                'date_range' => 'The end date must be greater than the start date (:start_date).',
            ]);

            $timeInterval = [$vdata['start_date'], $vdata['end_date']];
            $salesData = Order::with('items.product')
            ->select(['id', 'order_number', 'total_quantity', 'grand_total', 'name', 'phone', 'address', 'note', 'status', 'created_at'])
            ->where('supplier_id', $userID)
            ->whereBetween(DB::raw("DATE(created_at)"), $timeInterval) // Adjust for week
            ->orderBy('created_at')
            ->get();

            $totalRevenue = $salesData->sum('grand_total');
            $totalUnitsSold = $salesData->sum('total_quantity');
            // Calculate Total Revenue
            $filtered['total_revenue'] = $totalRevenue;

            // Calculate Number of Units Sold
            $filtered['total_units_sold'] = $totalUnitsSold;

            // Calculate Average Transaction Value
            $filtered['average_transaction_value'] = ($totalUnitsSold > 0) ? $totalRevenue / $totalUnitsSold : 0;


        } else {
            $salesData = Order::with('items.product')
            ->select(['id', 'order_number', 'total_quantity', 'grand_total', 'name', 'phone', 'address', 'note', 'status', 'created_at'])
            ->where('supplier_id', $userID)
            ->get();
        }

        return response([
            'data' => OrderSalesResource::collection($salesData),
            'totals' => $filtered
        ]);

    }

    public function getTransactionSalesData(Request $request)
    {
        $userID = Auth::user()->id;

        $salesData = [];
        $filtered = [];
        if(isset($request['start_date']) || isset($request['end_date'])) {
            $vdata = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|date_range:start_date',
            ], [
                'date_range' => 'The end date must be greater than the start date (:start_date).',
            ]);

            $timeInterval = [$vdata['start_date'], $vdata['end_date']];

            $salesData = Transaction::with('items.product')
            ->select(['id', 'staff_id', 'transaction_number', 'total_quantity', 'grand_total', 'note', 'created_at'])
            ->whereBetween(DB::raw("DATE(created_at)"), $timeInterval) // Adjust for week
            ->orderBy('created_at')
            ->get();

            $totalRevenue = $salesData->sum('grand_total');
            $totalUnitsSold = $salesData->sum('total_quantity');
            // Calculate Total Revenue
            $filtered['total_revenue'] = $totalRevenue;

            // Calculate Number of Units Sold
            $filtered['total_units_sold'] = $totalUnitsSold;

            // Calculate Average Transaction Value
            $filtered['average_transaction_value'] = ($totalUnitsSold > 0) ? $totalRevenue / $totalUnitsSold : 0;

        } else {
            $salesData = Transaction::with('items.product')
            ->select(['id', 'staff_id', 'transaction_number', 'total_quantity', 'grand_total', 'note', 'created_at'])
            ->get();
        }

        return response([
            'data' => TransactionSalesResource::collection($salesData),
            'totals' => $filtered
        ]);
    }


}
