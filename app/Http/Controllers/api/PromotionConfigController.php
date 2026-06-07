<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\PromotionConfig;
use Illuminate\Http\Request;

class PromotionConfigController extends Controller
{
    public function index(Request $request)
    {
        $scholarYearId = $request->query('scholar_year_id');

        $query = PromotionConfig::with(['overrides', 'academicLevel', 'scholarYear'])
            ->where('status', 1);

        if ($scholarYearId) {
            $query->where('scholar_year_id', $scholarYearId);
        }

        return response()->json(ApiResponse::success('Promotion configs retrieved', $query->get()));
    }

    public function store(Request $request)
    {
        $request->validate([
            'default_day' => 'required|integer|min:1|max:31',
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
            'scholar_year_id' => 'required|exists:scholar_years,id',
            'overrides' => 'nullable|array',
            'overrides.*.month' => 'required|integer|min:1|max:12',
            'overrides.*.deadline_date' => 'required|date',
        ]);

        $config = PromotionConfig::create($request->only([
            'default_day', 'academic_level_id', 'scholar_year_id'
        ]));

        if ($request->has('overrides')) {
            foreach ($request->overrides as $override) {
                $config->overrides()->create($override);
            }
        }

        return response()->json(ApiResponse::success('Promotion config created', $config->load('overrides')));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:promotion_configs,id',
            'default_day' => 'required|integer|min:1|max:31',
            'overrides' => 'nullable|array',
            'overrides.*.month' => 'required|integer|min:1|max:12',
            'overrides.*.deadline_date' => 'required|date',
        ]);

        $config = PromotionConfig::findOrFail($request->id);
        $config->update(['default_day' => $request->default_day]);

        $config->overrides()->delete();
        if ($request->has('overrides')) {
            foreach ($request->overrides as $override) {
                $config->overrides()->create($override);
            }
        }

        return response()->json(ApiResponse::success('Promotion config updated', $config->load('overrides')));
    }

    public function delete($id)
    {
        $config = PromotionConfig::findOrFail($id);
        $config->delete();

        return response()->json(ApiResponse::success('Promotion config deleted', []));
    }
}
