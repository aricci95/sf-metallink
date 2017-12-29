<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Search\UserSearchType;
use App\Entity\User;
use App\Repository\UserRepository;

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

    /**
     * @Route("/user/show/{id}", name="user_show")
     */
    public function show(User $user)
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/search", name="user_search")
     */
    public function search(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy($request->query->all());

        return $this->render('user/search.html.twig', [
            'users' => $users,
        ]);
    }
}
