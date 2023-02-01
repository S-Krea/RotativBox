<?php

namespace App\Controller;

use App\Exception\BoxFullException;
use App\Exception\ItemAlreadyInBoxException;
use App\Model\Box;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route(path: '/box/add/{productId}', name: 'cart_add')]
    public function addProduct(Request $request, int $productId, ?Box $box = null)
    {
        if (!$box) {
            $this->addFlash('danger', 'Veuillez sélectionner une box pour commencer.');

            return $this->redirectToRoute('app_home');
        }

        try {
            $box->addItem($productId);
            $this->addFlash('success', 'Produit ajouté à la box');
            $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
        } catch (BoxFullException $boxFullException) {
            $this->addFlash('warning', $boxFullException->getMessage());
        } catch (ItemAlreadyInBoxException $alreadyInBoxException) {
            $this->addFlash('warning', $alreadyInBoxException->getMessage());
        }

        return $this->redirectToRoute('app_box_chose', ['type' => $box->getMaxItems()]);
    }


    #[Route(path: '/box/remove/{productId}', name: 'cart_remove')]
    public function removeProduct(Request $request, $productId, ?Box $box = null)
    {
        $box->removeItem((int)$productId);
        $request->getSession()->set(Box::BOX_SESSION_KEY, $box);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route(path: '/cart', name: 'cart_show')]
    public function showCart(Request $request, ?Box $box = null)
    {
        return $this->render('front/cart/show.html.twig', ['box' => $box]);
    }
}