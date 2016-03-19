<?php

namespace EnquisaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EnquisaBundle\Entity\Pregunta;
use EnquisaBundle\Form\PreguntaType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pregunta controller.
 *
 * @Route("/admin/pregunta")
 */
class PreguntaController extends Controller
{
    /**
     * Lists all Pregunta entities.
     *
     * @Route("/", name="pregunta_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$preguntas = $em->getRepository('EnquisaBundle:Pregunta')->findAll();
        $preguntas = $em->getRepository('EnquisaBundle:Pregunta')->getOpciones();
        //dump($preguntas);

        return $this->render('pregunta/index.html.twig', array(
            'preguntas' => $preguntas,
        ));
    }

    /**
     * Creates a new Pregunta entity.
     *
     * @Route("/new", name="pregunta_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pregunta = new Pregunta();
        $form = $this->createForm('EnquisaBundle\Form\PreguntaType', $pregunta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pregunta);
            $em->flush();

            return $this->redirectToRoute('pregunta_show', array('id' => $pregunta->getId()));
        }

        return $this->render('pregunta/new.html.twig', array(
            'preguntum' => $pregunta,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Pregunta entity.
     *
     * @Route("/{id}", name="pregunta_show")
     * @Method("GET")
     */
    public function showAction(Pregunta $preguntum)
    {
        $deleteForm = $this->createDeleteForm($preguntum);

        return $this->render('pregunta/show.html.twig', array(
            'preguntum' => $preguntum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Pregunta entity.
     *
     * @Route("/{id}/edit", name="pregunta_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Pregunta $preguntum)
    {
        $deleteForm = $this->createDeleteForm($preguntum);
        $editForm = $this->createForm('EnquisaBundle\Form\PreguntaType', $preguntum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($preguntum);
            $em->flush();

            return $this->redirectToRoute('pregunta_edit', array('id' => $preguntum->getId()));
        }

        return $this->render('pregunta/edit.html.twig', array(
            'preguntum' => $preguntum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Pregunta entity.
     *
     * @Route("/{id}", name="pregunta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Pregunta $preguntum)
    {
        $form = $this->createDeleteForm($preguntum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($preguntum);
            $em->flush();
        }

        return $this->redirectToRoute('pregunta_index');
    }

    /**
     * Creates a form to delete a Pregunta entity.
     *
     * @param Pregunta $preguntum The Pregunta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pregunta $preguntum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pregunta_delete', array('id' => $preguntum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
