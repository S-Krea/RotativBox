<?php

namespace App\Service;

use App\Exception\PriceRateNotFoundException;
use App\Model\Box;
use App\Model\FinancingMode;
use App\Repository\PriceRateRepository;

class PriceCalculator
{
    private PriceRateRepository $priceRateRepository;

    public function __construct(PriceRateRepository $priceRateRepository)
    {
        $this->priceRateRepository = $priceRateRepository;
    }

    public function calculate(Box $box, $nbMois, $typeFinancement)
    {
        $financingMode = FinancingMode::from($typeFinancement);
        $priceRate = $this->priceRateRepository->findOneBy([
            'months' => $nbMois,
            'financingMode' =>$financingMode,
        ]);

        if (!$priceRate) {
            throw new PriceRateNotFoundException();
        }

        $total = $box->getProductTotal();
        $total += $box->getMaintenanceCost();
        $firstPayment = $financingMode->firstPaymentRate() * $total;
        $financingRate = (float)$priceRate->getRate();

        $mensualite = $total * $financingRate;

        /*
         * TODO: Gérer l'option ici
        if ($box->hasOptionDAC()) {
            $mensualite += $box->getOptionDacMonthlyPrice();
        }
        */

        return [
            'nbMois' => $nbMois,
            'type' => $financingMode,
            'total' => $total,
            'rate' => $financingRate,
            'firstPayment' => $firstPayment,
            'monthlyPayment' => $mensualite,
        ];
    }
}