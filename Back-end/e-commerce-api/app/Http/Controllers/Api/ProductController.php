<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $products=Product::all();
        return $this->apiResponse($products);
    }

    public function show($id){
        $product = Product::find($id);

        if($product){
            return $this->apiResponse($product);
        }

        return $this->NotFoundError();
    }

    public function store(Request $request){

        $validate = Validator::make($request->all(),[
            'title' => 'required|min:2|unique:mobiles,title',
            'description' => 'required|min:10,description',
            'price' => 'required|min:2,price',
            'type' => 'required|min:2,type',
        ]);

        if($validate->fails()){
            return  $this->apiResponse(null,$validate->errors(),422);
        }
        $product=Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
        ]);

        if($product){
            return $this->apiResponse($product);
        }
        return  $this->UnknownError();
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(),[
            'title' => 'required|min:2|unique:mobiles,title',
            'description' => 'required|min:10,description',
            'price' => 'required|min:2,price',
            'type' => 'required|min:2,type',
        ]);

        if($validate->fails()){
            return  $this->apiResponse(null,$validate->errors(),422);
        }

        $product = Product::find($id);

        if(!$product){
            return $this->NotFoundError();
        }

        $product->update($request->all());
        if($product){
            return $this->apiResponse($product,'',201);
        }

        return  $this->UnknownError();
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if($product){
            $product->delete();
            return $this->apiResponse(true,'',200);
        }

        return $this->NotFoundError();
    }

    public function sell($id , Request $request){
        $product = Product::find($id);
        if(!$product){
            return $this->NotFoundError();
        };

        if($request->amount > $product->amount){
            return $this->apiResponse(null , 'this amount not avaliable now!!');
        };

        $product->amount -= $request->amount;
        $product->save();
    }


}
