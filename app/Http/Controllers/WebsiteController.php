<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function dashboard()
    {
        $websites = Website::latest()->get();
        $total = $websites->count();
        $online = $websites->where('status', 'Online')->count();
        $offline = $websites->where('status', 'Offline')->count();

        return view('dashboard.index', compact(
            'websites',
            'total',
            'online',
            'offline'
        ));
    }

public function index()
{
    $websites = Website::latest()->get();
    return view('website.index', compact('websites'));
}

    public function create()
    {
        return view('website.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_website' => 'required|max:100',
            'instansi' => 'required|max:100',
            'url' => 'required|url|max:255',
        ]);

        Website::create([
            'nama_website' => $request->nama_website,
            'instansi' => $request->instansi,
            'url' => $request->url,
            'status' => 'Belum Dicek',
            'monitoring_aktif' => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website berhasil ditambahkan.');

<<<<<<< HEAD
=======
        return view(
            'dashboard.admin.index',
            compact(
                'total',
                'online',
                'offline',
                'websites'
            )
        );
>>>>>>> 040da206f75aafbb01274d7dfc18f6b97c3d4e31
    }
}