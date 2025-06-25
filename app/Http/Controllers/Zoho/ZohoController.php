<?php

namespace App\Http\Controllers\Zoho;

use App\Http\Controllers\Controller;
use App\Services\ZohoService;
use Illuminate\Http\Request;

class ZohoController extends Controller
{
    public function getDealStages(ZohoService $zoho)
    {
        try {
            $stages = $zoho->getDealStages();
            return response()->json($stages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Zoho error', 'message' => $e->getMessage()], 500);
        }
    }

    public function createDealAccount(Request $request, ZohoService $zoho)
    {
        $validated = $request->validate([
            'deal_name' => 'required|string|max:255',
            'deal_stage' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_website' => 'required|url|max:255',
            'account_phone' => 'required|string|max:255',
        ]);

        try {
            $accountData = [
                'Account_Name' => $validated['account_name'],
                'Website' => $validated['account_website'],
                'Phone' => $validated['account_phone'],
            ];
            $accountId = $zoho->createAccount($accountData);

            $dealData = [
                'Deal_Name' => $validated['deal_name'],
                'Stage' => $validated['deal_stage'],
                'Account_Name' => ['id' => $accountId],
            ];
            $zoho->createDeal($dealData);

            return redirect()->back()->with('success', 'Deal & Account created!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error occurred while creating deal or account: ' . $e->getMessage());
        }
    }

}
