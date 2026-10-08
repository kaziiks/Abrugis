<?php

namespace App\Services;

use App\Models\PavingType;

class CalculatorEstimateService
{
    /**
     * @param  array{area: int|float|string, paving_id: int|string, base: string, removal?: bool|string|int}  $inputs
     * @return array<string, int|float|string|bool>
     */
    public function calculate(array $inputs): array
    {
        $pavingType = PavingType::findOrFail($inputs['paving_id']);
        $area = (float) $inputs['area'];
        $pavingPrice = (float) $pavingType->price_per_m2;
        $basePrice = (float) config('abrugis.calculator.base_options')[$inputs['base']];
        $removal = (bool) ($inputs['removal'] ?? false);
        $removalPrice = $removal
            ? (float) config('abrugis.calculator.removal_price_per_m2')
            : 0.0;

        return [
            'area' => $area,
            'paving_type_id' => $pavingType->id,
            'paving_name' => $pavingType->name,
            'paving_price_per_m2' => $pavingPrice,
            'base' => $inputs['base'],
            'base_price_per_m2' => $basePrice,
            'removal' => $removal,
            'removal_price_per_m2' => $removalPrice,
            'paving_total' => $area * $pavingPrice,
            'base_total' => $area * $basePrice,
            'removal_total' => $area * $removalPrice,
            'total' => $area * ($pavingPrice + $basePrice + $removalPrice),
        ];
    }
}
