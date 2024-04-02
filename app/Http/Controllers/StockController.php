<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Http\Resources\StockResource;

class StockController extends Controller
{
    public function getStocks(Request $request){
    	$result = Stock::query()
          ->join('products as p', 'stocks.barcode', '=', 'p.barcode')
          // ->where('stocks.quantity', '!=', '0')
          ->where('p.role_id', '!=', 3)
          ->orderBy('stocks.created_at', 'DESC')
          ->get(['stocks.*', 
            'p.brand_name as brand_name',
          	'p.formulation as formulation',
            'p.packing as packing',
            'p.image_url as image_url'
          ]);

      return response([
        'data' => StockResource::collection($result)
      ]);
    }


}
