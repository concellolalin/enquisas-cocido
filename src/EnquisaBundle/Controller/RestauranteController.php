<?php

namespace EnquisaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EnquisaBundle\Entity\Restaurante;
use EnquisaBundle\Form\RestauranteType;

/**
 * Restaurante controller.
 *
 * @Route("/admin/restaurante")
 */
class RestauranteController extends Controller
{
    /**
     * Lists all Restaurante entities.
     *
     * @Route("/", name="restaurante_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $restaurantes = $em->getRepository('EnquisaBundle:Restaurante')->findAll();

        return $this->render('restaurante/index.html.twig', array(
            'restaurantes' => $restaurantes,
        ));
    }

    /**
     * Creates a new Restaurante entity.
     *
     * @Route("/new", name="restaurante_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $restaurante = new Restaurante();
        $form = $this->createForm('EnquisaBundle\Form\RestauranteType', $restaurante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurante);
            $em->flush();

            return $this->redirectToRoute('restaurante_show', array('id' => $restaurante->getId()));
        }

        return $this->render('restaurante/new.html.twig', array(
            'restaurante' => $restaurante,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Restaurante entity.
     *
     * @Route("/{id}", name="restaurante_show")
     * @Method("GET")
     */
    public function showAction(Restaurante $restaurante)
    {
        $deleteForm = $this->createDeleteForm($restaurante);

        return $this->render('restaurante/show.html.twig', array(
            'restaurante' => $restaurante,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Restaurante entity.
     *
     * @Route("/{id}/edit", name="restaurante_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Restaurante $restaurante)
    {
        $deleteForm = $this->createDeleteForm($restaurante);
        $editForm = $this->createForm('EnquisaBundle\Form\RestauranteType', $restaurante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurante);
            $em->flush();

            return $this->redirectToRoute('restaurante_edit', array('id' => $restaurante->getId()));
        }

        return $this->render('restaurante/edit.html.twig', array(
            'restaurante' => $restaurante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Restaurante entity.
     *
     * @Route("/{id}", name="restaurante_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Restaurante $restaurante)
    {
        $form = $this->createDeleteForm($restaurante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($restaurante);
            $em->flush();
        }

        return $this->redirectToRoute('restaurante_index');
    }

    /**
     * Creates a form to delete a Restaurante entity.
     *
     * @param Restaurante $restaurante The Restaurante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Restaurante $restaurante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('restaurante_delete', array('id' => $restaurante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
