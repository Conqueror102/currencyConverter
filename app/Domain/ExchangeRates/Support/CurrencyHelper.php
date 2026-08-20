<?php

namespace App\Domain\ExchangeRates\Support;

class CurrencyHelper
{
    private const SPECIAL_FLAGS = [
        'EUR' => '🇪🇺',
        'USD' => '🇺🇸',
        'GBP' => '🇬🇧',
        'NGN' => '🇳🇬',
        'JPY' => '🇯🇵',
        'CAD' => '🇨🇦',
        'AUD' => '🇦🇺',
        'CHF' => '🇨🇭',
        'CNY' => '🇨🇳',
        'GHS' => '🇬🇭',
        'KES' => '🇰🇪',
        'ARS' => '🇦🇷',
        'ZAR' => '🇿🇦',
        'EGP' => '🇪🇬',
        'VES' => '🇻🇪',
        'LBP' => '🇱🇧',
        'AED' => '🇦🇪',
        'INR' => '🇮🇳',
        'BRL' => '🇧🇷',
        'SGD' => '🇸🇬',
        'NZD' => '🇳🇿',
        'SEK' => '🇸🇪',
        'NOK' => '🇳🇴',
        'DKK' => '🇩🇰',
        'PLN' => '🇵🇱',
        'TRY' => '🇹🇷',
        'MXN' => '🇲🇽',
        'THB' => '🇹🇭',
        'IDR' => '🇮🇩',
        'PHP' => '🇵🇭',
        'MYR' => '🇲🇾',
        'VND' => '🇻🇳',
        'ILS' => '🇮🇱',
        'CLP' => '🇨🇱',
        'COP' => '🇨🇴',
        'PEN' => '🇵🇪',
        'KWD' => '🇰🇼',
        'QAR' => '🇶🇦',
        'SAR' => '🇸🇦',
        'BHD' => '🇧🇭',
        'OMR' => '🇴🇲',
        'JOD' => '🇯🇴',
        'CZK' => '🇨🇿',
        'HUF' => '🇭🇺',
        'RON' => '🇷🇴',
        'BGN' => '🇧🇬',
        'ISK' => '🇮🇸',
        'KRW' => '🇰🇷',
        'HKD' => '🇭🇰',
        'TWD' => '🇹🇼',
    ];

    private const CURRENCY_NAMES = [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'NGN' => 'Nigerian Naira',
        'JPY' => 'Japanese Yen',
        'CAD' => 'Canadian Dollar',
        'AUD' => 'Australian Dollar',
        'CHF' => 'Swiss Franc',
        'CNY' => 'Chinese Yuan',
        'GHS' => 'Ghanaian Cedi',
        'KES' => 'Kenyan Shilling',
        'ARS' => 'Argentine Peso',
        'ZAR' => 'South African Rand',
        'EGP' => 'Egyptian Pound',
        'VES' => 'Venezuelan Bolívar',
        'LBP' => 'Lebanese Pound',
        'AED' => 'UAE Dirham',
        'INR' => 'Indian Rupee',
        'BRL' => 'Brazilian Real',
        'SGD' => 'Singapore Dollar',
        'NZD' => 'New Zealand Dollar',
        'SEK' => 'Swedish Krona',
        'NOK' => 'Norwegian Krone',
        'DKK' => 'Danish Krone',
        'PLN' => 'Polish Zloty',
        'TRY' => 'Turkish Lira',
        'MXN' => 'Mexican Peso',
        'THB' => 'Thai Baht',
        'IDR' => 'Indonesian Rupiah',
        'PHP' => 'Philippine Peso',
        'MYR' => 'Malaysian Ringgit',
        'VND' => 'Vietnamese Dong',
        'ILS' => 'Israeli Shekel',
        'CLP' => 'Chilean Peso',
        'COP' => 'Colombian Peso',
        'PEN' => 'Peruvian Sol',
        'KWD' => 'Kuwaiti Dinar',
        'QAR' => 'Qatari Riyal',
        'SAR' => 'Saudi Riyal',
        'BHD' => 'Bahraini Dinar',
        'OMR' => 'Omani Rial',
        'JOD' => 'Jordanian Dinar',
        'CZK' => 'Czech Koruna',
        'HUF' => 'Hungarian Forint',
        'RON' => 'Romanian Leu',
        'BGN' => 'Bulgarian Lev',
        'ISK' => 'Icelandic Krona',
        'KRW' => 'South Korean Won',
        'HKD' => 'Hong Kong Dollar',
        'TWD' => 'Taiwan Dollar',
    ];

    private const CURRENCY_SYMBOLS = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'NGN' => '₦',
        'JPY' => '¥',
        'CAD' => 'CA$',
        'AUD' => 'AU$',
        'CHF' => 'CHF',
        'CNY' => '¥',
        'GHS' => 'GH₵',
        'KES' => 'KSh',
        'ARS' => '$',
        'ZAR' => 'R',
        'EGP' => 'E£',
        'VES' => 'Bs.',
        'LBP' => 'L£',
        'AED' => 'AED',
        'INR' => '₹',
        'BRL' => 'R$',
        'SGD' => 'S$',
        'NZD' => 'NZ$',
    ];

    /**
     * Get flag emoji for ISO currency code.
     */
    public static function flag(string $currencyCode): string
    {
        $code = strtoupper(trim($currencyCode));

        if (isset(self::SPECIAL_FLAGS[$code])) {
            return self::SPECIAL_FLAGS[$code];
        }

        if (strlen($code) >= 2) {
            $country = substr($code, 0, 2);
            $first = ord($country[0]);
            $second = ord($country[1]);

            if ($first >= 65 && $first <= 90 && $second >= 65 && $second <= 90) {
                return mb_chr(0x1F1E6 + ($first - 65)).mb_chr(0x1F1E6 + ($second - 65));
            }
        }

        return '🌐';
    }

    /**
     * Get clean currency name.
     */
    public static function name(string $currencyCode): string
    {
        $code = strtoupper(trim($currencyCode));

        return self::CURRENCY_NAMES[$code] ?? $code;
    }

    /**
     * Get currency symbol.
     */
    public static function symbol(string $currencyCode): string
    {
        $code = strtoupper(trim($currencyCode));

        return self::CURRENCY_SYMBOLS[$code] ?? $code;
    }
}
