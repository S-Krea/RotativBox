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

    public function process($fileName)
    {
        $fp = fopen($fileName, 'r');
        //Headers - osef
        fgetcsv($fp);

        while ($line = fgetcsv($fp)) {
            $nbMois = (int)$line[0];
            foreach ($line as $colNumber => $data) {
                if ($colNumber === 0) {
                    continue;
                }
                $data = (float)$data;
                $rate = new PriceRate();
                $rate->setMonths($nbMois);
                $rate->setRate((string) $data);
                switch ($colNumber) {
                    case 1:
                        $rate->setFinancingMode(FinancingMode::from('linear'));
                        break;
                    case 2:
                        $rate->setFinancingMode(FinancingMode::from('f1'));
                        break;
                    case 3:
                        $rate->setFinancingMode(FinancingMode::from('f2'));
                        break;
                    case 4:
                        $rate->setFinancingMode(FinancingMode::from('f3'));
                        break;
                }
                $this->entityManager->persist($rate);
            }
            $this->entityManager->flush();
        }

        fclose($fp);
    }
}