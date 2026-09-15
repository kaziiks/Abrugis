<?php

namespace App\Http\Controllers;

use App\Models\Atsauksme;
use App\Models\Pieteikums;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtsauksmeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pieteikums_id' => ['required', 'integer', 'exists:pieteikums,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'atsauksme' => ['nullable', 'string', 'max:2000'],
        ], [
            'pieteikums_id.required' => 'Lūdzu, izvēlieties projektu, par kuru vēlaties atstāt atsauksmi.',
            'rating.required' => 'Lūdzu, izvēlieties vērtējumu.',
            'rating.min' => 'Vērtējumam jābūt no 1 līdz 5 zvaigznēm.',
            'rating.max' => 'Vērtējumam jābūt no 1 līdz 5 zvaigznēm.',
        ]);

        $pieteikums = Pieteikums::whereKey($validated['pieteikums_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        if ($pieteikums->review()->exists()) {
            return back()->withErrors(['pieteikums_id' => 'Atsauksme par šo projektu jau ir iesniegta.']);
        }

        Atsauksme::create([
            'pieteikums_id' => $pieteikums->id,
            'author_name' => $pieteikums->client_name,
            'rating' => $validated['rating'],
            'atsauksme' => $validated['atsauksme'],
        ]);

        return back()->with('success', 'Paldies par jūsu atsauksmi!');
    }
}
