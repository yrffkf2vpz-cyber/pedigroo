<?php

namespace App\Pedroo\Intelligence\Color;

class PuppyPredictor
{
    /**
     * Két szülő genotípusai → lehetséges kölyök genotípusok + arányok
     *
     * @param array $sireGenotypes ['M' => 'M/m', 'E' => 'E/e', ...]
     * @param array $damGenotypes  ['M' => 'm/m', 'E' => 'E/e', ...]
     */
    public function predict(array $sireGenotypes, array $damGenotypes): array
    {
        $result = [];

        // Végigmegyünk minden génen, ami mindkét szülőnél ismert
        foreach ($sireGenotypes as $gene => $sireGenotype) {
            if (!isset($damGenotypes[$gene])) {
                continue;
            }

            $damGenotype = $damGenotypes[$gene];

            $offspring = $this->punnettForGene($sireGenotype, $damGenotype);

            $result[$gene] = $offspring;
        }

        return $result;
    }

    /**
     * Egy génre Punnett-négyzet számítása
     *
     * Pl.: "M/m" × "m/m" →:
     *  - 50% M/m
     *  - 50% m/m
     */
    private function punnettForGene(string $sireGenotype, string $damGenotype): array
    {
        $sireAlleles = $this->splitGenotype($sireGenotype); // pl. ['M', 'm']
        $damAlleles  = $this->splitGenotype($damGenotype);  // pl. ['m', 'm']

        $combinations = [];

        foreach ($sireAlleles as $sa) {
            foreach ($damAlleles as $da) {
                $child = $this->normalizeGenotype($sa, $da);
                $combinations[$child] = ($combinations[$child] ?? 0) + 1;
            }
        }

        // 4 kombináció maximum → százalékosítás
        $total = array_sum($combinations);
        $result = [];

        foreach ($combinations as $genotype => $count) {
            $result[$genotype] = ($count / $total) * 100;
        }

        return $result;
    }

    /**
     * "M/m" → ['M', 'm']
     */
    private function splitGenotype(string $genotype): array
    {
        $parts = explode('/', $genotype);

        if (count($parts) === 1) {
            // pl. "CEA" típusú kódok – kezeljük duplán
            return [$parts[0], $parts[0]];
        }

        return $parts;
    }

    /**
     * Allélpár normalizálása: domináns előre
     *
     * pl. ('m', 'M') → "M/m"
     */
    private function normalizeGenotype(string $a1, string $a2): string
    {
        // egyszerű rendezés: nagybetű előre
        $alleles = [$a1, $a2];

        usort($alleles, function ($x, $y) {
            // nagybetű (domináns) előre
            $isUpperX = ctype_upper($x[0]);
            $isUpperY = ctype_upper($y[0]);

            if ($isUpperX === $isUpperY) {
                return strcmp($x, $y);
            }

            return $isUpperX ? -1 : 1;
        });

        return $alleles[0] . '/' . $alleles[1];
    }
}