<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Http\Resources\ProductResource;
// use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use Illuminate\Support\Collection;
use Excel;
use PDF;

class ExportController extends Controller
{
    public function exportExcel() {
        // Fetch specific columns from the database
        $products = $this->staffProducts();
        $data = ProductResource::collection($products);

        // Get the current date
        $currentDate = date('Y-m-d'); // Or any other format you prefer

        // Generate a filename with the current date
        $filename = "book_data_{$currentDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $customColumnNames = [
            'barcode' => 'Barcode',
            'brand_name' => 'Brand Name',
            'generic_name' => 'Generic Name',
            'formulation' => 'Formulation',
            'packing' => 'Packing',
            'price' => 'Price',
            'stock' => 'Stock',
            'created_at' => 'Created At',
            // 'start_time' => 'Start Time',
        ];

        $callback = function() use ($data, $customColumnNames) {
            $file = fopen('php://output', 'w');

            // Add headers with custom column names
            fputcsv($file, array_values($customColumnNames));

            // Add data with specific columns
            foreach ($data as $row) {
                $rowData = [];
                foreach ($customColumnNames as $column => $customName) {
                    if ($column === 'created_at') {
                        // Convert timestamp to desired date format
                        $formattedDate = date('F j, Y', strtotime($row->{$column}));
                        $rowData[] = $formattedDate;
                    } else {
                        $rowData[] = $row->{$column};
                    }
                }
                fputcsv($file, $rowData);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    // public function exportExcel(Request $request)
    // {
    //      $model = $this->makeModel($request);
    //     $users = $model::select('id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at')->get();
    //     return Excel::download(new UsersExport($users), $request->model.'-users.xlsx');
    // }

    // public function exportExcel2(Request $request){
    //     // Open the file
    //     // Usage
    //     $sheet = $event->sheet;
    //     setColumnWidth($sheet, 'A', 20);
    //     setColumnWidth($sheet, 'B', 30);
    //     setColumnWidth($sheet, 'C', 40);
    // }

    // public function downloadPDF(Request $request)
    // {
    //     $model = $this->makeModel($request);
    //     $records = $model::all(); // Fetch all records from the database
    //     $data = [
    //         'title' => ucfirst($request->model).' Records',
    //         'records' => $records,
    //     ];

    //     $pdf = PDF::loadView('admin.includes.user-record', $data);
    //     return $pdf->download($request->model.'-records.pdf');
    // }

    // private function makeModel(Request $request){
    //     $model = "App\\Models\\".ucfirst($request->model);
    //     return $model;
    // }
}

// class UsersExport implements FromCollection, WithHeadings, WithColumnWidths
// {
//     protected $users;

//     public function __construct($users)
//     {
//         $this->users = $users;
//     }

//     public function collection()
//     {
//         return $this->users;
//     }

//     public function headings(): array
//     {
//         return [
//             'ID',
//             'First Name',
//             'Last Name',
//             'Email',
//             'Created At',
//             'Updated At',
//         ];
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 5,
//             'B' => 20,
//             'C' => 20,
//             'D' => 30,
//             'E' => 30,
//             'F' => 30,
//         ];
//     }
// }

// php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
// php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
