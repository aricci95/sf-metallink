<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\PictureType;
use App\Entity\Picture;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class PictureController extends AbstractController
{
    /**
     * @Route("/picture/edit", name="picture_edit")
     */
    public function edit(Request $request)
    {
        $picture = new Picture();

        $form = $this->createForm(PictureType::class, $picture, [
            'user' => $this->getUser(),
        ]);

        /*
        if ($picture->getId()) {
            $picture->setUrl(
                new File($picture->getUrl())
            );
        }*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()->getPictures()->count() == 5) {
                $this->get('session')->getFlashBag()->add('error', 'Vous ne pouvez télécharger que 5 photos maximum.');

                return $this->render('picture/edit.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $file = $picture->getName();
            
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                'photos/' . $this->getUser()->getId(),
                $fileName
            );

            $picture->setName($fileName);

            if (!$this->getUser()->getDefaultPicture()) {
                $picture->setIsDefault(true);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            $this->getUser()->addPicture($picture);

            return $this->redirect($this->generateUrl('picture_edit'));
        }

        return $this->render('picture/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/picture/collection/{id}", name="picture_collection", defaults={"id"=null})
     */
    public function collection(Picture $defaultPicture = null)
    {
        if ($defaultPicture) {
            foreach ($this->getUser()->getPictures() as $currentPicture) {
                $currentPicture->setIsDefault($currentPicture == $defaultPicture);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
        }

        return $this->render('picture/collection.html.twig', [
            'pictures' => $this->getUser()->getPictures(),
        ]);
    }

    /**
     * @Route("/picture/remove/{id}", name="picture_remove")
     */
    public function remove(Picture $picture, CacheManager $cache)
    {
        $cache->remove($picture->getPath());

        $em = $this->getDoctrine()->getManager();
        $em->remove($picture);
        $em->flush();

        return $this->render('picture/collection.html.twig', [
            'pictures' => $this->getUser()->getPictures(),
        ]);
    }
}
