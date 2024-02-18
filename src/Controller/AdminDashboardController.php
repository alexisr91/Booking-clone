<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statistics;


// Tableau de bord de l'administrateur avec stats et résultats, indexisation 


class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */

    public function index(Statistics $statsService)
    {

        $stats = $statsService->getStatistics();
        $bestAds= $statsService->getAdsStats('DESC');
        $worstAds= $statsService->getAdsStats('ASC');

            return $this->render('admin/dashboard/index.html.twig', [
            'stats'=>$stats,
            'bestAds'=>$bestAds,
            'worstAds'=>$worstAds,
            ]);
    }
}
