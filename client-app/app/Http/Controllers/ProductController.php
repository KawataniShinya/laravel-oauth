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
        $username = $request->session()->get('username');

        $token = $this->fetchAccessToken->handle($username);
        if (!$token) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => 'トークンが無効です。',
            ]);
        }

        $products = $this->fetchProductList->handle($token->access_token);
        if (is_string($products)) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => '許可されていません: ' . $products,
            ]);
        }
        if (!$products) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => '商品取得失敗',
            ]);
        }

        return Inertia::render('ProductList', [
            'products' => $products,
        ]);
    }
}
