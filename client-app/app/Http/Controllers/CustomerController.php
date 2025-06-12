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

        $customers = $this->fetchCustomerList->handle($token->access_token);
        if (is_string($customers)) {
            return Inertia::render('Home', [
                'errorMessage' => '許可されていません: ' . $customers,
            ]);
        }
        if (!$customers) {
            return Inertia::render('Home', [
                'errorMessage' => '顧客取得失敗',
            ]);
        }

        return Inertia::render('CustomerList', [
            'customers' => $customers,
        ]);
    }
}
