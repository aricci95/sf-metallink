<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\IndicatorService;

class IndicatorController extends AbstractController
{
    private $indicatorService;

    public function __construct(IndicatorService $indicatorService)
    {
        $this->indicatorService = $indicatorService;
    }

    /**
     * @Route("/indicator", name="indicator_list")
     */
    public function getIndicators()
    {
        return $this->render('indicator.html.twig', [
            'indicators' => $this->indicatorService->getAll(),
        ]);
    }
}
