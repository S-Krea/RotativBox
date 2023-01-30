<?php

namespace App\Controller;

use App\Model\Box;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BoxController extends AbstractController
{
    #[Route(path: '/box/{type}', name: "box_chose", requirements: [
        'type' => '\d+',
    ])]
    public function pickItems(Request $request, $type)
    {
        $savedBox = $request->getSession()->has(Box::BOX_SESSION_KEY, null);
        $box = new Box($type);

        return $this->render('front/box/pick_items.html.twig', ['box' => $box, 'savedBox' => $savedBox]);
    }
}