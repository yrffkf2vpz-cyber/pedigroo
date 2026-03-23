<?php

namespace App\Services\Normalizers\RegNo;

class RegNoResult
{
    public ?string $raw = null;
    public ?string $normalized = null;

    public ?string $prefix = null;
    public ?string $breed_code = null;
    public ?string $organization = null;
    public ?int    $year = null;
    public ?string $country = null;
    public ?string $sequence = null;
    public ?string $status = null; // ok / invalid / uncertain

    public bool $prefix_is_modern = false;

    // ÚJ: korszak besorolás
    public string $classification = 'modern'; // modern | historical | legacy

    public function __construct(array $data = [])
    {
        $this->raw              = $data['raw']              ?? null;
        $this->normalized       = $data['normalized']       ?? null;
        $this->prefix           = $data['prefix']           ?? null;
        $this->breed_code       = $data['breed_code']       ?? null;
        $this->organization     = $data['organization']     ?? null;
        $this->year             = $data['year']             ?? null;
        $this->country          = $data['country']          ?? null;
        $this->sequence         = $data['sequence']         ?? null;
        $this->status           = $data['status']           ?? null;
        $this->prefix_is_modern = $data['prefix_is_modern'] ?? false;
        $this->classification   = $data['classification']   ?? 'modern';
    }

    public function toArray(): array
    {
        return [
            'raw'              => $this->raw,
            'normalized'       => $this->normalized,
            'prefix'           => $this->prefix,
            'breed_code'       => $this->breed_code,
            'organization'     => $this->organization,
            'year'             => $this->year,
            'country'          => $this->country,
            'sequence'         => $this->sequence,
            'status'           => $this->status,
            'prefix_is_modern' => $this->prefix_is_modern,
            'classification'   => $this->classification,
        ];
    }
}