<?php

namespace App\Service;

use App\Exception\PriceRateNotFoundException;
use App\Model\Box;
use App\Model\FinancingMode;
use App\Repository\PriceRateRepository;
use App\Repository\SettingsRepository;

class PriceCalculator
{
    private PriceRateRepository $priceRateRepository;
    private SettingsRepository $settingsRepository;

    public function __construct(PriceRateRepository $priceRateRepository, SettingsRepository $settingsRepository)
    {
        $this->priceRateRepository = $priceRateRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function calculate(Box $box, $nbMois, $typeFinancement)
    {
        $settings = $this->settingsRepository->findOneBy([]);
        $financingMode = FinancingMode::from($typeFinancement);
        $priceRate = $this->priceRateRepository->findOneBy([
            'months' => $nbMois,
            'financingMode' =>$financingMode,
        ]);

        if (!$priceRate) {
            throw new PriceRateNotFoundException();
        }

        $total = $box->getProductTotal();
        $total += $box->getMaintenanceCost($settings->getMaintenanceCost());


        if ($box->hasOptionDAC()) {
            $total += $box->getOptionDacPrice($settings->getDacOptionPrice());
        }


        $firstPayment = $financingMode->firstPaymentRate() * $total;
        $financingRate = (float)$priceRate->getRate();

        $mensualite = $total * $financingRate;
        

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