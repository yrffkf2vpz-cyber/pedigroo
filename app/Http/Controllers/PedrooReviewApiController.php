<?php

namespace App\Http\Controllers;

use App\Services\Pedroo\HtmlReviewReader;

class PedrooReviewApiController extends Controller
{
    public function index(HtmlReviewReader $reader)
    {
        $categories = ['controllers', 'models', 'services', 'listeners'];

        $items = [];

        foreach ($categories as $category) {
            foreach ($reader->readCategory($category) as $item) {
                $items[] = $item;
            }
        }

        return response()->json($items);
    }
}