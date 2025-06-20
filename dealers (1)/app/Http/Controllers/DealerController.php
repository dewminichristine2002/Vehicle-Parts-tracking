<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::orderBy('last_login_at', 'desc')->get();
        return view('admin.index', compact('dealers'));
    }

    public function show(Dealer $dealer)
    {
        return view('admin.show', compact('dealer'));
    }
}