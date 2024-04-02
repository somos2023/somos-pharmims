<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Stock;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authRole = $request->user()->role_id; 
        $Orderdata = null;
        if($myId){
            $getOrder = Order::where('staff_id', $myId)->orderBy("created_at", "DESC")->get();

            $Orderdata = [];
            
            if($getOrder){

                foreach ($getOrder as $order) {
                    $dataItems = [];
                    $getOrderitems = $this->getOrderItems($order['id']);
                    if($getOrderitems){
                        $dataItems = $getOrderitems;
                    } 
                    $order['items'] = $dataItems;
                    $Orderdata[] = $order;
                }
            }
        } 

        return response()->json([
            'data' => OrderResource::collection($Orderdata)
        ]);
    }

    public function getShopOrder(Request $request) {
        $myId = $request->user()->id; 
        $data = null;

        if($myId){
            $getOrder = Order::where('supplier_id', $myId)->orderBy("created_at", "DESC")->get();
            $Orderdata = [];
            
            if($getOrder){

                foreach ($getOrder as $order) {
                    $dataItems = [];
                    $getOrderitems = $this->getOrderItems($order['id']);
                    if($getOrderitems){
                        $dataItems = $getOrderitems;
                    } 
                    $order['items'] = $dataItems;
                    $Orderdata[] = $order;
                }
            }
        } 

        return response()->json([
            'data' => OrderResource::collection($Orderdata)
        ]);
    }

    public function getMyOrder(Request $request) {
        $myId = $request->user()->id; 
        $Orderdata = null;
        if($myId){
            $getOrder = Order::orderBy("created_at", "DESC")->get();

            $Orderdata = [];
            
            if($getOrder){

                foreach ($getOrder as $order) {
                    $dataItems = [];
                    $getOrderitems = $this->getOrderItems($order['id']);
                    if($getOrderitems){
                        $dataItems = $getOrderitems;
                    } 
                    $order['items'] = $dataItems;
                    $Orderdata[] = $order;
                }
            }
        } 

        return response()->json([
            'data' => OrderResource::collection($Orderdata)
        ]);
    }

    public function buyNow(StoreOrderRequest $request){
        $vdata = $request->validated();
        
        
        // if (!$order) {
        //     return response()->json(['message' => 'Place order failed'], 404);
        // }

        $saveData = [];

        foreach ($vdata['item'] as $orderItem) {
            // $itemVData = $this->buyNowValidate($orderItem);

            $vdata['order_number'] = $this->generateOrderNumber();
            $vdata['supplier_id'] =  $orderItem['supplier_id'];
            $vdata['total_quantity'] = $orderItem['quantity'];
            $vdata['grand_total'] = $orderItem['total'];

            $resource = new Order();
            $resource->fill($vdata); 
            $order = $resource->save();

            foreach ($orderItem['products'] as $productData) {
                $itemData['order_id'] = $resource->id;
                $itemData['product_id'] = $productData['id'];
                $itemData['expires_at'] = $productData['expires_at'];
                $itemData['price'] = $productData['price'];
                $itemData['quantity'] = $productData['quantity'];
                $itemData['subtotal'] = $productData['subtotal'];

                $product = new OrderItem();
                $product->fill($itemData);
                $saveProduct = $product->save();

                if(!$saveProduct){
                    $itemO = Order::find($orderItem->id);
                    $itemO->delete();
                    return response()->json(['message' => 'Place order failed'], 404);
                }

                $resource = Product::findOrFail($itemData['product_id']);
                $newStock = (int)$resource->stock - $itemData['quantity'];
                $resource->update(['stock' => $newStock]);
            }
        }

        return response()->json(['message' => 'Place order successfully!'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $vdata = $request->validated();

        

        // $order = false;
        foreach ($vdata['item'] as $supplierData) {
            $vdata['order_number'] = $this->generateOrderNumber();
            $vdata['supplier_id'] = $supplierData['supplier_id'];
            $vdata['total_quantity'] = $supplierData['quantity'];
            $vdata['grand_total'] = $supplierData['total'];

            // Create a new Order instance
            $order = new Order();

            // Save the order
            $order->fill($vdata);
            $saveOrder = $order->save();

            if (!$saveOrder) {
                return response()->json(['message' => 'Place order failed'], 404);
            }

            // Iterate through the products for the current supplier
            foreach ($supplierData['products'] as $productData) {
                $productData['order_id'] = $order->id;
                // Assuming you have a Product model
                $product = new OrderItem();

                $product->fill($productData);
                $saveProduct = $product->save();

                if(!$saveProduct){
                    $itemO = Order::find($order->id);
                    $itemO->delete();
                    return response()->json(['message' => 'Place order failed'], 404);
                }

                $resource = Product::findOrFail($productData['product_id']);
                $newStock = (int)$resource->stock - $productData['quantity'];
                $resource->update(['stock' => $newStock]);
            
                $item = Cart::find($productData['id']);
                $item->delete();
            }

            
        }

        


        
        

        return response()->json(['message' => 'Place order successfully!'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();

        if($user->role_id == 3){
            $check = Order::with('items.product')
            ->where('supplier_id', $user->id)
            ->where('id', $id)
            ->where('deleted_flag', '!=', 1)
            ->first();
            if(!$check){
                return response()->json([
                    'data' => null
                ]);
            }
        }

        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('deleted_flag', '!=', 1)
            ->first();

        return response()->json([
            'data' => new OrderDetailResource($order)
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id)
    {       
        $vdata = $request->validated();


        $resource = Order::findOrFail($id);
        $resource->update($vdata);

        if ($resource->wasChanged()) {
            $userRoleID = $request->user()->role_id;
            if($userRoleID != 3 && $vdata['status'] == 'Received'){
                $orderItems = $this->getOrderItems($id);
                $newProducts = [];

                if($orderItems){
                    foreach($orderItems as $item){
                        $newProducts['order_number'] = $resource->order_number;
                        $newProducts['barcode'] = $item['barcode'];
                        $newProducts['quantity'] = $item['quantity'];
                        $newProducts['purchase_price'] = $item['price'];
                        $newProducts['expires_at'] = $item['expires_at'];

                        $product = new Stock();
                        $product->fill($newProducts);
                        $saveProduct = $product->save(); 

                        $checkProduct = Product::where("role_id", '!=', 3)
                            ->where('barcode', $item['barcode'])
                            ->first();

                        if(!$checkProduct){
                            $newProductData = [
                               'role_id' => $userRoleID,
                               'user_id' => $request->user()->id,
                               'barcode' => $item['barcode'],
                               'brand_name' => $item['brand_name'],
                               'generic_name' => $item['generic_name'],
                               'formulation' => $item['formulation'],
                               'packing' => $item['packing'],
                               'price' => $item['price'],
                               'expires_at' => $item['expires_at'],
                            ];
                           
                           

                            $newProduct = new Product();
                            $newProduct->fill($newProductData);
                            $newProduct->save(); 
                        }
                    }  
                }               
            }

            return response()->json([
                'message' => 'Update successfully!',
            ], 200);
        } else {
            return response()->json(['message' => 'Nothing change!'], 200);
        } 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    private function generateOrderNumber()
    {
        $prefix = 'ORDER'; // You can set your desired prefix for the order number
        $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8); // Generate a random string
        $timestamp = now()->format('Ymd'); // Get current timestamp in a specific format

        $orderNumber = $prefix . '-' . $timestamp . '-' . $randomString;
        $randomNumber = substr(str_shuffle('0123456789'), 0, 15);
        // You can return the generated order number or use it according to your requirements
        return $randomNumber;
    }

    private function storeValidate($data)
    {
        $validator = Validator::make($data, [
            'product_id' => 'exists:products,id',
            'expires_at' => 'required|date',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ]);

        return $validator->validated();
    }

    private function buyNowValidate($data)
    {
        $validator = Validator::make($data, [
            'id' => 'exists:products,id',
            'expires_at' => 'required|date',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ]);

        return $validator->validated();
    }

    private function getOrderItems($id){
      $result = OrderItem::query()
        ->join('products as c', 'order_items.product_id', '=', 'c.id')
        ->orderBy('order_items.created_at', 'ASC')
        ->where('order_items.order_id', $id )
        ->get(['order_items.*', 
            'c.barcode as barcode',
            'c.brand_name as brand_name',
            'c.generic_name as generic_name',
            'c.formulation as formulation',
            'c.packing as packing',
            'c.description as description',
            'c.image_url as image_url',
        ]);
      return $result;
    }
}
