<?php

namespace App\Service;

use App\Entity\PriceRate;
use App\Model\FinancingMode;
use Doctrine\ORM\EntityManagerInterface;

class PriceRateImporter
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFinancingModeForCol($colNumber)
    {
        switch ($colNumber) {
            case 1:
                return FinancingMode::from('linear');
                break;
            case 2:
                return FinancingMode::from('f1');
                break;
            case 3:
                return FinancingMode::from('f2');
                break;
            case 4:
                return FinancingMode::from('f3');
                break;
        }
    }

    public function process($fileName)
    {
        $fp = fopen($fileName, 'r');
        //Headers - osef
        fgetcsv($fp);

        while ($line = fgetcsv($fp)) {
            $nbMois = (int)$line[0];
            foreach ($line as $colNumber => $data) {
                if ($colNumber === 0 || $nbMois === 0) {
                    continue;
                }
                $financingMode = $this->getFinancingModeForCol($colNumber);

                $rate = $this->findExistingRate($nbMois, $financingMode);

                $data = (float)$data;
                if (!$rate) {
                    $rate = new PriceRate();
                }
                $rate->setMonths($nbMois);
                $rate->setRate((string) $data);
                $rate->setFinancingMode($financingMode);
                $this->entityManager->persist($rate);
            }
            $this->entityManager->flush();
        }

        fclose($fp);
    }

    private function findExistingRate(int $nbMois, FinancingMode $financingMode)
    {
        $repo = $this->entityManager->getRepository(PriceRate::class);
        return $repo->findOneBy(['months' => $nbMois, 'financingMode' => $financingMode]);
    }
}