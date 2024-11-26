<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ZohoController extends Controller
{
    // Redirect to Zoho for Authentication
    public function redirectToZoho()
    {
        $query = http_build_query([
            'client_id' => env('ZOHO_CLIENT_ID'),
            'response_type' => 'code',
            'redirect_uri' => env('ZOHO_REDIRECT_URI'),
            'scope' => 'ZohoCRM.modules.ALL',
        ]);

        return response()->json(['redirect_url' => env('ZOHO_API_BASE_URL') . '/oauth/v2/auth?' . $query]);
    }

    // Handle Zoho Callback
    public function handleZohoCallback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }

        $response = Http::asForm()->post(env('ZOHO_API_BASE_URL') . '/oauth/v2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'redirect_uri' => 'http://localhost:3000/dashboard',
            'code' => $code,
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            session(['zoho_access_token' => $data['access_token']]);
            return response()->json(['message' => 'Authenticated successfully', 'data' => $data]);
        }

        return response()->json(['error' => 'Failed to authenticate', 'details' => $data], 500);
    }

    // Fetch Chart of Accounts
    public function getChartOfAccounts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withToken($accessToken)->get(env('ZOHO_API_BASE_URL') . '/books/v3/chartofaccounts');

        if ($response->successful()) {
            return response()->json(['accounts' => $response->json()]);
        }

        return response()->json(['error' => 'Failed to fetch chart of accounts'], 500);
    }

    // Sync Chart of Accounts
    public function syncChartOfAccounts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get(env('ZOHO_API_BASE_URL') . '/api/v3/chartofaccounts');

        if ($response->successful()) {
            $accounts = $response->json()['chartofaccounts'];

            foreach ($accounts as $account) {
                DB::table('zoho_accounts')->updateOrInsert(
                    ['zoho_account_id' => $account['account_id']],
                    [
                        'name' => $account['account_name'],
                        'type' => $account['account_type'],
                        'currency' => $account['currency'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json(['message' => 'Sync successful']);
        }

        return response()->json(['message' => 'Failed to sync accounts'], 500);
    }

    // Fetch Contacts
    public function getContacts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withToken($accessToken)->get(env('ZOHO_API_BASE_URL') . '/books/v3/contacts');

        if ($response->successful()) {
            return response()->json(['contacts' => $response->json()]);
        }

        return response()->json(['error' => 'Failed to fetch contacts'], 500);
    }

    // Sync Contacts
    public function syncContacts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get(env('ZOHO_API_BASE_URL') . '/api/v3/contacts');

        if ($response->successful()) {
            $contacts = $response->json()['contacts'];

            foreach ($contacts as $contact) {
                DB::table('zoho_contacts')->updateOrInsert(
                    ['zoho_contact_id' => $contact['contact_id']],
                    [
                        'name' => $contact['contact_name'],
                        'email' => $contact['email'],
                        'phone' => $contact['phone'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json(['message' => 'Sync successful']);
        }

        return response()->json(['message' => 'Failed to sync contacts'], 500);
    }

    // Fetch Receipts
    public function getReceipts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withToken($accessToken)->get(env('ZOHO_API_BASE_URL') . '/books/v3/receipts');

        if ($response->successful()) {
            return response()->json(['receipts' => $response->json()]);
        }

        return response()->json(['error' => 'Failed to fetch receipts'], 500);
    }

    // Sync Receipts
    public function syncReceipts()
    {
        $accessToken = session('zoho_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get(env('ZOHO_API_BASE_URL') . '/api/v3/receipts');

        if ($response->successful()) {
            $receipts = $response->json()['receipts'];

            foreach ($receipts as $receipt) {
                DB::table('zoho_receipts')->updateOrInsert(
                    ['zoho_receipt_id' => $receipt['receipt_id']],
                    [
                        'amount' => $receipt['amount'],
                        'date' => $receipt['date'],
                        'contact_id' => $receipt['contact_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json(['message' => 'Sync successful']);
        }

        return response()->json(['message' => 'Failed to sync receipts'], 500);
    }
}
