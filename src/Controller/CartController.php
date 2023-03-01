<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\BoxFullException;
use App\Exception\ItemAlreadyInBoxException;
use App\Exception\PriceRateNotFoundException;
use App\Form\DevisForm;
use App\Model\Box;
use App\Service\Mailer;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class CartController extends AbstractController
{
    #[Route(path: '/box/add/{id}', name: 'cart_add')]
    public function addProduct(Request $request, Product $product, ?Box $box = null)
    {
        if (!$box) {
            $this->addFlash('danger', 'Veuillez sélectionner une box pour commencer.');

            return $this->redirectToRoute('app_home');
        }

        try {
            $box->addItem($product);
            $this->addFlash('success', 'Produit ajouté à la box');
            $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
        } catch (BoxFullException $boxFullException) {
            $this->addFlash('warning', $boxFullException->getMessage());
        } catch (ItemAlreadyInBoxException $alreadyInBoxException) {
            $this->addFlash('warning', $alreadyInBoxException->getMessage());
        }

        return $this->redirectToRoute('app_box_chose', ['type' => $box->getMaxItems()]);
    }


    #[Route(path: '/box/remove/{id}', name: 'cart_remove')]
    public function removeProduct(Request $request, Product $product, ?Box $box = null)
    {
        $box->removeItem($product);
        $request->getSession()->set(Box::BOX_SESSION_KEY, $box);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route(path: '/cart', name: 'cart_show')]
    public function showCart(Request $request, ?Box $box = null)
    {
        if (!$box) {
            $this->addFlash('warning', 'Vous n\'avez pas encore sélectionné de box.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('front/cart/show.html.twig', ['box' => $box]);
    }

    #[Route(path:'/_calculate', name: 'cart_calculate', methods: ['POST'])]
    public function calculate(Request $request, ?Box $box, PriceCalculator $calculator)
    {
        $params = json_decode($request->getContent());

        $nbMois = $params->nbMois ?? 36;
        $typeFinance = $params->financement ?? 'linear';
        $optionDac = ($params->optionDAC === 'on');

        try {
            $box->setOptionDAC($optionDac);
            $result = $calculator->calculate($box, $nbMois, $typeFinance);
            $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
        } catch (PriceRateNotFoundException $e) {
            return new Json([
                'html' => 'Une erreur est intervenue : '.$e->getMessage(),
            ]);
        }
        $form = $this->createForm(DevisForm::class,['nbMois' => $nbMois, 'financement' => $typeFinance]);

        $html = $this->renderView('front/cart/_financement.html.twig', ['result' => $result, 'form' => $form]);

        return new JsonResponse([
            'html' => $html
        ]);
    }

    #[Route(path: '/cart/validate', name: 'cart_validate')]
    public function validate(Request $request, Mailer $mailer, PriceCalculator $calculator, ?Box $box = null)
    {
        $form = $this->createForm(DevisForm::class);
        $form->handleRequest($request);

        $dataDevis = $form->getData();
        try {
            $results = $calculator->calculate($box, $dataDevis['nbMois'], $dataDevis['financement']);
            $mailer->sendBox($box, $dataDevis, $results);
            $request->getSession()->remove(Box::BOX_SESSION_KEY);
            $this->addFlash('success', 'Votre devis a été envoyé à notre service commercial. Vous serez recontacté sous peu.');
        } catch (PriceRateNotFoundException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_home');
    }
}