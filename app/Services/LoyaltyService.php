<?php

namespace App\Services;

use App\Models\Customer;
use Carbon\Carbon;

class LoyaltyService
{
    //Check if customer qualifies for the once-a-month loyalty discount.
    public function isEligible(?Customer $customer): bool
    {
        if (!$customer) {
            return false;
        }

        // 1. Check if discount was already used this calendar month
        if ($customer->last_loyalty_discount_at && 
            Carbon::parse($customer->last_loyalty_discount_at)->isCurrentMonth()) {
            return false;
        }

        // 2. Check consistency: at least 3 completed/delivered orders in the last 30 days
        $recentOrdersCount = $customer->transactions()
            ->whereIn('status', ['completed', 'delivered'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return $recentOrdersCount >= 3;
    }

    //Calculate discount breakdown for order items.
    public function calculateDiscount(?Customer $customer, array $cartItems): float
    {
        if (!$this->isEligible($customer)) {
            return 0.00;
        }

        $waterSubtotal = 0.00;
        $otherSubtotal = 0.00;

        foreach ($cartItems as $item) {
            $itemSubtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 0);

            // Safely check product category/type
            if (!empty($item['is_water'])) {
                $waterSubtotal += $itemSubtotal;
            } else {
                $otherSubtotal += $itemSubtotal;
            }
        }

        // Rule 1: 30% discount on Water products
        $waterDiscount = $waterSubtotal * 0.30;

        // Rule 2: 10% discount on Other products if subtotal > KES 1,000
        $otherDiscount = ($otherSubtotal > 1000) ? ($otherSubtotal * 0.10) : 0.00;

        return round($waterDiscount + $otherDiscount, 2);
    }

    // Mark the loyalty discount as used for the current month.
    public function markDiscountAsUsed(Customer $customer): void
    {
        $customer->update([
            'last_loyalty_discount_at' => now(),
        ]);
    }

     //Builds the server-verified cart items array (price + is_water from DB, not client input).
    public function buildVerifiedCartItems(array $items): array
    {
        $cartItems = [];

        foreach ($items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if (!$product) continue;

            $cartItems[] = [
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'is_water' => $product->category === 'water',
            ];
        }

        return $cartItems;
    }
}