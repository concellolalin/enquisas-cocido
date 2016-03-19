<?php

namespace EnquisaBundle\Controller;

use EnquisaBundle\EnquisaBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EnquisaBundle\Entity\Enquisa;
use EnquisaBundle\Entity\Resposta;
use EnquisaBundle\Form\EnquisaType;

use JpGraph\JpGraph;
use GuzzleHttp\Client;
use HTML2PDF;

/**
 * Enquisa controller.
 *
 * @Route("/admin/enquisa")
 */
class EnquisaController extends Controller
{
    /**
     * Lists all Enquisa entities.
     *
     * @Route("/index", name="enquisa_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $enquisas = $em->getRepository('EnquisaBundle:Enquisa')->findBy(
            array(), array('id' => 'DESC')
        );

        return $this->render('enquisa/index.html.twig', array(
            'enquisas' => $enquisas,
        ));
    }

    /**
     * Dashboard.
     *
     * @Route("/dashboard", name="enquisa_dashboard")
     * @Method("GET")
     */
    public function dashboardAction()
    {                
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('EnquisaBundle:Enquisa')->getTotal();
        
        $restaurantes = $em->getRepository('EnquisaBundle:Restaurante');
        //dump($restaurantes->findAll());
        
        
        $totalRestaurantes = $restaurantes->getTotalRestaurantes();
        //dump($totalRestaurantes);
        
        $preguntas = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntas();
        //dump($preguntas);
                        
        /*$preguntasStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntasStats();
        //dump($preguntasStats);*/

        return $this->render('enquisa/dashboard.html.twig', array(
            'total' => $total,
            'total_restaurantes' => $totalRestaurantes,
            'preguntas' => $preguntas,
            'restaurantes' => $restaurantes->findAll(),
            //'preguntasStats' => $preguntasStats,
        ));
    }
    
    /**
     * Rotar imaxe.
     *
     * @Route("/rotate/{filename}", name="enquisa_rotate")
     * @Method("GET")
     */
    public function rotateAction(Request $request, $filename)
    {                
        $dir = $this->container->getParameter('kernel.root_dir') .
            '/../web/uploads/enquisas';
        
        $file = realpath($dir . '/' . $filename . '.png');
        
        if(!file_exists($file)) {
            throw new \Exception('Ficheiro da enquisa non existe: ' . $file);
        }
        
        $image = new \Imagick($file);
        // Por si fixese falta rotar a imaxe
        //$image->rotateImage(new \ImagickPixel('#00000000'), 90);
            
        
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type','image/png');
        /* $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.png";');
        $response->headers->set('Content-length', $image->getImageLength()); */
        
        $response->setContent($image->getImageBlob());

        return $response;
    }
    
    /**
     *
     * @Route("/panel/{qid}", name="enquisa_panel", options={"expose"=true})
     * @Method("GET")
     */
    public function questionAction(Request $request, $qid)
    {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('EnquisaBundle:Enquisa')->getTotal();
        
        //$qid = $request->query->get('qid');
        
        $preguntaStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntaStats($qid);
        
        /*$preguntasStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntasStats();
        dump($preguntasStats);*/
        
        return new JsonResponse([
            'stats' => $preguntaStats,
        ]);
    }

    /**
     *
     * @Route("/panel/{qid}/{rid}", name="enquisa_panel_filtered", options={"expose"=true})
     * @Method("GET")
     */
    public function questionFilteredAction(Request $request, $qid, $rid)
    {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('EnquisaBundle:Enquisa')->getTotal();
        
        //$qid = $request->query->get('qid');
        
        if ($rid == 0) {
            $preguntaStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntaStats($qid);
        } else {
            $preguntaStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntaStatsByRestaurant($qid, $rid);
        }
        
        
        /*$preguntasStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntasStats();
        dump($preguntasStats);*/
        
        return new JsonResponse([
            'stats' => $preguntaStats,
        ]);
    }

    /**
     *
     * @Route("/piechart/{qid}/{rid}/{style}", name="enquisa_piechart")
     * @Method("GET")
     */
    public function piechartAction(Request $request, $qid, $rid, $style)
    {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        //$total = $em->getRepository('EnquisaBundle:Enquisa')->getTotal();
        //$qid = $request->query->get('qid');

        if ($rid == 0) {
            $preguntaStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntaStats($qid);
        } else {
            $preguntaStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntaStatsByRestaurant($qid, $rid);
        }

        if(count($preguntaStats) > 0) {
            $data = [];
            $label = [];
            $title = '';
            foreach ($preguntaStats as $pregunta) {
                $title = $pregunta['texto'];
                $data[] = $pregunta['value'];
                $label[] = $pregunta['label'] . ' %.1f%%';
            }

            JpGraph::load();
            JpGraph::module('pie');
            JpGraph::module('pie3d');

            $graph = new \PieGraph(350, 280);

            if($style == 'VividTheme') {
                $theme_class = new \VividTheme;
            } else {
                $theme_class = new \UniversalTheme; //\VividTheme;
            }
            $graph->SetTheme($theme_class);
            $graph->legend->SetPos(0.5, 0.97, 'center', 'bottom');
            $graph->legend->SetColumns(3);

            $p1 = new \PiePlot3D($data);
            $graph->Add($p1);

            $p1->SetLegends($label);
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetLabelType(PIE_VALUE_PER);
            $p1->SetSize(0.4);
            $p1->SetCenter(0.5, 0.47);
            $p1->value->Show();
            $p1->value->SetFont(FF_ARIAL, FS_NORMAL, 12);

            ob_start();
            $graph->Stroke();
            $imgStream = ob_get_contents();
            ob_end_clean();
        } else {
            //gif transparent 1x1
            $imgStream = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        }

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type','image/png');
        $response->setContent($imgStream);
        return $response;
    }

    /**
     * Report.
     *
     * @Route("/report/{rid}", name="enquisa_report")
     * @Method("GET")
     */
    public function reportAction(Request $request, $rid) {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('EnquisaBundle:Enquisa')->getTotal();

        $restaurantes = $em->getRepository('EnquisaBundle:Restaurante');
        $enquisas = $em->getRepository('EnquisaBundle:Enquisa');
        //dump($restaurantes->findAll());


        $totalRestaurantes = $restaurantes->getTotalRestaurantes();
        //dump($totalRestaurantes);

        $preguntas = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntas();
        //dump($preguntas);

        /*$preguntasStats = $em->getRepository('EnquisaBundle:Enquisa')->getPreguntasStats();
        //dump($preguntasStats);*/

        return $this->render('enquisa/report.html.twig', array(
            'total' => $total,
            'total_restaurantes' => $totalRestaurantes,
            'total_enquisas' => count($enquisas->findBy(['restaurante' => $rid])),
            'preguntas' => $preguntas,
            //'restaurantes' => $restaurantes->findAll(),
            'restaurante' => $restaurantes->findOneBy(['id' => $rid]),
            //'preguntasStats' => $preguntasStats,
        ));
    }

    /**
     * Informe PDF.
     *
     * @Route("/pdf/{rid}", name="enquisa_pdf")
     * @Method("GET")
     */
    public function pdfAction($rid)
    {
        $url = $this->generateUrl('enquisa_report', ['rid' => $rid], TRUE);
        $client = new Client();
        $http = $client->request('GET', $url);
        $html = $http->getBody();

        $html2pdf = new HTML2PDF('L','A4','es');
        $html2pdf->WriteHTML($html);
        $html2pdf->Output('exemple.pdf');

    }

    /**
     * Lists reports
     *
     * @Route("/reports", name="enquisa_reports")
     * @Method("GET")
     */
    public function reportsAction()
    {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $restaurantes = $em->getRepository('EnquisaBundle:Restaurante');

        return $this->render('enquisa/reports.html.twig', array(
          'restaurantes' => $restaurantes->findAll(),
        ));
    }
    

    /**
     * Creates a new Enquisa entity.
     *
     * @Route("/new", name="enquisa_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $enquisa = new Enquisa();
        $form = $this->createForm('EnquisaBundle\Form\EnquisaType', $enquisa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($enquisa);
            $em->flush();

            return $this->redirectToRoute('enquisa_show', array('id' => $enquisa->getId()));
        }

        return $this->render('enquisa/new.html.twig', array(
            'enquisa' => $enquisa,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Enquisa entity.
     *
     * @Route("/{id}", name="enquisa_show", requirements = { "id" = "\d+" })
     * @Method("GET")
     */
    public function showAction(Enquisa $enquisa)
    {
        $deleteForm = $this->createDeleteForm($enquisa);

        return $this->render('enquisa/show.html.twig', array(
            'enquisa' => $enquisa,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Enquisa entity.
     *
     * @Route("/{id}/edit", name="enquisa_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Enquisa $enquisa)
    {
        /** @var $em Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($enquisa);
        $editForm = $this->createForm('EnquisaBundle\Form\EnquisaType', $enquisa);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($enquisa);
            $em->flush();

            // Eliminar respostas anteriores
            $query = $em->createQuery('DELETE FROM EnquisaBundle\Entity\Resposta r WHERE r.enquisa=:enquisa');
            $query->setParameters(array(
                'enquisa' => $enquisa//->getId()
            ));
            $query->execute();

            $opciones = $request->get('opcion');
            foreach($opciones as $opcion) {
                /** @var $resposta EnquisaBundle\Entity\Resposta */
                $resposta = new Resposta();

                $resposta->setEnquisa($enquisa);
                $resposta->setOpcion(
                    $em->getRepository('EnquisaBundle:Opcion')->findOneById($opcion)
                );

                $em->persist($resposta);
                $em->flush();
            }

            return $this->redirectToRoute('enquisa_edit', array('id' => $enquisa->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $preguntas = $em->getRepository('EnquisaBundle:Pregunta')->getOpciones();

        return $this->render('enquisa/edit.html.twig', array(
            'enquisa' => $enquisa,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'preguntas' => $preguntas,
        ));
    }

    /**
     * Displays a form to upload an group of Enquisa entities.
     *
     * @Route("/upload", name="enquisa_upload")
     * @Method({"GET", "POST"})
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createForm('EnquisaBundle\Form\UploadType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ignore_user_abort(true);
            set_time_limit(0);

            //dump($scanner);
            $dir = $this->container->getParameter('kernel.root_dir') .
                '/../web/uploads/enquisas';

            $ficheiro = $form->getData()['ficheiro'];
            $filename = $dir . '/' . $ficheiro->getClientOriginalName();
            $ficheiro->move($dir, $ficheiro->getClientOriginalName());

            $restaurante = $form->getData()['restaurante'];

            /*$em = $this->getDoctrine()->getManager();
            $restaurante = $em->getRepository('EnquisaBundle:Restaurante')->find($restauranteId);*/

            /** @var $scanner \EnquisaBundle\Service\Scanner */
            $scanner = $this->container->get('scanner');
            $scanner->run($restaurante, $filename);

            return $this->redirectToRoute('enquisa_index');
        }

        return $this->render('enquisa/upload.html.twig', array(
            'upload_form' => $form->createView(),
        ));
    }

    /**
     * Calibrar
     *
     * @Route("/calibrate/{filename}", name="enquisa_calibrate")
     * @Method({"GET"})
     */
    public function calibrateAction(Request $request, $filename)
    {
        //dump($scanner);
        $dir = $this->container->getParameter('kernel.root_dir') .
            '/../web/uploads/enquisas';

        $filename = $dir . '/' . $filename;

        $filename = '/home/vifito/public_html/compromiso-calidade/enquisa/web/uploads/enquisas/Vento.pdf';

        /** @var $scanner \EnquisaBundle\Service\Scanner */
        $scanner = $this->container->get('scanner');
        $scanner->calibrate($filename);

        return new Response('<h1>OK</h1>');
    }

    /**
     * Deletes a Enquisa entity.
     *
     * @Route("/{id}", name="enquisa_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Enquisa $enquisa)
    {
        $form = $this->createDeleteForm($enquisa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Eliminar respostas anteriores
            $query = $em->createQuery('DELETE FROM EnquisaBundle\Entity\Resposta r WHERE r.enquisa=:enquisa');
            $query->setParameters(array(
                'enquisa' => $enquisa//->getId()
            ));
            $query->execute();

            $em->remove($enquisa);
            $em->flush();
        }

        return $this->redirectToRoute('enquisa_index');
    }

    /**
     * Creates a form to delete a Enquisa entity.
     *
     * @param Enquisa $enquisa The Enquisa entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Enquisa $enquisa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enquisa_delete', array('id' => $enquisa->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
