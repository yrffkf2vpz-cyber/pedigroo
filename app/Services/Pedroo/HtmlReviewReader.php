<?php

namespace App\Services\Pedroo;

class HtmlReviewReader
{
    public function readCategory(string $category): array
    {
        $basePath = public_path("pedroo-review/{$category}");
        $results = [];

        foreach (glob($basePath . '/*.html') as $filePath) {

            $fileName = basename($filePath); // pl. 1.html
            $number = intval($fileName);     // 1
            $id = sprintf("%s-%04d", $category, $number);

            $html = file_get_contents($filePath);

            $review = $this->extractReviewBlock($html);

            $results[] = [
                'id'       => $id,
                'category' => $category,
                'file'     => $fileName,
                'review'   => $review,
            ];
        }

        return $results;
    }

    private function extractReviewBlock(string $html): array
    {
        $pattern = '/<!--\s*PEDROO-REVIEW:(.*?)-->/s';

        if (!preg_match($pattern, $html, $matches)) {
            return [
                'status'   => 'unknown',
                'pipeline' => null,
                'notes'    => null,
            ];
        }

        $block = trim($matches[1]);

        $lines = array_filter(array_map('trim', explode("\n", $block)));

        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = array_map('trim', explode(':', $line, 2));
                $data[$key] = $value;
            }
        }

        return [
            'status'   => $data['status']   ?? 'unknown',
            'pipeline' => $data['pipeline'] ?? null,
            'notes'    => $data['notes']    ?? null,
        ];
    }
}