<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCartRequest;
use App\Http\Resources\CartResource;


class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $userId = $request->user()->id;
        $cartData = $this->cartQuery($userId);

        return response()->json([
            'data' => $cartData
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        $vdata = $request->validated();
        $vdata['product_id'] = $vdata['product'];
        unset($vdata['product']);

        $checkExist = Cart::where('user_id', $vdata['user_id'])
            ->where('product_id', $vdata['product_id'])
            ->first();
        $cart = false;
        if($checkExist){
            $vdata['quantity'] = (int)$checkExist->quantity += 1;
            $resource = Cart::findOrFail($checkExist->id);
            $cart = $resource->update($vdata);
        } else {
            $supplier = Product::where("id", $vdata['product_id'])->first();
            $vdata['supplier_id'] = $supplier->user_id;
            $resource = new Cart();
            $resource->fill($vdata); 
            $cart = $resource->save();
        }
        
        if (!$cart) {
            return response()->json(['message' => 'Add to cart failed'], 404);
        }

        return response()->json(['message' => 'Added to Cart Successfully!'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        $resource = Cart::find($id);

        if (!$resource) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        
        $data = $this->cartQuery($id);

        return response([
            'data' => CartResource::collection([$data[0]])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vdata = $request->validate([
            'quantity' => 'required|numeric',
        ]);

        $resource = Cart::findOrFail($id);
        $resource->update($vdata);

        if ($resource->wasChanged()) {
            return response()->json([
                'message' => 'Updated successfully!',
            ], 200);
        } else {
            return response()->json(['message' => 'Nothing change!'], 200);
        } 

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Cart::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully!'], 200);
    }

    private function cartQuery($userId=""){
        $cart = null;
        if(!empty($userId)){
            $cart = Cart::query()
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('users as s_user', 'carts.supplier_id', '=', 's_user.id')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->where('users.id', $userId)
            ->where('products.deleted_flag', '!=', 1)
            ->orderBy("carts.created_at", "ASC")
            ->get([
                'products.*', 
                // 'users.*', 
                'products.id as product_id',
                'users.id as user_id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.image_url as image_url',
                's_user.id as supplier_id',
                's_user.first_name as supplier_first_name',
                's_user.last_name as supplier_last_name',
                's_user.image_url as supplier_image_url',
                'carts.id as cart_id',
                'carts.quantity as quantity',
                'carts.created_at as date_added',
                'users.image_url as user_image',
                'products.image_url as product_image',
            ]);
     
            $newcart = [];

            foreach ($cart as $item) {
                if (!isset($newcart[$item->supplier_id])) {
                    $newcart[$item->supplier_id] = [
                        'supplier_id' => $item->supplier_id,
                        'supplier_name' => $item->supplier_first_name . " " . $item->supplier_last_name,
                        'supplier_image' => $item->supplier_image_url,
                        'products' => [],
                    ];
                }

                $newcart[$item->supplier_id]['products'][] = new CartResource($item);
            }

            $cart = $newcart;

        } else {
            $cart = Cart::query()
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('users as s_user', 'carts.supplier_id', '=', 's_user.id')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->where('products.deleted_flag', '!=', 1)
            ->orderBy("carts.created_at", "ASC")
            ->get([
                'products.*', 
                // 'users.*', 
                'products.id as product_id',
                'users.id as user_id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.image_url as image_url',
                's_user.id as supplier_id',
                's_user.first_name as supplier_first_name',
                's_user.last_name as supplier_last_name',
                's_user.image_url as supplier_image_url',
                'carts.id as cart_id',
                'carts.quantity as quantity',
                'carts.created_at as date_added',
                'users.image_url as user_image',
                'products.image_url as product_image',
            ]);

            $newcart = [];

            foreach ($cart as $item) {
                if (!isset($newcart[$item->supplier_id])) {
                    $newcart[$item->supplier_id] = [
                        'supplier_id' => $item->supplier_id,
                        'supplier_name' => $item->supplier_first_name . " " . $item->supplier_last_name,
                        'supplier_image' => $item->supplier_image_url,
                        'products' => [],
                    ];
                }

                $newcart[$item->supplier_id]['products'][] = new CartResource($item);
            }

            $cart = $newcart;
        }
        
        return $cart;
    }
}
