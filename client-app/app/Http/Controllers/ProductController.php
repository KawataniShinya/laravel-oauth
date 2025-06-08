<?php

namespace App\Http\Controllers;

use App\UseCase\FetchAccessToken;
use App\UseCase\FetchProductList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected FetchAccessToken $fetchAccessToken;
    protected FetchProductList $fetchProductList;

    public function __construct(FetchAccessToken $fetchAccessToken, FetchProductList $fetchProductList)
    {
        $this->fetchAccessToken = $fetchAccessToken;
        $this->fetchProductList = $fetchProductList;
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        $token = $this->fetchAccessToken->handle($username, $password);
        if (!$token) {
            return response()->json(['error' => 'トークン取得失敗'], 401);
        }

        $products = $this->fetchProductList->handle($token->access_token);
        if (!$products) {
            return response()->json(['error' => '商品取得失敗'], 500);
        }

        return Inertia::render('ProductList', [
            'products' => $products,
        ]);
    }
}
