<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    // Лента автомобилей друзей
    public function index()
    {
        $user = auth()->user();
        $cars = $user->friendsCarsFeed();
        
        return view('feed.index', [
            'cars' => $cars,
            'header' => 'Лента друзей'
        ]);
    }
}