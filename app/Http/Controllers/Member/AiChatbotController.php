<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;

class AiChatbotController extends Controller
{
    public function index(Request $request, Business $business)
    {
        return Inertia::render('Member/AiChatbot/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }
}
