<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk fitur voucher.
 */
class VoucherController extends Controller
{
    /**
     * Memvalidasi voucher.
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'course_id' => 'required|exists:kursus,id',
            'price' => 'required|numeric|min:0',
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk menggunakan voucher.'
            ], 401);
        }

        $userId = Auth::id();
        $voucher = Voucher::where('code', strtoupper($request->voucher_code))->first();

        // Check if voucher exists
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak ditemukan.'
            ]);
        }

        // Check if voucher is valid
        if (!$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah tidak berlaku atau telah mencapai batas penggunaan.'
            ]);
        }

        // Check if user can use this voucher
        if (!$voucher->canBeUsedBy($userId, $request->course_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menggunakan voucher ini atau sudah pernah menggunakannya.'
            ]);
        }

        // Check minimum purchase
        if ($voucher->min_purchase > 0 && $request->price < $voucher->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal pembelian Rp ' . number_format($voucher->min_purchase, 0, ',', '.') . ' untuk menggunakan voucher ini.'
            ]);
        }

        // Calculate discount
        $discount = $voucher->calculateDiscount($request->price);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'description' => $voucher->description,
                'discount_text' => $voucher->discount_text,
            ],
            'discount' => $discount,
            'final_price' => $request->price - $discount,
        ]);
    }
}