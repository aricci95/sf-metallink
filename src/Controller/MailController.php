<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Mail;
use App\Entity\User;
use App\Repository\MailRepository;
use App\Form\MailType;

class MailController extends SearchController
{
    /**
     * @Route("/mail/list", name="mail_list")
     */
    public function list()
    {
        return $this->render('mail/list.html.twig');
    }

    /**
     * @Route("/mail/show/{id}", name="mail_show")
     */
    public function show(Request $request, User $target, MailRepository $mailRepository)
    {
        $mails = $this->getDoctrine()->getRepository(Mail::class)->getUsersMails($target, $this->getUser());

        $form = $this->createForm(MailType::class, new Mail(), [
            'user'   => $this->getUser(),
            'target' => $target,
        ]);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $form->getData();

            $em->persist($mail);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Mail envoyé.');

            return $this->redirectToRoute('mail_show', ['id' => $target->getId()]);
        }

        $render = $this->render('mail/show.html.twig', [
            'mails'  => $mails,
            'target' => $target,
            'form'   => $form->createView(),
        ]);

        foreach ($mails as $mail) {
            $mail->setStatus(Mail::STATUS_READ);

            $em->persist($mail);
        }

        $em->flush();

        return $render;
    }

    /**
     * @Route("/mail/search/{page}", name="mail_search", defaults={"page"=1})
     */
    public function search(Request $request, $page = 1)
    {
        $params = $request->query->all();

        return parent::doSearch(Mail::class, $params, $page);
    }
}
