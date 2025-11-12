<?php
declare(strict_types=1);
namespace MyApp\Utils;

enum AccountCurrency: string {
    case USD = 'USD';
    case NGN = 'NGN';
}

/**
 * Handles transaction fee calculations and currency conversion
 * for funding virtual cards and card creation fees.
 */
class TransactionService {
    private AccountCurrency $accountCurrency;
    private float $exchangeRate; // e.g. 1 USD = 1500 NGN

    /**
     * @param AccountCurrency $accountCurrency  User's account currency (USD or NGN)
     * @param float $exchangeRate              Conversion rate (1 USD = ? NGN)
     */
    public function __construct(AccountCurrency $accountCurrency, float $exchangeRate) {
        $this->accountCurrency = $accountCurrency;
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Calculate the funding charge.
     * Rule: $1 for every $50 funded (rounded up).
     *
     * @param float $amountUsd     Funding amount in USD
     * @param float $fundingCharge Charge applied per $50
     * @return float               Total charge in USD
     */
    public function calculateFundingCharge(float $amountUsd, float $fundingCharge): float {
        if ($amountUsd <= 0) return 0;
        return ceil($amountUsd / 50) * $fundingCharge;
    }

    /**
     * Convert amount between USD and NGN using the exchange rate.
     *
     * @param float $amount        Amount to convert
     * @param AccountCurrency $from Source currency
     * @param AccountCurrency $to   Target currency
     * @return float                Converted amount
     */
    public function convertCurrency(float $amount, AccountCurrency $from, AccountCurrency $to): float {
        if ($from === $to) return $amount;

        if ($from === AccountCurrency::USD && $to === AccountCurrency::NGN) {
            return $amount * $this->exchangeRate;
        } elseif ($from === AccountCurrency::NGN && $to === AccountCurrency::USD) {
            return $amount / $this->exchangeRate;
        }

        return $amount;
    }

    /**
     * Convert a USD charge into the user's account currency.
     *
     * @param float $amountUsdCharge Charge amount in USD
     * @return float                 Charge in user's currency
     */
    public function getChargeInUserCurrency(float $amountUsdCharge): float {
        if ($this->accountCurrency === AccountCurrency::USD) return $amountUsdCharge;
        return $this->convertCurrency($amountUsdCharge, AccountCurrency::USD, AccountCurrency::NGN);
    }

    /**
     * Calculate total funding cost (funding amount + charge).
     *
     * @param float $fundingAmountUsd Funding amount in USD
     * @param float $fundingCharge    Charge applied per $50
     * @return float                  Total cost in user's account currency
     */
    public function calculateTotalFundingCost(float $fundingAmountUsd, float $fundingCharge): float {
        $chargeUsd = $this->calculateFundingCharge($fundingAmountUsd, $fundingCharge);
        $totalUsd = $fundingAmountUsd + $chargeUsd;
        return $this->getChargeInUserCurrency($totalUsd);
    }

    public function calculateTotalWithdrawingCost(float $amountUsd, float $withdrawalCharge): float
    {
        return $amountUsd + $withdrawalCharge;
    }

    /**
     * Calculate the flat card creation fee in user's currency.
     *
     * @param float $creationFeeUsd Card creation fee in USD
     * @return float                Fee in user's account currency
     */
    public function calculateCardCreationFee(float $creationFeeUsd): float {
        return $this->getChargeInUserCurrency($creationFeeUsd);
    }
}

// Example usage:
// $service = new TransactionService(AccountCurrency::NGN, 1500);

// Funding example: fund $120, charge $1 per $50
// $fundingCost = $service->calculateTotalFundingCost(120, 1);
// echo "Total funding cost in NGN: ₦" . $fundingCost . PHP_EOL;

// Card creation example: flat $3 fee
// $cardFee = $service->calculateCardCreationFee(3);
// echo "Card creation fee in NGN: ₦" . $cardFee . PHP_EOL;