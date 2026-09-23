<?php

namespace App\Http\Controllers;

use App\Models\Website;

class WebsiteController extends Controller
{
    public function dashboard()
    {
        $total = Website::count();

        $online = Website::where(
            'status',
            'Online'
        )->count();

        $offline = Website::where(
            'status',
            'Offline'
        )->count();

        $websites = Website::latest()->get();

        return view(
            'dashboard.admin.index',
            compact(
                'total',
                'online',
                'offline',
                'websites'
            )
        );
    }
}