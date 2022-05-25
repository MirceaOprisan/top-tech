<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ListController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     * @Method({"GET"})
     */
    public function index()
    {
        $companies = $this->getDoctrine()->getRepository(Company::class)->findAll();

        return $this->render('list/index.html.twig', [
            'companies' => $companies
        ]);
    }


    /**
     * @Route("/list/new", name="new_company")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $company = new Company();

        $form = $this->createFormBuilder($company)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('market_value', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, array('label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-4')))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         $company = $form->getData();

         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($company);
         $entityManager->flush();

         return $this->redirectToRoute('list');
        }

        return $this->render('/list/new.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/list/edit/{id}", name="edit_company")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $company = new Company();
        $company = $this->getDoctrine()->getRepository(Company::class)->find($id);

        $form = $this->createFormBuilder($company)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('market_value', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, array('label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-4')))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('/list/edit.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/list/{id}", name="company_show")
     */
    public function show(int $id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $company = $this->getDoctrine()->getRepository(Company::class)->find($id);
        $categoryName = $company->getCategories($id);
        return $this->render('list/show.html.twig', array('company' => $company, 'category' => $categoryName

        ));
    }


    /**
     * @Route("/list/delete/{id}")
     * @Method({"DELETE"})
     */
    public function delete(Company $id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $company = $this->getDoctrine()->getRepository(Company::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($company);
        $entityManager->flush();

        return $this->redirectToRoute('list');
    }}


