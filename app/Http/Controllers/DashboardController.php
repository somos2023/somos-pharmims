<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Notification;
use App\Models\Order;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\StockResource;
use App\Http\Resources\NotificationResource;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $authData = $request->user(); 
        $data = null;

        if($authData->role_id != 3){
            $this->checkStocks();
            $this->checkProductsToExpire();
        }

        if($authData->role_id == 1){
            $data = $this->admin_data();
        } else if($authData->role_id == 2) {
            $data = $this->staff_data();           
        } else {
            $data = $this->supplier_data($authData->id);           
        }

        return response([
            'data' => $data
        ]);
    }

    private function admin_data(){
        $totalStaff = User::query()->where('role_id', '=', '2')->count();
        $totalSupplier = User::query()->where('role_id', '=', '3')->count();
        $totalProduct = Product::query()->where('role_id', '!=', '3')->count();
        $totalExpired = Stock::query()->where('status', '=', 'expired')->sum('quantity');
        $criticalStocks = $this->criticalStocks();
        $expiredProducts = $this->expiredProducts();
        $highSellingWeek = $this->highSelling([now()->startOfWeek(), now()->endOfWeek()]);
        $highSellingMonth = $this->highSelling([now()->startOfMonth(), now()->endOfMonth()]);
        $highSellingYear = $this->highSelling([now()->startOfYear(), now()->endOfYear()]);
        $lowSellingWeek = $this->lowSelling([now()->startOfWeek(), now()->endOfWeek()]);
        $lowSellingMonth = $this->lowSelling([now()->startOfMonth(), now()->endOfMonth()]);
        $lowSellingYear = $this->lowSelling([now()->startOfYear(), now()->endOfYear()]);

         $statsData = [
            [
                'icon' => "bx bx-user",
                'label' => "Total Staff",
                'number' => (int)$totalStaff,
                'link' => 'user-report'
            ],
            [
              'icon' => "bx bx-user",
              'label' => "Total Supplier",
              'number' => (int)$totalSupplier,
              'link' => 'user-report'
            ],
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Total Products",
              'number' => (int)$totalProduct,
              'link' => 'product-report'
            ],
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Expired Products",
              'number' => (int)$totalExpired,
              'link' => 'expired-products'
            ],
        ];

        $highSells = [
            'week' => $highSellingWeek,
            'month' => $highSellingMonth,
            'year' => $highSellingYear,
        ];

        $lowSells = [
            'week' => $lowSellingWeek,
            'month' => $lowSellingMonth,
            'year' => $lowSellingYear,
        ];

        return [
            "highsells" => $highSells ,
            "lowsells" => $lowSells,
            "stats" => $statsData,
            "criticals" => $criticalStocks,
            "expired" => $expiredProducts,
        ];
    }

    private function staff_data(){
        $totalStaff = User::query()->where('role_id', '=', '2')->count();
        $totalSupplier = User::query()->where('role_id', '=', '3')->count();
        $totalProduct = Product::query()->where('role_id', '!=', '3')->count();
        $totalExpired = Stock::query()->where('status', '=', 'expired')->sum('quantity');
        $criticalStocks = $this->criticalStocks();
        $expiredProducts = $this->expiredProducts();
        $highSellingWeek = $this->highSelling([now()->startOfWeek(), now()->endOfWeek()]);
        $highSellingMonth = $this->highSelling([now()->startOfMonth(), now()->endOfMonth()]);
        $highSellingYear = $this->highSelling([now()->startOfYear(), now()->endOfYear()]);
        $lowSellingWeek = $this->lowSelling([now()->startOfWeek(), now()->endOfWeek()]);
        $lowSellingMonth = $this->lowSelling([now()->startOfMonth(), now()->endOfMonth()]);
        $lowSellingYear = $this->lowSelling([now()->startOfYear(), now()->endOfYear()]);

        $statsData = [
            [
                'icon' => "bx bx-user",
                'label' => "Total Staff",
                'number' => (int)$totalStaff,
                'link' => 'user-list'
            ],
            [
              'icon' => "bx bx-user",
              'label' => "Total Supplier",
              'number' => (int)$totalSupplier,
              'link' => 'user-list'
            ],
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Total Products",
              'number' => (int)$totalProduct,
              'link' => 'product-list'
            ],
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Expired Products",
              'number' => (int)$totalExpired,
              'link' => 'expired-products'
            ],
        ];

        $highSells = [
            'week' => $highSellingWeek,
            'month' => $highSellingMonth,
            'year' => $highSellingYear,
        ];

        $lowSells = [
            'week' => $lowSellingWeek,
            'month' => $lowSellingMonth,
            'year' => $lowSellingYear,
        ];

        return [
            "highsells" => $highSells ,
            "lowsells" => $lowSells,
            "stats" => $statsData,
            "criticals" => $criticalStocks,
            "expired" => $expiredProducts,
        ];
    }

    private function supplier_data($authID){
        $totalProducts = Product::query()->where('role_id', '=', '3')->count();
        $totalOrders = Order::query()->where('supplier_id', $authID )->count();

        $statsData = [
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Total Products",
              'number' => $totalProducts,
              'link' => 'product-list'
            ],
            [
              'icon' => "bx bx-purchase-tag-alt",
              'label' => "Total Orders",
              'number' => $totalOrders,
              'link' => 'shop-orders'
            ],
        ];

         return [

            "stats" => $statsData,

        ];
    }

    private function checkStocks()
    {
        $stocks = Stock::where("status", "=", "available")->get();

        foreach ($stocks as $stock) {
            if ($stock->quantity === 0) {
                $stock->update(['status' => 'sold out']);
            }

            if ($stock['expires_at'] && now()->greaterThan($stock->expires_at)) {
                $stock->update(['status' => 'expired']);
            }
        }
    }

    private function criticalStocks()
    {
        // Define the critical stock threshold
        $criticalThreshold = 200; // Adjust this according to your requirement

        // Query products with critical stocks
        $productsWithCriticalStocks = Product::where("stock", "<=", 50)->where("role_id", "!=", 3)->where("deleted_flag", "!=", 1)->orderBy("stock", "ASC")->get();

        // Calculate stock percentage for each product
        // $productsWithCriticalStocks->transform(function ($product) use ($criticalThreshold) {
        //     $product->stock_percentage = $this->calculateStockPercentage($product->stock, $criticalThreshold);
        //     return $product;
        // });

        return $productsWithCriticalStocks;
    }

    public function getNotif(){
       $notifications = Notification::all();
        
        return response([
            'data' => NotificationResource::collection($notifications)
        ]);
    }

    public function changeNotifStatus(string $id){
        $resource = Notification::findOrFail($id);
        $resource->update([ 'status' => 'viewed']);
    }

    private function checkProductsToExpire(){
         // $oneWeekFromNow = Carbon::now()->addWeek();
        // $twoWeeksFromNow = Carbon::now()->addWeeks(2);
        $oneMonthFromNow = Carbon::now()->addMonth();

        $result = Stock::query()
            ->join('products as p', 'stocks.barcode', '=', 'p.barcode')
            ->where('stocks.quantity', '!=', '0')
            ->where('p.role_id', '!=', 3)
            ->where('stocks.status', '!=', 'expired')
            ->where('stocks.expires_at', '<=', $oneMonthFromNow)
            ->orderBy('stocks.expires_at')
            ->get([
                'stocks.*',
                'p.brand_name as brand_name',
                'p.formulation as formulation',
                'p.packing as packing',
                'p.image_url as image_url'
        ]);

        if (count($result) > 0) {
            foreach ($result as $item) {
                $findNotif = Notification::where('stock_id', $item['id'])->where('type', '=', 'expire_soon')->first();
                if(!$findNotif){
                    $notifData = [
                        'stock_id' => $item['id'],
                        'type' => 'expire_soon',
                        'message' => 'Products will expire soon!',
                    ];

                    $notification = new Notification;
                    $notification->fill($notifData);
                    $notification->save();
                }
                
            }
        }

    }

    private function expiredProducts()
    {
        $result = Stock::query()
            ->join('products as p', 'stocks.barcode', '=', 'p.barcode')
            ->where('stocks.quantity', '!=', '0')
            ->where('p.role_id', '!=', 3)
            ->where('stocks.status', '=', 'expired')
            ->orderBy('stocks.expires_at')
            ->get([
                'stocks.*',
                'p.brand_name as brand_name',
                'p.formulation as formulation',
                'p.packing as packing',
                'p.image_url as image_url'
            ]);

        if (!$result->isEmpty()) {
            // Prepare data for scatter chart
            $scatterData = $result->map(function ($item) {
                return [
                    'x' =>$this->formatDate($item->expires_at), // Adjust the date format as needed
                    'y' => $item->quantity,
                    'brand_name' => $item->brand_name,
                    'order_number' => $item->order_number,
                    'barcode' => $item->barcode,
                    'image' => $item->image_url ? URL::to($item->image_url) : URL::to('defaults/no-image.png'),
                ];
            });

            return $scatterData;
        }

        return null;
    }


    private function calculateStockPercentage($currentStock, $criticalThreshold, $decimalPlaces = 2)
    {
        // Ensure that the criticalThreshold is not zero to avoid division by zero
        if ($criticalThreshold != 0) {
            // Calculate the percentage of remaining stock
            $stockPercentage = ($currentStock / $criticalThreshold) * 100;

            // Ensure the percentage is within the range [0, 100]
            $stockPercentage = max(0, min(100, $stockPercentage));

            $stockPercentage = round($stockPercentage, $decimalPlaces);

            return $stockPercentage;
        }

        // Handle the case where criticalThreshold is zero to avoid division by zero
        return 100; // You may choose a different default value based on your requirements
    }

    private function highSelling($timeInterval)
    {

        $highSellingProducts = TransactionItem::select(
            'p.id',
            'p.brand_name',
            DB::raw('SUM(transaction_items.quantity) as totalQuantitySold')
        )
        ->join("products as p", "p.id", "=", "transaction_items.product_id")
        ->where("p.deleted_flag", "!=", 1)
        ->where('p.role_id', '!=', 3)
        ->whereBetween(DB::raw("DATE(transaction_items.created_at)"), $timeInterval) // Adjust for week
        ->groupBy('p.id', 'p.brand_name')
        ->orderByDesc('totalQuantitySold')
        ->take(10)
        ->get();


        if(!$highSellingProducts){
            return null;
        }

        $categoriesData = [];
        $seriesData = [];
        foreach ($highSellingProducts as $product) {
            $categoriesData[] = $product->brand_name;
            $seriesData[] = (int)$product->totalQuantitySold; 
        }

        $data = [
            'categories' => $categoriesData,
            'series' => $seriesData
        ];

        return $data;
    }

    private function lowSelling($timeInterval)
    {
        $lowSellingProducts = Product::select(
            'products.id',
            'products.brand_name',
            DB::raw('COALESCE(SUM(t.quantity), 0) as totalQuantitySold')
        )
            ->leftJoin("transaction_items as t", "t.product_id", "=", "products.id")
            ->where("products.deleted_flag", "!=", 1)
            ->where('products.role_id', '!=', 3)
            // ->where("t.quantity", "<=", 10)
            ->whereBetween(DB::raw("DATE(products.created_at)"), $timeInterval) // Adjust for week
            ->groupBy('products.id', 'products.brand_name') // Use the product table's id and brand_name
            ->orderBy('totalQuantitySold')
            ->take(10)
            ->get();

        if(!$lowSellingProducts){
            return null;
        }

        $categoriesData = [];
        $seriesData = [];
        foreach ($lowSellingProducts as $product) {
            $categoriesData[] = $product->brand_name;
            $seriesData[] = (int) $product->totalQuantitySold;
        }

        $data = [
            'categories' => $categoriesData,
            'series' => $seriesData
        ];

        return $data;
    }


    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }



    // private function getQuantitySoldPerInterval($productId, $dateColumn, $timeInterval)
    // {
    //     // Adjust the date range based on the $timeInterval
    //     switch ($timeInterval) {
    //         case 'week':
    //             $intervalFunction = 'WEEK'; // Adjust the interval function for week-based grouping
    //             break;
    //         case 'month':
    //             $intervalFunction = 'MONTH'; // Adjust the interval function for month-based grouping
    //             break;
    //         case 'year':
    //             $intervalFunction = 'YEAR'; // Adjust the interval function for year-based grouping
    //             break;
    //         default:
    //             // Default to MONTH if $timeInterval is not provided or invalid
    //             $intervalFunction = 'MONTH';
    //             break;
    //     }

    //     // Query to get the quantity sold per interval
    //     $quantitySoldPerInterval = TransactionItem::select(
    //         DB::raw('SUM(quantity) as quantitySold'),
    //         DB::raw("$intervalFunction(`{$dateColumn}`) as `interval`", Carbon::today()->week)
    //     )
    //     ->where('product_id', $productId)
    //     ->groupBy('interval')
    //     ->pluck('quantitySold', 'interval')
    //     ->toArray();






    //     // Fill in the missing intervals with zero
    //     $allIntervals = range(1, (int)now()->{$intervalFunction});
    //     $quantitySoldData = array_map(function ($interval) use ($quantitySoldPerInterval) {
    //         return $quantitySoldPerInterval[$interval] ?? 0;
    //     }, $allIntervals);

    //     return $quantitySoldData;
    // }


    // private function highSelling()
    // {
    //     // Use the SUM function to calculate the total quantity sold for each product
    //     $highSellingProducts = TransactionItem::select(
    //         'p.id',
    //         'p.brand_name',
    //         DB::raw('SUM(transaction_items.quantity) as totalQuantitySold')
    //     )
    //     ->join("products as p", "p.id", "=", "transaction_items.product_id")
    //     ->groupBy('p.id', 'p.brand_name')
    //     ->orderByDesc('totalQuantitySold')
    //     ->take(10) // Adjust the number of products you want to retrieve
    //     ->get();

    //     // Now $highSellingProducts contains the high-selling products
    //     return $highSellingProducts;
    // }

    // private function lowSelling()
    // {
    //     // Use the SUM function to calculate the total quantity sold for each product
    //     $lowSellingProducts = TransactionItem::select(
    //         'p.id',
    //         'p.brand_name',
    //         DB::raw('SUM(transaction_items.quantity) as totalQuantitySold')
    //     )
    //     ->join("products as p", "p.id", "=", "transaction_items.product_id")
    //     ->groupBy('p.id', 'p.brand_name')
    //     ->orderBy('totalQuantitySold')
    //     ->take(5) // Adjust the number of products you want to retrieve
    //     ->get();

    //     // Now $lowSellingProducts contains the low-selling products
    //     return $lowSellingProducts;
    // }


}
