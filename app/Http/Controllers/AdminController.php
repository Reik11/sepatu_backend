<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard home with statistical overviews.
     */
    public function index()
    {
        $totalSales = Transaction::where('status', 'approved')->sum('total_price');
        $totalOrders = Transaction::count();
        $pendingOrders = Transaction::where('status', 'pending')->count();
        $approvedOrders = Transaction::where('status', 'approved')->count();
        $totalShoes = Shoe::count();

        // Get 5 latest transactions
        $recentTransactions = Transaction::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'pendingOrders',
            'approvedOrders',
            'totalShoes',
            'recentTransactions'
        ));
    }

    /**
     * Display a list of shoes, with support for search and category filter.
     */
    public function shoesIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Shoe::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        $shoes = $query->orderBy('created_at', 'desc')->get();

        // If it's an AJAX request (for real-time search), return partial table content
        if ($request->ajax()) {
            return view('admin.shoes.partials.table', compact('shoes'));
        }

        return view('admin.shoes.index', compact('shoes', 'search', 'category'));
    }

    /**
     * Show the form to create a new shoe.
     */
    public function shoesCreate()
    {
        return view('admin.shoes.create');
    }

    /**
     * Store a newly created shoe in database.
     */
    public function shoesStore(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'integer|min:30|max:50',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'file|max:5120';
        }

        $request->validate($rules);

        $data = $request->only(['name', 'brand', 'category', 'price', 'sizes', 'description', 'stock']);

        // Handle image upload and move to public/uploads/shoes
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Ensure folder exists
            if (!File::exists(public_path('uploads/shoes'))) {
                File::makeDirectory(public_path('uploads/shoes'), 0755, true);
            }
            
            $image->move(public_path('uploads/shoes'), $imageName);
            $data['image_url'] = 'uploads/shoes/' . $imageName;
        } else {
            // Set a generic shoe placeholder image url
            $data['image_url'] = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400';
        }

        Shoe::create($data);

        return redirect()->route('admin.shoes.index')->with('success', 'Produk sepatu berhasil ditambahkan!');
    }

    /**
     * Show the form to edit an existing shoe.
     */
    public function shoesEdit($id)
    {
        $shoe = Shoe::findOrFail($id);
        return view('admin.shoes.edit', compact('shoe'));
    }

    /**
     * Update the specified shoe in database.
     */
    public function shoesUpdate(Request $request, $id)
    {
        $shoe = Shoe::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'integer|min:30|max:50',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'file|max:5120';
        }

        $request->validate($rules);

        $data = $request->only(['name', 'brand', 'category', 'price', 'sizes', 'description', 'stock']);

        if ($request->hasFile('image')) {
            // Delete old file if exists and not a placeholder
            if ($shoe->image_url && File::exists(public_path($shoe->image_url)) && strpos($shoe->image_url, 'http') === false) {
                File::delete(public_path($shoe->image_url));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            if (!File::exists(public_path('uploads/shoes'))) {
                File::makeDirectory(public_path('uploads/shoes'), 0755, true);
            }

            $image->move(public_path('uploads/shoes'), $imageName);
            $data['image_url'] = 'uploads/shoes/' . $imageName;
        }

        $shoe->update($data);

        return redirect()->route('admin.shoes.index')->with('success', 'Produk sepatu berhasil diperbarui!');
    }

    /**
     * Delete the specified shoe from database.
     */
    public function shoesDestroy($id)
    {
        $shoe = Shoe::findOrFail($id);

        // Delete image file
        if ($shoe->image_url && File::exists(public_path($shoe->image_url)) && strpos($shoe->image_url, 'http') === false) {
            File::delete(public_path($shoe->image_url));
        }

        $shoe->delete();

        return redirect()->route('admin.shoes.index')->with('success', 'Produk sepatu berhasil dihapus!');
    }

    /**
     * List all transactions.
     */
    public function transactionsIndex(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Transaction::query();

        if ($status && $status !== 'All') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
            return view('admin.transactions.partials.table', compact('transactions'));
        }

        return view('admin.transactions.index', compact('transactions', 'status', 'search'));
    }

    /**
     * Display detail of a transaction.
     */
    public function transactionsShow($id)
    {
        $transaction = Transaction::with(['items.shoe'])->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Approve or reject a transaction payment.
     */
    public function transactionsUpdateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $oldStatus = $transaction->status;
        $newStatus = $request->input('status');
        
        $transaction->status = $newStatus;
        $transaction->save();

        // If transaction is approved and was not approved before, deduct stock of purchased items
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            foreach ($transaction->items as $item) {
                if ($item->shoe) {
                    $shoe = $item->shoe;
                    $shoe->stock = max(0, $shoe->stock - $item->quantity);
                    $shoe->save();
                }
            }
        }
        
        // If status changes back from approved to pending/rejected, restore stock
        if ($oldStatus === 'approved' && $newStatus !== 'approved') {
            foreach ($transaction->items as $item) {
                if ($item->shoe) {
                    $shoe = $item->shoe;
                    $shoe->stock = $shoe->stock + $item->quantity;
                    $shoe->save();
                }
            }
        }

        return redirect()->route('admin.transactions.show', $id)->with('success', 'Status transaksi berhasil diperbarui!');
    }
}
