<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Next()
 * @method static static Cancel()
 * @method static static Previous()
 */
final class NavigationActions extends Enum
{
    const Next = 'Next';
    const Cancel = 'Cancel';
    const Previous = 'Previous';
}
