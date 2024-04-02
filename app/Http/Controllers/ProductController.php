<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\SupplierProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authRole = $request->user()->role_id; 
        $products = [];

        if($authRole != 3){
            $products = $this->staffProducts();
        } else {
            $products = $this->supplierProducts($request->user()->id);           
        }
        return response()->json([
            'data' => ProductListResource::collection($products)
        ]);
    }

    public function supplierProduct(Request $request){
        $products = $this->supplierProducts();
        return response()->json([
            'data' => SupplierProductResource::collection($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if (isset($data['image_url'])) {
            $relativePath = $this->saveImage($data['image_url']);
            $data['image_url'] = $relativePath;
        } else {
            $data['image_url'] = null;
        }

        $data['expires_at'] = $data['expiration_date'];
        $data['formulation'] = $data['dosage'];
        $data['packing'] = $data['unit'];
        unset($data['expiration_date']);
        unset($data['unit']);
        unset($data['dosage']);

        

        $product = new Product();
        $product->fill($data); 
        $save = $product->save();

        $authRole = $request->user()->role_id; 
        $products = [];

        if($authRole != 3){
            $products = $this->staffProducts();
        } else {
            $products = $this->supplierProducts();           
        }

        return response([
            'message' => 'Product added successfully!',
            'data' => ProductResource::collection($products)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = Product::find($id);

        if (!$resource) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        $data = $this->product($id);

        if($resource->role_id != 3){
            $barcode = $resource->barcode;
            $result = Stock::where('barcode', $barcode)
                        ->where('status', '=', 'available')
                        ->sum('quantity');
            $data[0]['available_stocks'] = (int)$result;
        }

        return response([
            'data' => ProductResource::collection([$data[0]])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $data = $request->validated();

        if (isset($data['image_url'])) {
            $relativePath = $this->saveImage($data['image_url']);
            $data['image_url'] = $relativePath;
        } else {
            $data['image_url'] = null;
        }

        $data['expires_at'] = $data['expiration_date'];
        unset($data['expiration_date']);

        $resource = Product::findOrFail($id);
        $resource->update($data);

        if($data['stock_added'] != 0){
            $stocks = Stock::where('quantity', '!=', '0')
                ->where('barcode', $resource->barcode)
                ->orderBy('created_at', 'ASC')
                ->get();

            $stockAdded = $data['stock_added'];
            foreach($stocks as $item){
                if($stockAdded > 0){
                    $resource = Stock::findOrFail($item->id);
                    $itemQty = $resource->quantity;

                    $newQty = 0;
                    $qty = 0;
                    if($itemQty >= $stockAdded){
                        $newQty = $itemQty - $stockAdded;
                        $qty = $itemQty - $stockAdded;
                    } else {
                        $newQty = 0;
                        $qty = $itemQty;
                    }
                    
                    $resource->update(["quantity" => $newQty]);
                    $stockAdded = $stockAdded - $qty;
                }
            }
        } 
       
        $authRole = $request->user()->role_id; 
        $products = [];

        if($authRole != 3){
            $products = $this->staffProducts();
        } else {
            $products = $this->supplierProducts();           
        }

        if ($resource->wasChanged()) {
            return response()->json([
                'message' => 'Update successfully!',
                'data' => ProductResource::collection($products)
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
        $item = Product::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        
        $data['deleted_flag'] = 1;
        $item->update($data);

        return response()->json(['message' => 'Item deleted'], 200);
    }

    /**
     * Delete Permanently the specified resource from storage.
     */
    public function permanentlyDelete(string $id)
    {
        $item = User::find($id);

        if (!$item) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }

    private function staffProducts(){
         $result = Product::query()
          ->join('categories as c', 'products.category_id', '=', 'c.id')
          ->orderBy('products.created_at', 'ASC')
          ->where('products.deleted_flag', '!=', '1')
          ->where('products.role_id', '!=', '3')
          ->get(['products.*', 'c.category as category']);
        return $result;
    }

    private function supplierProducts($id=""){
        $result = null;
        if(!empty($id)){
            $result = Product::query()
              ->join('categories as c', 'products.category_id', '=', 'c.id')
              ->orderBy('products.created_at', 'ASC')
              ->where('products.deleted_flag', '!=', '1')
              ->where('products.role_id', '=', '3')
              ->where('products.user_id', '=', $id)
              ->get(['products.*', 'c.category as category']);
        } else {
            $result = Product::query()
              ->join('categories as c', 'products.category_id', '=', 'c.id')
              ->join('users as u', 'products.user_id', '=', 'u.id')
              ->orderBy('products.created_at', 'ASC')
              ->where('products.deleted_flag', '!=', '1')
              ->where('products.role_id', '=', '3')
              ->get([
                'products.*', 
                'c.category as category',
                'u.first_name as supplier_fname',
                'u.last_name as supplier_lname',
                'u.image_url as supplier_image',
                'u.email as supplier_email',
                'u.phone_number as supplier_phone_number',
            ]);
        }
        return $result;
    }

    private function product($id){
      $result = Product::query()
        ->join('categories as c', 'products.category_id', '=', 'c.id')
        ->orderBy('products.created_at', 'ASC')
        ->where('products.id', $id )
        ->where('products.deleted_flag', '!=', '1')
        ->get(['products.*', 'c.category as category']);
      return $result;
    }

    private function saveImage($image) {

        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            $image = substr($image, strpos($image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('Invalid image type');
            }

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }

        } else {
            throw new \Exception('Did not match data URI with image data');
        }


        $dir = 'images/products/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }
}
