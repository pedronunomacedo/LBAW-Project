<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function showWishlist(Request $request)
    {

        if (Auth::check()) {

            $user = Auth::user();
            //$this->authorize('edit', $user);
            $products = $user->wishlist()->get();
        }
        return view('pages.wishlist', ['products' => $products]);

    }

    public function addWishlistProduct(Request $request)
    {
        $product = Product::findOrFail($request->id);
        if ($product != NULL) {

            if (Auth::check()) {
                $user = Auth::user();
                if($user->wishlist()->where('idproduct', $request->id)->count() > 0){
                    return response(json_encode("You already have this product in your wishlist"), 400);
                }
                Auth::user()->wishlist()->attach($product);
                return response(json_encode("Product added to Wishlist"), 200);
            } else {
                return redirect('/login');
            }
        }
    }

    public function removeWishlistProduct(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $product = $user->wishlist()->where('product_id', $request->id)->first();
        }
        if($product != null){
            $user->wishlist()->detach([$request->id]);
            return response(json_encode("Product deleted from wishlist"), 200);
        }
        else{
            return response(json_encode("That product is not in wishlist"), 404);
        }
    }
}