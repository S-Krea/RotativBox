<?php

namespace App\Controller;

use App\Entity\PriceRate;
use App\Entity\Settings;
use App\Form\Admin\SettingsFormType;
use App\Repository\PriceRateRepository;
use App\Service\PriceRateImporter;
use App\Service\ProductSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
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
    public function importRate(Request $request, EntityManagerInterface $entityManager, PriceRateImporter $priceRateImporter)
    {
        $rateRepository = $entityManager->getRepository(PriceRate::class);
        $settingsRepository = $entityManager->getRepository(Settings::class);

        $settings = $settingsRepository->findOneBy([]);
        if (!$settings) {
            $settings = new Settings();
            $settings->setMaintenanceCost('350');
            $settings->setDacOptionPrice('7200');
        }
        $templateFile = "/import/template/modele_taux.csv";
        $currentRates = $rateRepository->findBy([],['months'=>'asc', 'financingMode' => 'asc']);

        $currentRates = $this->formatToArray($currentRates);

        $formBuilder = $this->createFormBuilder([]);
        $formBuilder->add('file', FileType::class, [
            'label' => 'Nouveau fichier des taux : '
        ]);
        $form = $formBuilder->getForm();
        $formSettings = $this->createForm(SettingsFormType::class, $settings);


        $formSettings->handleRequest($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $priceRateImporter->process($form->get('file')->getData());
            $this->addFlash('success', 'Nouveaux taux importés');
        }

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $settingsRepository->save($settings, true);
            $this->addFlash('success', 'Configuration sauvegardée.');
        }

        return $this->render('admin/import_rate.html.twig', [
            'templateFile' => $templateFile,
            'form' => $form,
            'formSettings' => $formSettings,
            'rates' => $currentRates,
        ]);
    }

    #[Route(path: '/sync', name: 'sync_product')]
    public function importProducts(Request $request, ProductSynchronizer $productSynchronizer)
    {
        $productSynchronizer->synchronizeAll();
        $this->addFlash('success', 'Synchronisation produit effectuée');

        return $this->redirectToRoute('app_admin_import_rate');
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