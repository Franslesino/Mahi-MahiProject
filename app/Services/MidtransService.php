<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
        Config::$appendNotifUrl = config('midtrans.append_notif_url');
    }

    /**
     * Generate Snap Token untuk payment page
     */
    public function generateSnapToken(Transaction $transaction)
    {
        try {
            $transaction->load(['user', 'kursus']);

            $transactionDetails = [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total_bayar,
            ];

            $customerDetails = [
                'first_name' => $transaction->user->first_name ?? $transaction->user->name,
                'last_name' => $transaction->user->last_name ?? '',
                'email' => $transaction->user->email,
                'phone' => $transaction->user->phone ?? '',
            ];

            $itemDetails = [
                [
                    'id' => 'COURSE-' . $transaction->kursus_id,
                    'price' => (int) $transaction->total_bayar,
                    'quantity' => 1,
                    'name' => $transaction->kursus->judul ?? 'Kursus',
                ]
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('transactions.finish'),
                    'unfinish' => route('transactions.unfinish'),
                    'error' => route('transactions.error'),
                ]
            ];

            $snapToken = Snap::getSnapToken($payload);

            return $snapToken;

        } catch (Exception $e) {
            \Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw new Exception('Gagal membuat token pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Cek status transaksi dari Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (Exception $e) {
            \Log::error('Midtrans Status Check Error: ' . $e->getMessage());
            throw new Exception('Gagal mengecek status transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Approve transaction (manual)
     */
    public function approveTransaction($orderId)
    {
        try {
            $response = \Midtrans\Transaction::approve($orderId);
            return $response;
        } catch (Exception $e) {
            \Log::error('Midtrans Approve Error: ' . $e->getMessage());
            throw new Exception('Gagal approve transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction($orderId)
    {
        try {
            $response = \Midtrans\Transaction::cancel($orderId);
            return $response;
        } catch (Exception $e) {
            \Log::error('Midtrans Cancel Error: ' . $e->getMessage());
            throw new Exception('Gagal cancel transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Refund transaction
     */
    public function refundTransaction($orderId, $amount = null)
    {
        try {
            if ($amount) {
                $response = \Midtrans\Transaction::refund($orderId, $amount);
            } else {
                $response = \Midtrans\Transaction::refund($orderId);
            }
            return $response;
        } catch (Exception $e) {
            \Log::error('Midtrans Refund Error: ' . $e->getMessage());
            throw new Exception('Gagal refund transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function handleNotification($notification)
    {
        try {
            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $paymentType = $notification['payment_type'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            $transaction = Transaction::where('transaction_code', $orderId)->first();

            if (!$transaction) {
                throw new Exception('Transaction not found: ' . $orderId);
            }

            // Update payment details
            $transaction->payment_method = $paymentType;
            $transaction->payment_details = $notification;
            $transaction->snap_token = null;

            // Handle transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $transaction->status = 'pending';
                } elseif ($fraudStatus == 'accept') {
                    $transaction->status = 'paid';
                    $transaction->paid_at = now();
                }
            } elseif ($transactionStatus == 'settlement') {
                $transaction->status = 'paid';
                $transaction->paid_at = now();
            } elseif ($transactionStatus == 'pending') {
                $transaction->status = 'pending';
            } elseif ($transactionStatus == 'deny') {
                $transaction->status = 'cancelled';
            } elseif ($transactionStatus == 'cancel') {
                $transaction->status = 'cancelled';
            } elseif ($transactionStatus == 'expire') {
                $transaction->status = 'expired';
                $transaction->expired_at = now();
            } elseif ($transactionStatus == 'refund') {
                $transaction->status = 'refunded';
            }

            $transaction->save();

            return $transaction;

        } catch (Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage());
            throw new Exception('Gagal handle notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Verify notification signature
     */
    public function verifyNotificationSignature($orderId, $statusCode, $grossAmount, $serverKey)
    {
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return $signature;
    }
}
