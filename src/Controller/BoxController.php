<?php

namespace App\Controller;

use App\Model\Box;
use App\Service\ProductFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class BoxController extends AbstractController
{
    #[Route(path: '/box/{type}', name: "box_chose", requirements: [
        'type' => '\d+',
    ])]
    public function selectBox(Request $request, $type,?Box $box = null)
    {
        $intType = intval($type);

        if (!$box || ($box->getMaxItems() !== $intType)) {
            $box = new Box($type);
            $request->getSession()->set(Box::BOX_SESSION_KEY, $box);
        }

        return $this->redirectToRoute('app_products_list');
    }
}