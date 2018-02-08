<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BandController extends AbstractController
{
     /**
     * @Route("/band/save", name="band_save")
     */
    public function saveBandsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        foreach ($request->request->get('bands', []) as $bandName) {
            $band = $em->getRepository(Band::class)->findOneByName($bandName);
            
            if (!$band) {
                $band = new Band();
                $band->setName($bandName);
            }

            $user->addBand($band);
            $band->addUser($user);

            $em->persist($user);
            $em->persist($band);
        }

        $em->flush();

        return new JsonResponse(200);
    }
}
