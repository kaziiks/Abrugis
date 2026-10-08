<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Review;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ], [
            'application_id.required' => 'Lūdzu, izvēlieties projektu, par kuru vēlaties atstāt atsauksmi.',
            'rating.required' => 'Lūdzu, izvēlieties vērtējumu.',
            'rating.min' => 'Vērtējumam jābūt no 1 līdz 5 zvaigznēm.',
            'rating.max' => 'Vērtējumam jābūt no 1 līdz 5 zvaigznēm.',
        ]);

        $application = Application::whereKey($validated['application_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        if ($application->review()->exists()) {
            return back()->withErrors(['application_id' => 'Atsauksme par šo projektu jau ir iesniegta.']);
        }

        try {
            Review::create([
                'application_id' => $application->id,
                'author_name' => $application->client_name,
                'rating' => $validated['rating'],
                'review' => $validated['review'],
            ]);
        } catch (QueryException $exception) {
            if (! $application->review()->exists()) {
                throw $exception;
            }

            return back()->withErrors(['application_id' => 'Atsauksme par šo projektu jau ir iesniegta.']);
        }

        return back()->with('success', 'Paldies par jūsu atsauksmi!');
    }
}
