<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/change", name="profile_change")
     */
    public function edit()
    {
        return $this->render('profile/edit.html.twig', []);
    }
}
