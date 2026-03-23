<?php

namespace App\Http\Controllers;

use App\Services\Pedroo\HtmlReviewReader;

class PedrooConsoleController extends Controller
{
    public function index(HtmlReviewReader $reader)
    {
        $categories = ['controllers', 'models', 'services', 'listeners'];

        $data = [];

        foreach ($categories as $category) {
            $data[$category] = $reader->readCategory($category);
        }

        return view('pedroo.console.index', [
            'categories' => $categories,
            'data' => $data
        ]);
    }
}