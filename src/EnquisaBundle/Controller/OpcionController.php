<?php

namespace EnquisaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EnquisaBundle\Entity\Opcion;
use EnquisaBundle\Form\OpcionType;

/**
 * Opcion controller.
 *
 * @Route("/admin/opcion")
 */
class OpcionController extends Controller
{
    /**
     * Lists all Opcion entities.
     *
     * @Route("/", name="opcion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $opcions = $em->getRepository('EnquisaBundle:Opcion')->findAll();

        return $this->render('opcion/index.html.twig', array(
            'opcions' => $opcions,
        ));
    }

    /**
     * Creates a new Opcion entity.
     *
     * @Route("/new", name="opcion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $opcion = new Opcion();
        $form = $this->createForm('EnquisaBundle\Form\OpcionType', $opcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($opcion);
            $em->flush();

            return $this->redirectToRoute('opcion_show', array('id' => $opcion->getId()));
        }

        return $this->render('opcion/new.html.twig', array(
            'opcion' => $opcion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Opcion entity.
     *
     * @Route("/{id}", name="opcion_show")
     * @Method("GET")
     */
    public function showAction(Opcion $opcion)
    {
        $deleteForm = $this->createDeleteForm($opcion);

        return $this->render('opcion/show.html.twig', array(
            'opcion' => $opcion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Opcion entity.
     *
     * @Route("/{id}/edit", name="opcion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Opcion $opcion)
    {
        $deleteForm = $this->createDeleteForm($opcion);
        $editForm = $this->createForm('EnquisaBundle\Form\OpcionType', $opcion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($opcion);
            $em->flush();

            return $this->redirectToRoute('opcion_edit', array('id' => $opcion->getId()));
        }

        return $this->render('opcion/edit.html.twig', array(
            'opcion' => $opcion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Opcion entity.
     *
     * @Route("/{id}", name="opcion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Opcion $opcion)
    {
        $form = $this->createDeleteForm($opcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($opcion);
            $em->flush();
        }

        return $this->redirectToRoute('opcion_index');
    }

    /**
     * Creates a form to delete a Opcion entity.
     *
     * @param Opcion $opcion The Opcion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Opcion $opcion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('opcion_delete', array('id' => $opcion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
