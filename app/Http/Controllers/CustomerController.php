<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class CustomerController extends Controller
{
    public function index()
    {
        $produks = Produk::with('diskon')->get();
        return view('customer.dashboard', compact('produks'));
    }
}