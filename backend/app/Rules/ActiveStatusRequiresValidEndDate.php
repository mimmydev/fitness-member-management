<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class ActiveStatusRequiresValidEndDate implements Rule
{
    private $endDate;

    public function __construct($endDate)
    {
        $this->endDate = $endDate;
    }

    public function passes($attribute, $value)
    {
        // If status is not active, validation passes
        if ($value !== 'active') {
            return true;
        }

        // If status is active but no end date, passes (indefinite membership)
        if (!$this->endDate) {
            return true;
        }

        // If status is active and end date exists, must be today or future
        return Carbon::parse($this->endDate)->isFuture() ||
               Carbon::parse($this->endDate)->isToday();
    }

    public function message()
    {
        return 'Cannot set status as "active" when membership end date is in the past.';
    }
}
