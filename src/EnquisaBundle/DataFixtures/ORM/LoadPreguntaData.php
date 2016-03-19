<?php
namespace EnquisaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EnquisaBundle\Entity\Pregunta;
use EnquisaBundle\Entity\Opcion;

class LoadPreguntaData implements FixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $data = [
            [
                'texto' => 'A atención recibida por parte do persoal do restaurante considera que foi...',
                'opcions' => [
                    [
                        'valor'  => 'Moi boa',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 468,
                        'y'      => 1431,
                    ],
                    [
                        'valor'  => 'Boa',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 468,
                        'y'      => 1243,
                    ],
                    [
                        'valor'  => 'Mellorable',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 468,
                        'y'      => 972,
                    ],
                    [
                        'valor'  => 'Mala',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 468,
                        'y'      => 795,
                    ],
                ]
            ],
            [
                'texto' => 'A cantidade de Cocido que acaba de degustar é',
                'opcions' => [
                    [
                        'valor'  => 'Moi boa',
                        'width'  => 78,
                        'height' => 57,
                        'x'      => 720,
                        'y'      => 1436,
                    ],
                    [
                        'valor'  => 'Boa',
                        'width'  => 78,
                        'height' => 57,
                        'x'      => 720,
                        'y'      => 1243,
                    ],
                    [
                        'valor'  => 'Mellorable',
                        'width'  => 78,
                        'height' => 57,
                        'x'      => 720,
                        'y'      => 978,
                    ],
                    [
                        'valor'  => 'Mala',
                        'width'  => 78,
                        'height' => 57,
                        'x'      => 720,
                        'y'      => 800,
                    ],
                ]
            ],
            [
                'texto' => 'Considera que o prezo é axeitado',
                'opcions' => [
                    [
                        'valor'  => 'Si',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 965,
                        'y'      => 1519,
                    ],
                    [
                        'valor'  => 'Non',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 965,
                        'y'      => 1366,
                    ],
                ]
            ],
            [
                'texto' => 'O tempo de espera foi o apropiado',
                'opcions' => [
                    [
                        'valor'  => 'Si',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1221,
                        'y'      => 1517,
                    ],
                    [
                        'valor'  => 'Non',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1223,
                        'y'      => 1366,
                    ],
                ]
            ],
            [
                'texto' => 'Recomendaría a experiencia de vir a lalín a degustar o Cocido',
                'opcions' => [
                    [
                        'valor'  => 'Si',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1470,
                        'y'      => 1517,
                    ],
                    [
                        'valor'  => 'Non',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1469,
                        'y'      => 1368,
                    ],
                ]
            ],
            [
                'texto' => 'Recomendaría este establecemento a outra persoa',
                'opcions' => [
                    [
                        'valor'  => 'Si',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1720,
                        'y'      => 1519,
                    ],
                    [
                        'valor'  => 'Non',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1719,
                        'y'      => 1369,
                    ],
                ]
            ],
            [
                'texto' => 'En xeral, a súa estancia en Lalín foi',
                'opcions' => [
                    [
                        'valor'  => 'Moi boa',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1972,
                        'y'      => 1436,
                    ],
                    [
                        'valor'  => 'Boa',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1972,
                        'y'      => 1246,
                    ],
                    [
                        'valor'  => 'Regular',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1972,
                        'y'      => 995,
                    ],
                    [
                        'valor'  => 'Mala',
                        'width'  => 72,
                        'height' => 65,
                        'x'      => 1972,
                        'y'      => 800,
                    ],
                ]
            ],            
        ];
        
        // Cargar as preguntas
        foreach($data as $orde => $preg) {
            $pregunta = new Pregunta();
            $pregunta->setTexto($preg['texto']);
            $pregunta->setOrde($orde);
            
            $manager->persist($pregunta);
            
            // Opcións da pregunta
            foreach($preg['opcions'] as $opc) {
                $opcion = new Opcion();
                $opcion->setValor($opc['valor']);
                $opcion->setWidth($opc['width']);
                $opcion->setHeight($opc['height']);
                $opcion->setX($opc['x']);
                $opcion->setY($opc['y']);
                
                $opcion->setPregunta($pregunta);
                $manager->persist($opcion);
            }            
                                    
            $manager->flush();
        }
       
    }
    
}