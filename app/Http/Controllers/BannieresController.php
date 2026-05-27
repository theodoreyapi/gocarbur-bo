<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannieresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderByDesc('created_at')->get();

        $stats = [
            'active_count'      => $banners->where('is_active', true)->count(),
            'impressions_total' => $banners->sum('impressions_count'),
            'clicks_total'      => $banners->sum('clicks_count'),
            'ctr_avg'           => $banners->sum('impressions_count') > 0
                ? round(($banners->sum('clicks_count') / $banners->sum('impressions_count')) * 100, 1)
                : 0,
        ];

        $positionLabels = Banner::positionLabels();
        $targetLabels   = Banner::targetLabels();

        return view('pages.banners', compact('banners', 'stats', 'positionLabels', 'targetLabels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'position'        => 'required|in:home_top,home_middle,map_bottom,stations_list,garages_list,articles_list,splash',
            'advertiser_name' => 'nullable|string|max:255',
            'starts_at'       => 'required|date',
            'ends_at'         => 'required|date|after_or_equal:starts_at',
            'action_url'      => 'nullable|url|max:500',
            'target_type'     => 'required|in:all,free_users,premium_users,city',
            'target_city'     => 'nullable|required_if:target_type,city|string|max:100',
            'price_paid'      => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('banners', 'public');
            $imageUrl = Storage::url($path);
        }

        Banner::create([
            ...$validated,
            'image_url' => $imageUrl ?? '',
            'is_active' => true,
        ]);

        return redirect()->route('banners.index')
            ->with('toast_success', 'Bannière créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Returns JSON for modal population via AJAX
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        // Toggle-only request (is_active)
        if ($request->has('is_active') && count($request->all()) === 2) {
            $banner->update(['is_active' => $request->boolean('is_active')]);
            $label = $banner->is_active ? 'activée' : 'désactivée';
            return redirect()->route('banners.index')
                ->with('toast_info', "Bannière {$label}.");
        }

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'position'        => 'required|in:home_top,home_middle,map_bottom,stations_list,garages_list,articles_list,splash',
            'advertiser_name' => 'nullable|string|max:255',
            'starts_at'       => 'required|date',
            'ends_at'         => 'required|date|after_or_equal:starts_at',
            'action_url'      => 'nullable|url|max:500',
            'target_type'     => 'required|in:all,free_users,premium_users,city',
            'target_city'     => 'nullable|required_if:target_type,city|string|max:100',
            'price_paid'      => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if stored locally
            if ($banner->image_url && str_starts_with($banner->image_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image_url));
            }
            $path                  = $request->file('image')->store('banners', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $banner->update($validated);

        return redirect()->route('banners.index')
            ->with('toast_success', 'Bannière mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image_url && str_starts_with($banner->image_url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image_url));
        }

        $banner->delete();

        return redirect()->route('banners.index')
            ->with('toast_error', 'Bannière supprimée.');
    }
}
