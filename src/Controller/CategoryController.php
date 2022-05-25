<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Company;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CategoryController extends AbstractController
{



    /**
     * @Route("/list/newcategory", name="new_category")
     * Method({"GET", "POST"})
     */


    public function new(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-4')))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        return $this->render('/list/category.html.twig', array(
            'form' => $form->createView()
        ));
}

/**
 * @Route("/categories", name="categories")
 * @Method({"GET"})
 */
public function index()
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

    return $this->render('category/index.html.twig', [
        'categories' => $categories,
    ]);
}

    /**
     * @Route("/categories/edit/{id}", name="edit_category")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $category = new Company();
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $form = $this->createFormBuilder($category)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-4')))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        return $this->render('/category/editcategory.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/categories/delete/{id}")
     * @Method({"DELETE"})
     */
    public function delete(Category $id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('categories');
    }}