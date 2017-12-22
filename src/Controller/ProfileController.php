<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/edit", name="profile_edit")
     */
    public function edit()
    {
        return $this->render('profile/edit.html.twig', []);
    }
}
