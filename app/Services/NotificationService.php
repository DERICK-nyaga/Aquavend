<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a customer and log it.
     * Actual SMS/WhatsApp sending is stubbed for now — swap in a real provider later.
     */
    public function send(Customer $customer, string $event, string $message, string $channel = 'sms'): NotificationLog
    {
        $log = NotificationLog::create([
            'customer_id' => $customer->id,
            'channel' => $channel,
            'event' => $event,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            $this->dispatch($customer, $channel, $message);
            $log->update(['status' => 'sent']);
        } catch (\Exception $e) {
            Log::error("Notification failed: {$e->getMessage()}");
            $log->update(['status' => 'failed']);
        }

        return $log;
    }

    private function dispatch(Customer $customer, string $channel, string $message): void
    {
        // TODO: wire up a real provider (Africa's Talking, Twilio, WhatsApp Cloud API, etc.)
        // For now this just logs — replace this method body once you pick a provider.
        Log::info("[{$channel}] To: {$customer->phone} — {$message}");
    }

    // Convenience helpers for common events

    public function orderConfirmation(Customer $customer, float $amount): void
    {
        $this->send(
            $customer,
            'order_confirmation',
            "Hi {$customer->name}, your order of KES {$amount} has been confirmed. Thank you for choosing Aquavend!"
        );
    }

    public function lowWalletBalance(Customer $customer): void
    {
        $this->send(
            $customer,
            'low_wallet',
            "Hi {$customer->name}, your Aquavend wallet balance is low (KES {$customer->wallet_balance}). Top up to keep ordering."
        );
    }

    public function creditReminder(Customer $customer): void
    {
        $this->send(
            $customer,
            'credit_reminder',
            "Hi {$customer->name}, you have an outstanding balance of KES {$customer->credit_balance} with Aquavend. Please settle when convenient."
        );
    }
}