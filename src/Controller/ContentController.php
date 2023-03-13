<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    #[Route(path: '/concept', name: 'concept')]
    public function conceptPage(Request $request)
    {
        return $this->render('front/page/concept.html.twig');
    }

    #[Route(path: '/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(Request $request)
    {
        return $this->render('front/page/mentions_legales.html.twig');
    }

    #[Route(path: '/confidentialite', name: 'confidentialite')]
    public function politiqueConfidentialite(Request $request)
    {
        return $this->render('front/page/confidentialite.html.twig');
    }
}