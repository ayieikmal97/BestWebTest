<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Requests\StoreProductRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;

class ProductController extends Controller
{
    public function index()
    {
        $categories=ProductCategory::all();
        return view('dashboard',compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        if(isset($request->id)){
            $product = Product::findOrFail($request->id);
            $product->update($request->validated());
        }else{
            Product::create($request->validated());
        }

        return response(['saved'=>1]);
    }

    public function create(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['name', 'category_id', 'price', 'stock', 'status'];

        $query = Product::with('category:id,name');

        // Search
        if(!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhereHas('category', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $totalRecords = $query->count();

        // Ordering
        if(isset($columns[$orderColumnIndex])) {
            $orderColumn = $columns[$orderColumnIndex];
            $query->orderBy($orderColumn, $orderDir);
        }

        // Pagination
        $products = $query->skip($start)->take($length)->get();

        $statusArr = ['Disabled','Enabled'];
        $data = [];
        foreach($products as $product) {
            $data[] = [
                $product->name,
                $product->category->name,
                $product->price,
                $product->stock,
                $statusArr[$product->status],
                "<input type='checkbox' class='product-checkbox' value='{$product->id}'>",
                "<button type='button' class='btn btn-info' onclick='editProduct({$product->id})' data-bs-toggle='modal' data-bs-target='#productModal'>Edit</button>",
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')), // sent by DataTable
            'recordsTotal' => Product::count(),        // total records
            'recordsFiltered' => $totalRecords,       // filtered records
            'data' => $data
        ]);
    }

    
    public function show($id)
    {
        $product=Product::findOrFail($id);
        return response(['data'=>$product]);
    }

    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $product->delete();
        return response(['saved'=>1]);
    }

    public function export() 
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        Product::whereIn('id', $ids)->delete();

        return response(['saved'=>1]);
    }
}
