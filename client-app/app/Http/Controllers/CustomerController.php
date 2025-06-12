<?php

namespace App\Http\Controllers;

use App\UseCase\FetchAccessToken;
use App\UseCase\FetchCustomerList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    protected FetchAccessToken $fetchAccessToken;
    protected FetchCustomerList $fetchCustomerList;

    public function __construct(FetchAccessToken $fetchAccessToken, FetchCustomerList $fetchCustomerList)
    {
        $this->fetchAccessToken = $fetchAccessToken;
        $this->fetchCustomerList = $fetchCustomerList;
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

        $customers = $this->fetchCustomerList->handle($token->access_token);
        if (is_string($customers)) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => '許可されていません: ' . $customers,
            ]);
        }
        if (!$customers) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => '顧客取得失敗',
            ]);
        }

        return Inertia::render('CustomerList', [
            'customers' => $customers,
        ]);
    }
}
