<?php

namespace Tests\Unit\Normalize;

use Tests\TestCase;
use App\Services\Normalize\DiagnosisNormalizeEngine;

class DiagnosisNormalizeEngineTest extends TestCase
{
    public function test_hd_and_ed_normalization_basic()
    {
        $engine = new DiagnosisNormalizeEngine();

        $result = $engine->normalize([
            'hd'    => 'HD A',
            'ed'    => '0',
            'other' => 'boas 1',
        ], 'kuvasz');

        $this->assertEquals('HD-A', $result['diagnosisHdNormalized']);
        $this->assertEquals('ED-0', $result['diagnosisEdNormalized']);
        $this->assertEquals('BOAS-1', $result['diagnosisOtherNormalized']);
    }
}