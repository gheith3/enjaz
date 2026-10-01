<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchieverController extends Controller
{
    public function show($id)
    {
        // Hardcoded achiever data
        $achiever = [
            'id' => $id,
            'name' => 'د. جوخة الحارثية',
            'english_name' => 'Dr. Jokha Al-Harithiya',
            'title' => 'كاتبة وأديبة عُمانية',
        ];

        return view('single-achiever', compact('achiever'));
    }
} 