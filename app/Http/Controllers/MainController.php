<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Theme;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function index(){
        return view('frontend.index');
    }
}
