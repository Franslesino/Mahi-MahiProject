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
        
        // Tentukan CA certificate path dengan fallback strategy
        $caInfoPath = $this->getCACertificatePath();
        
        // Paksa cURL memakai CA bundle yang ada agar tidak bergantung pada path lama di php.ini
        Config::$curlOptions = [
            CURLOPT_CAINFO => $caInfoPath,
            // Midtrans library mengharapkan key ini ada saat merge header, jadi set kosong untuk hindari undefined array key
            CURLOPT_HTTPHEADER => [],
        ];
    }

    /**
     * Determine the appropriate CA certificate path with fallback strategy
     * Supports: .env configuration, Laragon, Composer bundle, PHP built-in
     */
    private function getCACertificatePath()
    {
        // 1. Check if MIDTRANS_CAINFO is configured in .env
        $envCaInfo = config('midtrans.cainfo');
        if ($envCaInfo && file_exists($envCaInfo)) {
            return $envCaInfo;
        }

        // 2. Try Laragon Windows path
        $laragonPath = 'C:\\laragon\\etc\\ssl\\cacert.pem';
        if (file_exists($laragonPath)) {
            return $laragonPath;
        }

        // 3. Try Composer's CA bundle
        $composerCertPath = base_path('vendor/composer/ca-bundle/res/cacert.pem');
        if (file_exists($composerCertPath)) {
            return $composerCertPath;
        }

        // 4. Try PHP's built-in OpenSSL CA bundle
        $phpCertPath = php_ini_loaded_file() ? dirname(php_ini_loaded_file()) . '/cacert.pem' : null;
        if ($phpCertPath && file_exists($phpCertPath)) {
            return $phpCertPath;
        }

        // 5. Last resort: Use PHP's default (may not work for all systems)
        return 'php://openssl.cacert.pem';
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

            // Extract payment channel from notification
            $paymentChannel = null;
            if ($paymentType === 'bank_transfer' && isset($notification['bank'])) {
                $paymentChannel = strtoupper($notification['bank']);
            } elseif ($paymentType === 'echannel' && isset($notification['bank'])) {
                $paymentChannel = strtoupper($notification['bank']);
            } elseif (in_array($paymentType, ['gopay', 'ovo', 'dana', 'shopeepay', 'qris'])) {
                $paymentChannel = strtoupper($paymentType);
            } elseif ($paymentType === 'credit_card' && isset($notification['issuer'])) {
                $paymentChannel = strtoupper($notification['issuer']);
            }

            // Update payment details
            $transaction->payment_method = $paymentType;
            $transaction->payment_channel = $paymentChannel;
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

    /**
     * Get transaction details from Midtrans (payment method and channel info)
     */
    public function getTransactionDetails($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            
            // Convert object to array
            $statusArray = json_decode(json_encode($status), true);
            
            $paymentType = $statusArray['payment_type'] ?? null;
            $paymentChannel = null;
            
            // Extract payment channel based on payment type
            if ($paymentType === 'bank_transfer' && isset($statusArray['bank'])) {
                $paymentChannel = strtoupper($statusArray['bank']);
            } elseif ($paymentType === 'echannel' && isset($statusArray['bank'])) {
                $paymentChannel = strtoupper($statusArray['bank']);
            } elseif (in_array($paymentType, ['gopay', 'ovo', 'dana', 'shopeepay', 'qris'])) {
                $paymentChannel = strtoupper($paymentType);
            } elseif ($paymentType === 'credit_card' && isset($statusArray['issuer'])) {
                $paymentChannel = strtoupper($statusArray['issuer']);
            }

            return [
                'payment_type' => $paymentType,
                'payment_channel' => $paymentChannel,
                'transaction_status' => $statusArray['transaction_status'] ?? null,
                'full_details' => $status,
            ];

        } catch (Exception $e) {
            \Log::error('Midtrans Get Transaction Details Error: ' . $e->getMessage());
            throw new Exception('Gagal mengambil detail transaksi dari Midtrans: ' . $e->getMessage());
        }
    }
}
