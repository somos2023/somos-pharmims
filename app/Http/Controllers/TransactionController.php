<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $user = $request->user();
        $transactionData = [];
        if($user->role_id == 1){
            $getTransaction = Transaction::query()
            ->join('users as u', 'transactions.staff_id', '=', 'u.id' )
            ->orderBy("transactions.created_at", "DESC")
            ->get("transactions.*", 
                "u.first_name as first_name",
                "u.last_name as last_name",
                "u.email as email",
            );
            if($getTransaction){
                foreach ($getTransaction as $item) {
                    $dataItems = [];
                    $getTransactionitems = $this->getTransItems($item['id']);
                    if($getTransactionitems){
                        $dataItems = $getTransactionitems;
                    } 
                    // $newItem = [];
                    // $newItem['id'] = $item['id'];
                    // $newItem['staff_id'] = $item['staff_id'];
                    // $newItem['total_quantity'] = $item['total_quantity'];
                    $item['operated_by'] = $request->user()->first_name; ;
                    $item['items'] = $dataItems;
                    $transactionData[] = $item;
                }
            }
        } else {
            $myId = $user->id; 

            $getTransaction = Transaction::query()
            ->join('users as u', 'transactions.staff_id', '=', 'u.id' )
            ->where('transactions.staff_id', $myId)
            ->orderBy("transactions.created_at", "DESC")
            ->get("transactions.*", 
                "u.first_name as first_name",
                "u.last_name as last_name",
                "u.email as email",
            );
            if($getTransaction){
                foreach ($getTransaction as $item) {
                    $dataItems = [];
                    $getTransactionitems = $this->getTransItems($item['id']);
                    if($getTransactionitems){
                        $dataItems = $getTransactionitems;
                    } 
                    // $newItem = [];
                    // $newItem['id'] = $item['id'];
                    // $newItem['staff_id'] = $item['staff_id'];
                    // $newItem['total_quantity'] = $item['total_quantity'];
                    $item['operated_by'] = $request->user()->first_name; ;
                    $item['items'] = $dataItems;
                    $transactionData[] = $item;
                }
            }
        }

        return response()->json([
            'data' => TransactionResource::collection($transactionData),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $vdata = $request->validated();

        $vdata['transaction_number'] = $this->generateOrderNumber();
        $transac = new Transaction();
        $transac->fill($vdata);
        $saveTransac = $transac->save();

        if(!$saveTransac){
            return response()->json(['message' => 'Transaction not save!'], 422);
        }

        foreach ($vdata['items'] as $item) {
           $itemsData = [];

           $itemsData['transaction_id'] = $transac->id;
           $itemsData['product_id'] = $item['product_id'];
           $itemsData['selling_price'] = $item['price'];
           $itemsData['quantity'] = $item['quantity'];
           $itemsData['total'] = $item['total'];

            $transactionItem = new TransactionItem();
            $transactionItem->fill($itemsData);
            $saveTransactionItem = $transactionItem->save();

            if($saveTransactionItem){
                $resource = Product::findOrFail($item['product_id']);
                $newStock = (int)$resource->stock - $item['quantity'];
                $resource->update(['stock' => $newStock]);
            }
        }
        
        return response()->json(['message' => 'Transaction saved!'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = Transaction::query()
            ->join('users as u', 'transactions.staff_id', '=', 'u.id' )
            ->where('transactions.id', $id)
            ->orderBy("transactions.created_at", "DESC")
            ->get("transactions.*", 
                "u.first_name as first_name",
                "u.last_name as last_name",
                "u.email as email",
            );

        if (!$resource) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $transactionData = [];

        if($resource){
            foreach ($resource as $item) {
                $dataItems = [];
                $getTransactionitems = $this->getTransItems($item['id']);
                if($getTransactionitems){
                    $dataItems = $getTransactionitems;
                } 

                // $item['operated_by'] = $request->user()->first_name; ;
                $item['items'] = $dataItems;
                $transactionData[] = $item;
            }
        }

        return response()->json([
            'data' => TransactionResource::collection($transactionData),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    private function generateOrderNumber()
    {
        $randomNumber = substr(str_shuffle('0123456789'), 0, 15);
        return $randomNumber;
    }

    private function getTransItems($id){
      $result = TransactionItem::query()
        ->join('products as c', 'transaction_items.product_id', '=', 'c.id')
        ->orderBy('transaction_items.created_at', 'ASC')
        ->where('transaction_items.transaction_id', $id )
        ->get(['transaction_items.*', 
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
