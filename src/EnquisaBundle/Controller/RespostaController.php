<?php

namespace EnquisaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EnquisaBundle\Entity\Resposta;
use EnquisaBundle\Form\RespostaType;

/**
 * Resposta controller.
 *
 * @Route("/admin/resposta")
 */
class RespostaController extends Controller
{
    /**
     * Lists all Resposta entities.
     *
     * @Route("/", name="resposta_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $respostas = $em->getRepository('EnquisaBundle:Resposta')->findAll();

        return $this->render('resposta/index.html.twig', array(
            'respostas' => $respostas,
        ));
    }

    /**
     * Creates a new Resposta entity.
     *
     * @Route("/new", name="resposta_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $respostum = new Resposta();
        $form = $this->createForm('EnquisaBundle\Form\RespostaType', $respostum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($respostum);
            $em->flush();

            return $this->redirectToRoute('resposta_show', array('id' => $respostum->getId()));
        }

        return $this->render('resposta/new.html.twig', array(
            'respostum' => $respostum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Resposta entity.
     *
     * @Route("/{id}", name="resposta_show")
     * @Method("GET")
     */
    public function showAction(Resposta $respostum)
    {
        $deleteForm = $this->createDeleteForm($respostum);

        return $this->render('resposta/show.html.twig', array(
            'respostum' => $respostum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Resposta entity.
     *
     * @Route("/{id}/edit", name="resposta_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Resposta $respostum)
    {
        $deleteForm = $this->createDeleteForm($respostum);
        $editForm = $this->createForm('EnquisaBundle\Form\RespostaType', $respostum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($respostum);
            $em->flush();

            return $this->redirectToRoute('resposta_edit', array('id' => $respostum->getId()));
        }

        return $this->render('resposta/edit.html.twig', array(
            'respostum' => $respostum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Resposta entity.
     *
     * @Route("/{id}", name="resposta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Resposta $respostum)
    {
        $form = $this->createDeleteForm($respostum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($respostum);
            $em->flush();
        }

        return $this->redirectToRoute('resposta_index');
    }

    /**
     * Creates a form to delete a Resposta entity.
     *
     * @param Resposta $respostum The Resposta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Resposta $respostum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('resposta_delete', array('id' => $respostum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
