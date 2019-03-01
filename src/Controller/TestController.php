<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;
use App\Entity\Evaluation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TestController extends AbstractController
{


    /**
     * function pour tester les évaluations/notes
     * @Route("/test", name="test")
     */
    public function test()
    {
        $ms = $this->getDoctrine()->getRepository(Movie::class)->findAll();
        //fonction qui calcule la note moyenne d'un film (ne marche pas, à vérifier).
        for ($i=0; $i < count($ms) ; $i) {
          $notes = $ms[$i]->getEvaluations()->getGrade();
        }
        return $this->render('test/index.html.twig', [
          "ms" => $ms
        ]);
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {
        $ms = $this->getDoctrine()->getRepository(Movie::class)->findAll();
        return $this->render('test/index.html.twig', [
          "ms" => $ms
        ]);
    }

    /**
     * @Route("/single/{id}", name="app_show")
     */
    public function show(Movie $a)
    {
        return $this->render('test/single.html.twig', [
          "a" => $a
        ]);
    }

    /**
     * @Route("/evaluation/{id}", name="app_rate")
     *
     * @Isgranted("ROLE_USER")
     */
    public function rate(Movie $b, Request $c)
    {
        $d = new Evaluation();
        $u = new User();

        $form = $this->createFormBuilder($d)
            ->add('comment')
            ->add('grade')
            ->add('soumettre', SubmitType::class)
            ->getForm();

        $form->handleRequest($c);

        if ($form->isSubmitted() && $form->isValid()) {
          $d->setMovie($b);
          $d->setUser($u);
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($b, $u);
          $entityManager->flush();
        }

        return $this->render('test/evaluation.html.twig', [
          "b" => $b,
          "form" => $form->createView()
        ]);
    }
}
