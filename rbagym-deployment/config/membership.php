<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Membership Plan Types
    |--------------------------------------------------------------------------
    |
    | This array defines the available membership plan types and their base prices.
    | Prices are per month.
    |
    */
    'plan_types' => [
        'basic' => [
            'name' => 'Basic',
            'base_price' => 50.00,
            'description' => 'Basic gym access with standard facilities',
        ],
        'vip' => [
            'name' => 'VIP',
            'base_price' => 100.00,
            'description' => 'Premium access with additional amenities',
        ],
        'premium' => [
            'name' => 'Premium',
            'base_price' => 150.00,
            'description' => 'Full access with all premium features',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Duration Types
    |--------------------------------------------------------------------------
    |
    | This array defines the available duration types and their multipliers.
    | The multiplier is applied to the base monthly price.
    |
    */
    'duration_types' => [
        'monthly' => [
            'name' => 'Monthly',
            'multiplier' => 1,
            'days' => 30,
        ],
        'quarterly' => [
            'name' => 'Quarterly',
            'multiplier' => 3,
            'days' => 90,
        ],
        'biannually' => [
            'name' => 'Biannually',
            'multiplier' => 6,
            'days' => 180,
        ],
        'annually' => [
            'name' => 'Annually',
            'multiplier' => 12,
            'days' => 365,
        ],
    ],

    'payment_statuses' => [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Membership Statuses
    |--------------------------------------------------------------------------
    |
    | Available membership statuses.
    |
    */
    'membership_statuses' => [
        'active' => 'Active',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ],
];
