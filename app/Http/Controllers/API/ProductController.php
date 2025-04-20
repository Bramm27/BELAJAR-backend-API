<?php

namespace App\Http\Controllers\API;


// use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Support\Facades\Validator;


class ProductController extends BaseController
{
    public function index(): JsonResponse
    {
        $allProduct = Product::all();

        return $this->sendResponse(200, 'All Product successfully collected.', ProductResource::collection($allProduct));
    }

    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Product not found.');
        }

        // Return the product data as a response
        return $this->sendResponse(200, 'Product found successfully.', new ProductResource($product));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'detail' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $duplicateTitleProduct = Product::where('title', $request->title)->first();

        if ($duplicateTitleProduct) {
            return $this->sendError('Title Product already exists.');
        }

        $input = $request->all();
        $product = Product::create($input);

        return $this->sendResponse(201, 'Product created successfully.', new ProductResource($product));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'detail' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Product not found.');
        }

        $dupliacateTitleProduct = Product::where('title', $request->title)
            ->where('id', '!=', $id)
            ->first();

        if ($dupliacateTitleProduct) {
            return $this->sendError('Title Product already exists.');
        }

        $oldProduct = clone $product;

        // Update data menggunakan fill() dan save()
        $product->update([
            'title' => $request->title,
            'detail' => $request->detail,
            'price' => $request->price,
        ]);

        // return $this->sendResponse(200, 'Product updated successfully.', new ProductResource($product));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'old_data' => new ProductResource($oldProduct),
            'new_data' => new ProductResource($product),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Product not found.');
        }

        if ($product->title !== $request->title) {
            return $this->sendError('Title Product does not match.');
        }

        // Update data menggunakan fill() dan save()
        $product->delete();

        return $this->sendResponse(200, 'Product deleted successfully.', new ProductResource($product));
    }
}