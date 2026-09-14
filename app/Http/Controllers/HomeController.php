<?php

namespace App\Http\Controllers;

use App\Models\PortfolioInfo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'portfolio' => PortfolioInfo::latest()->get(),
        ]);
    }
}
