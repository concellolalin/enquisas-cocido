<?php

namespace EnquisaBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;

class Scanner {

    const RATIO = 2.0;

    private $rexions = NULL;

    private $filename = NULL;

    private $resultados = NULL;

    private $calibrate = NULL;

    private $procesada = NULL;

    /**
     * @var string|null
     */
    private $basedir = NULL;

    /**
     * @var EntityManager|null
     */
    private $em = NULL;

    /**
     * @var Logger|null
     */
    private $logger = NULL;

    private $i = 0;


    public function __construct(EntityManager $em, $basedir, Logger $logger) 
    {
        $this->basedir   = $basedir;
        $this->em        = $em;
        $this->logger    = $logger;
        $this->calibrate = false;
    }


    public function run(\EnquisaBundle\Entity\Restaurante $restaurante, $filename = NULL) 
    {
        // Establecer filename se proporciona o parámetro filename
        if ($filename !== NULL) {
            $this->setFilename($filename);
        }

        $fs = new Filesystem();
        if(!$fs->exists($this->getFilename())) {
            throw new Exception('A ruta ao ficheiro non é válida');
        }

        $this->logger->info('Procesando ficheiro: ' . $this->getFilename());

        // Cargar rexións
        $this->loadRexions();

        // Comprobar si se pode executar o proceso, en caso contrario lanza Exception
        $this->testRun();

        // Extraer as páxinas do PDF, gardar os paths de cada páxina/imaxe
        $pages = $this->extractPages();
        foreach ($pages as $page) {
            $this->logger->info('Procesando páxina: ' . $page);
            $this->processPage($page);
        }

        //dump($this->resultados);

        // Gardar en base de datos
        $opcionRepo = $this->em->getRepository('EnquisaBundle:Opcion');

        foreach($this->resultados as $ficheiro => $resultado) {
            $enquisa = new \EnquisaBundle\Entity\Enquisa();
            $enquisa->setNome($ficheiro);
            $enquisa->setFicheiro($ficheiro);
            $enquisa->setProcesada(true);
            $enquisa->setRestaurante($restaurante);

            $this->em->persist($enquisa);
            $this->em->flush();

            foreach($resultado as $quest => $option) {
                $opcion = $opcionRepo->find($option);

                if($opcion !== NULL) {
                    $resposta = new \EnquisaBundle\Entity\Resposta();

                    $resposta->setEnquisa($enquisa);
                    $resposta->setOpcion($opcion);

                    $this->em->persist($resposta);
                    $this->em->flush();
                } else {
                    $enquisa->setProcesada(false);
                    $this->em->persist($enquisa);
                    $this->em->flush();
                }
            }
        }
    }


    /**
     * Método para calibrar
     *
     * @param $filename
     * @throws \Exception
     */
    public function calibrate($filename)
    {
        $this->calibrate = true;

        $this->setFilename($filename);

        $fs = new Filesystem();
        if(!$fs->exists($this->getFilename())) {
            throw new Exception('A ruta ao ficheiro non é válida');
        }

        $this->logger->info('Procesando ficheiro: ' . $this->getFilename());

        // Cargar rexións
        $this->loadRexions();

        // Comprobar si se pode executar o proceso, en caso contrario lanza Exception
        $this->testRun();

        // Extraer as páxinas do PDF, gardar os paths de cada páxina/imaxe
        $pages = $this->extractPages();
        foreach($pages as $page) {
            $this->logger->info('Procesando páxina: ' . $page);
            $this->processPage($page);
        }

        /*foreach ($pages as $page) {
            $this->logger->info('Procesando páxina: ' . $page);
            $this->processPage($page);
        }*/


    }

    /**
     * @param $page string
     */
    private function processPage($page) 
    {
        $enquisa = new \Imagick(realpath($page));

        $filename = pathinfo($page)['filename'];

        // Marcar como que o procesamento vai ben
        $this->procesada = TRUE;

        // Recorrer as preguntas por cada páxina
        foreach ($this->rexions as $i => $question) {
            $this->resultados[$filename]['Q'.$question['id']] =
                $this->processQuestion($enquisa, $question);
        }
    }

    private function processQuestion($enquisa, $question) 
    {

        $weights = [-1];

        for ($i = 0; $i < count($question['opcions']); $i++) {

            $width = $question['opcions'][$i]['width'];
            $height = $question['opcions'][$i]['height'];
            $x = $question['opcions'][$i]['x'];
            $y = $question['opcions'][$i]['y'];

            $box = clone $enquisa;

            $box->cropImage($width, $height, $x, $y);

            if($this->calibrate) {
                $box->writeImage($basename = $this->basedir . '/' . ($this->i++) . '.png');
            }


            $weights[] = array(
                'weight' => $this->weight($box),
                'opcionId' => $question['opcions'][$i]['id']
            );

            $box->destroy();
        }
        
        $result = $this->selected($weights);
        if($result == -1) {
            $this->logger->error('Marca non detectada');
            $this->procesada = FALSE;
        }

        return $result;
    }

    private function weight($imageRegion) 
    {

        // Outros efectos para mellorar
        //$imageRegion->negateImage(true);
        //$imageRegion->fxImage('intensity');
        //$imageRegion->contrastImage(1);
        $imageRegion->whiteThresholdImage('#EEE');
        $imageRegion->blackThresholdImage('#EEE');

        /*$basename = $this->basedir . '/%02d.png';
        $imageRegion->writeImage( sprintf($basename, $this->i++) );*/

        // Contar pixels
        $it = new \ImagickPixelIterator($imageRegion);

        //$whitePixel = new \ImagickPixel('#000');
        $whitePixel = new \ImagickPixel('#FFF');

        $totalPixels = 0;
        $dirtyPixels = 0;

        foreach ($it as $pixels) {
            foreach ($pixels as $column => $pixel) {
                if (!$pixel->isSimilar($whitePixel, 0.1)) {
                    $dirtyPixels++;
                }

                $totalPixels++;
            }
            $it->syncIterator();
        }

        $percent = ($dirtyPixels*100/$totalPixels);
        //dump($percent);
        
        $this->logger->warning('%: ' . $percent);

        return $percent;
    }

    /**
     * Comproba que ten un 5% máis de puntos de diferencia,
     * noutrocaso é que deixou en branco
     *
     * @param $weights
     * @return int
     */
    private function selected($weights) 
    {
        list($opcionId, $weight) = $this->maximo($weights);

        foreach($weights as $value) {                        
            if(($opcionId != $value['opcionId']) && ($value['weight']+Scanner::RATIO >= $weight)) {
                return -1;
            }
        }

        return $opcionId;
    }

    private function maximo($array)
    {
        $opcionId = -1;
        $max = -1;

        foreach($array as $item) {
            if($item['weight'] > $max) {
                $max      = $item['weight'];
                $opcionId = $item['opcionId'];
            }
        }

        return [$opcionId, $max];
    }

    private function loadRexions() 
    {        
        $this->rexions = $this->em->getRepository('EnquisaBundle:Pregunta')->getOpciones();
        $this->logger->info('Rexións a procesar cargadas');
    }

    private function extractPages() {
        // Nome base para os ficheiros extraídos
        $basename = $this->basedir . '/' . pathinfo($this->filename)['filename'] . '-PDF-%03d.png';
        
        $this->logger->info('Basename: ' . $basename);

        $im = new \Imagick();
        //$im->setResolution(300, 300);
        $im->setResolution(150, 150);
        //$im->setResolution(200, 200);
        $im->readImageBlob(file_get_contents($this->filename));
        $this->logger->info('Cargando: ' . $this->filename);
                
        $num_pages = $im->getNumberImages();
        
        $this->logger->info('Total enquisas a procesar: ' . $num_pages);

        $pages = array();
        for ($i = 0; $i < $num_pages; $i++) {
            $im->setIteratorIndex($i);
            $im->setImageFormat('png');

            $filename = sprintf($basename, $i);
            $this->logger->info('Gardando...: ' . $filename);

            $im->writeImage($filename);
            $this->logger->info('Imaxe da enquisa ' . $i . '/' . $num_pages . ' gardada en ' . $filename);
            
            $pages[] = $filename;
        }
        $im->destroy();

        return $pages;
    }

    private function testRun() 
    {
        if ($this->filename === NULL) {
            throw new \Exception('Non se estableceu un nome de ficheiro PDF para procesar');
        }

        if (empty($this->rexions)) {
            throw new \Exception('Non se estableceron rexións para procesar');
        }

        if (!extension_loaded('imagick')) {
            throw new \Exception('Extensión imagick non instalada');
        }
        $this->logger->info('Módulo Imagick cargado');
    }


    /**
     * @return null
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @param null $filename
     */
    public function setFilename($filename) {
        $this->filename = $filename;
    }


    /**
     * @return null
     */
    public function getRexions() {
        return $this->rexions;
    }

    /**
     * @param null $rexions
     */
    public function setRexions($rexions) {
        $this->rexions = $rexions;
    }

}
