<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;

class PlaygroundController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SystemControl/Playground');
    }

    public function showError($status)
    {
        return Inertia::render('Error', ['status' => (int) $status])
            ->toResponse(request())
            ->setStatusCode((int) $status);
    }
}
