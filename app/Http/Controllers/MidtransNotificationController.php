<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    /**
     * Handle Midtrans webhook notification
     * POST /api/midtrans/notification
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Midtrans Notification Received:', $request->all());

            // Get notification from request
            $notification = $request->all();

            // Verify signature
            $midtransService = new MidtransService();
            $orderId = $notification['order_id'] ?? null;
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $signatureKey = $notification['signature_key'] ?? null;

            $serverKey = config('midtrans.server_key');
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Invalid Midtrans signature', [
                    'order_id' => $orderId,
                    'expected' => $expectedSignature,
                    'actual' => $signatureKey
                ]);
                return response()->json(['status' => 'invalid signature'], 403);
            }

            // Find transaction
            $transaction = Transaction::where('transaction_code', $orderId)->first();

            if (!$transaction) {
                Log::warning('Transaction not found for order_id: ' . $orderId);
                return response()->json(['status' => 'transaction not found'], 404);
            }

            // Handle notification
            DB::beginTransaction();

            try {
                $transaction->handleMidtransNotification($notification);

                // If payment successful, create enrollment
                if ($transaction->status === 'paid' && !$transaction->kursus->students()->where('user_id', $transaction->user_id)->exists()) {
                    $this->createEnrollment($transaction);
                }

                DB::commit();

                Log::info('Midtrans notification processed successfully', [
                    'order_id' => $orderId,
                    'new_status' => $transaction->status
                ]);

                return response()->json(['status' => 'success'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing notification: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Create enrollment when payment successful
     */
    private function createEnrollment(Transaction $transaction)
    {
        try {
            $enrollment = \App\Models\Enrollment::create([
                'user_id' => $transaction->user_id,
                'kursus_id' => $transaction->kursus_id,
                'status_pendaftaran' => 'active',
                'tanggal_daftar' => now(),
            ]);

            // Notify instructor yang mengampuh kursus ini
            $course = $transaction->kursus;
            if ($course) {
                // Prioritas: instructor_id, jika tidak ada baru pembuat (admin)
                $instructorId = $course->instructor_id ?: $course->pembuat;
                
                // Hanya kirim notifikasi ke instructor, bukan admin
                if ($instructorId && $instructorId != $course->pembuat) {
                    \App\Models\Notification::create([
                        'user_id' => $instructorId,
                        'title'   => 'Pendaftar baru',
                        'message' => 'Pengguna ' . $transaction->user->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                        'type'    => 'info',
                    ]);
                } elseif ($instructorId == $course->pembuat && $course->pembuat) {
                    // Jika tidak ada instructor_id, kirim ke pembuat (admin/creator)
                    \App\Models\Notification::create([
                        'user_id' => $course->pembuat,
                        'title'   => 'Pendaftar baru',
                        'message' => 'Pengguna ' . $transaction->user->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                        'type'    => 'info',
                    ]);
                }
            }

            // Notify user
            \App\Models\Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Berhasil',
                'message' => 'Pembayaran untuk kursus "' . $course->judul . '" telah berhasil dikonfirmasi. Selamat belajar!',
                'type' => 'success',
            ]);

            Log::info('Enrollment created for transaction: ' . $transaction->transaction_code);

        } catch (\Exception $e) {
            Log::error('Error creating enrollment: ' . $e->getMessage());
            throw $e;
        }
    }
}
