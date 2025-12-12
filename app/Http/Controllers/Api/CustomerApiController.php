<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerApiController extends Controller
{
    public function syncFromFirebase(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'firebase_uid' => 'required|string',
            'email'       => 'required|email',
            'name'        => 'nullable|string',
            'phone'       => 'nullable|string',
        ]);

        // Hanapin kung existing na customer base sa firebase_uid or email
        $customer = Customer::where('firebase_uid', $data['firebase_uid'])
            ->orWhere('email', $data['email'])
            ->first();

        if (!$customer) {
            // Kung wala pa, gumawa ng bagong customer
            $customer = Customer::create([
                'firebase_uid' => $data['firebase_uid'],
                'email'        => $data['email'],
                'name'         => $data['name'] ?? '',
                'phone'        => $data['phone'] ?? null,
            ]);
        } else {
            // Kung meron na, i-update info (optional pero useful)
            $customer->update([
                'firebase_uid' => $data['firebase_uid'],
                'name'         => $data['name'] ?? $customer->name,
                'phone'        => $data['phone'] ?? $customer->phone,
            ]);
        }

        // Ibalik sa FlutterFlow ang customer_id at ibang info
        return response()->json([
            'customer_id' => $customer->id,
            'email'       => $customer->email,
            'name'        => $customer->name,
            'phone'       => $customer->phone,
        ]);
    }
}
