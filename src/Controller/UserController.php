<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Search\UserSearchType;
use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_list")
     */
    public function index()
    {
        return $this->render('user/list.html.twig', [
            'form' => $this->createForm(UserSearchType::class, new User())->createView()
        ]);
    }
}
