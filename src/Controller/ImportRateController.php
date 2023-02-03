<?php

namespace App\Controller;

use App\Entity\PriceRate;
use App\Repository\PriceRateRepository;
use App\Service\PriceRateImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: "/import", name: "admin_import_")]
#[IsGranted('ROLE_ADMIN')]
class ImportRateController extends AbstractController
{
    #[Route(path: '/', name: 'rate')]
    public function importRate(Request $request, PriceRateRepository $rateRepository, PriceRateImporter $priceRateImporter)
    {
        $templateFile = "/import/template/modele_taux.csv";
        $currentRates = $rateRepository->findBy([],['months'=>'asc']);

        $currentRates = $this->formatToArray($currentRates);

        $formBuilder = $this->createFormBuilder([],['csrf_protection' => false]);
        $formBuilder->add('file', FileType::class, [
            'label' => 'Nouveau fichier des taux : '
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $priceRateImporter->process($form->get('file')->getData());
            $this->addFlash('success', 'Nouveaux taux importés');
        }

        return $this->render('admin/import_rate.html.twig', [
            'templateFile' => $templateFile,
            'form' => $form,
            'rates' => $currentRates,
        ]);
    }

    /**
     * @param PriceRate[] $currentRates
     * @return void
     */
    private function formatToArray(array $currentRates)
    {
        $groupedData = [];

        foreach ($currentRates as $currentRate) {
            if (!isset($groupedData[$currentRate->getMonths()])) {
                $groupedData[$currentRate->getMonths()] = [];
            }
            $groupedData[$currentRate->getMonths()][] = $currentRate;
        }

        return $groupedData;
    }
}