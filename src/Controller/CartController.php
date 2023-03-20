<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\BoxFullException;
use App\Exception\ItemAlreadyInBoxException;
use App\Exception\PriceRateNotFoundException;
use App\Form\DevisForm;
use App\Model\Box;
use App\Repository\ProductRepository;
use App\Service\Mailer;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class CartController extends AbstractController
{
    #[Route(path: '/bo_x/add/{id}', name: 'old_cart_add')]
    public function oldAddProduct(Request $request, Product $product, ?Box $box = null)
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

    #[Route(path: '/box/add/{id}', name: 'cart_add')]
    public function addProduct(Request $request, Product $product, ?Box $box = null)
    {
        if (!$box) {
            return new JsonResponse(['status' => 'danger', 'message' => 'Veuillez sélectionner une box pour commencer.'], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $box->addItem($product);
            $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
            $data = [
                'status' => 'success',
                'nbItems' => $box->getItems()->count(),
                'remainings' => $box->getMaxItems() - $box->getItems()->count(),
                'message' => 'Produit ajouté à la box',
            ];
            $returnStatus = 201;
        } catch (BoxFullException $boxFullException) {
            $data = [
                'status' => 'warning',
                'nbItems' => $box->getItems()->count(),
                'remainings' => $box->getMaxItems() - $box->getItems()->count(),
                'message' => $boxFullException->getMessage(),
            ];
            $returnStatus = 400;
        } catch (ItemAlreadyInBoxException $alreadyInBoxException) {
            $data = [
                'status' => 'warning',
                'nbItems' => $box->getItems()->count(),
                'remainings' => $box->getMaxItems() - $box->getItems()->count(),
                'message' => $alreadyInBoxException->getMessage(),
            ];
            $returnStatus = 400;
        }

        return new JsonResponse($data, $returnStatus);
    }


    #[Route(path: '/box/remove/{id}', name: 'cart_remove')]
    public function removeProduct(Request $request, Product $product, ?Box $box = null)
    {
        $box->removeItem($product);
        $request->getSession()->set(Box::BOX_SESSION_KEY, $box);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route(path: '/cart', name: 'cart_show')]
    public function showCart(Request $request, ProductRepository $productRepository, ?Box $box = null)
    {

        //Récup des box depuis la queryString, si présent on écrase la box en cours.
        $box = $this->getPacksParams($request, $productRepository, $box);

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

    private function getPacksParams(Request $request, ProductRepository $productRepository, ?Box $box): ?Box
    {
        $prods = [];
        for ($i=1;$i<=9;$i++) {
            $prods[] = $request->query->get('p'.$i, null);
        }

        $prods = array_filter($prods);
        $nbProds = count($prods);

        if ($nbProds === 0) {
            return $box;
        }

        if (!in_array($nbProds, [3,6,9])) {
            return $box;
        }

        $box = new Box($nbProds);
        foreach ($prods as $prod) {
            $product = $productRepository->findOneBy(['woocommerceId' => $prod]);
            if ($product) {
                $box->addItem($product);
            }
        }

        $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
        return $box;
    }
}