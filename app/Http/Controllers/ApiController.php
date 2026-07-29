<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    /**
     * Fetch list of shoes with optional filters (search, category).
     */
    public function getShoes(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $shoes = collect();
        $usedFallback = false;

        try {
            $query = Shoe::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', '%' . $search . '%')
                      ->orWhere('brand', 'ilike', '%' . $search . '%');
                });
            }

            if ($category && $category !== 'All') {
                $query->where('category', $category);
            }

            $shoes = $query->orderBy('created_at', 'desc')->get();
            
            if ($shoes->isEmpty() && !$search) {
                $usedFallback = true;
            }
        } catch (\Exception $e) {
            $usedFallback = true;
        }

        // Fallback to static JSON file if DB fails or is empty
        if ($usedFallback) {
            $jsonPath = public_path('sneakers_dummy.json');
            if (File::exists($jsonPath)) {
                $jsonDecoded = json_decode(File::get($jsonPath), true);
                
                // Apply simple PHP filtering for Search and Category on JSON data
                $shoes = collect($jsonDecoded)->map(function($item) {
                    return (object) $item;
                });

                if ($search) {
                    $shoes = $shoes->filter(function($item) use ($search) {
                        return str_contains(strtolower($item->name), strtolower($search)) || 
                               str_contains(strtolower($item->brand), strtolower($search));
                    });
                }

                if ($category && $category !== 'All') {
                    $shoes = $shoes->filter(function($item) use ($category) {
                        return $item->category === $category;
                    });
                }
            }
        }

        // Format image URLs
        $formattedShoes = $shoes->map(function($shoe) {
            $shoeArray = $shoe instanceof \Illuminate\Database\Eloquent\Model ? $shoe->toArray() : (array) $shoe;
            if (isset($shoeArray['image_url']) && strpos($shoeArray['image_url'], 'http') === false) {
                $shoeArray['image_url'] = url($shoeArray['image_url']);
            }
            // Decode sizes if it comes as a string from DB (sqlite/postgres edge cases)
            if (isset($shoeArray['sizes']) && is_string($shoeArray['sizes'])) {
                $shoeArray['sizes'] = json_decode($shoeArray['sizes'], true);
            }
            return $shoeArray;
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $formattedShoes
        ], 200);
    }

    /**
     * Get detail of a specific shoe.
     */
    public function getShoeDetail($id)
    {
        $shoe = null;

        try {
            $shoe = Shoe::find($id);
        } catch (\Exception $e) {
            // DB fail fallback
        }

        // Fallback to JSON search if DB fails or shoe not found in DB
        if (!$shoe) {
            $jsonPath = public_path('sneakers_dummy.json');
            if (File::exists($jsonPath)) {
                $jsonDecoded = json_decode(File::get($jsonPath), true);
                $found = collect($jsonDecoded)->firstWhere('id', intval($id));
                if ($found) {
                    $shoe = (object) $found;
                }
            }
        }

        if (!$shoe) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $shoeArray = $shoe instanceof \Illuminate\Database\Eloquent\Model ? $shoe->toArray() : (array) $shoe;
        if (isset($shoeArray['image_url']) && strpos($shoeArray['image_url'], 'http') === false) {
            $shoeArray['image_url'] = url($shoeArray['image_url']);
        }
        if (isset($shoeArray['sizes']) && is_string($shoeArray['sizes'])) {
            $shoeArray['sizes'] = json_decode($shoeArray['sizes'], true);
        }

        return response()->json([
            'status' => 'success',
            'data' => $shoeArray
        ], 200);
    }

    /**
     * User registration endpoint.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * User login endpoint.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    /**
     * Submit a new transaction order, uploading the payment proof.
     */
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_courier' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'total_price' => 'required|numeric',
            'user_id' => 'nullable|integer',
            'payment_proof' => 'required|file|max:5120',
            'items' => 'required|string', // JSON string containing array of items e.g., [{"shoe_id":1,"shoe_size":42,"quantity":2,"price":150000}]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Decode the JSON items
        $items = json_decode($request->input('items'), true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($items)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format item transaksi tidak valid'
            ], 422);
        }

        // Handle payment proof upload and save to public/uploads/receipts
        $receiptPath = null;
        if ($request->hasFile('payment_proof')) {
            $image = $request->file('payment_proof');
            $imageName = time() . '_receipt_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            if (!File::exists(public_path('uploads/receipts'))) {
                File::makeDirectory(public_path('uploads/receipts'), 0755, true);
            }
            
            $image->move(public_path('uploads/receipts'), $imageName);
            $receiptPath = 'uploads/receipts/' . $imageName;
        }

        // Create transaction
        $transaction = Transaction::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_courier' => $request->shipping_courier,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'total_price' => $request->total_price,
            'user_id' => $request->user_id,
            'status' => 'pending',
            'payment_proof' => $receiptPath,
        ]);

        // Create transaction items
        foreach ($items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'shoe_id' => $item['shoe_id'],
                'shoe_size' => $item['shoe_size'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil dibuat. Menunggu persetujuan admin.',
            'data' => [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]
        ], 201);
    }

    /**
     * Get transaction history for a specific user.
     */
    public function getUserTransactions($userId)
    {
        $transactions = Transaction::with(['items.shoe'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($transaction) {
                if ($transaction->payment_proof && strpos($transaction->payment_proof, 'http') === false) {
                    $transaction->payment_proof = url($transaction->payment_proof);
                }
                
                $transaction->items->map(function($item) {
                    if ($item->shoe && $item->shoe->image_url && strpos($item->shoe->image_url, 'http') === false) {
                        $item->shoe->image_url = url($item->shoe->image_url);
                    }
                    return $item;
                });
                
                return $transaction;
            });

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }
}
