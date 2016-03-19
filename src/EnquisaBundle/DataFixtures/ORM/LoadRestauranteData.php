<?php
namespace EnquisaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EnquisaBundle\Entity\Restaurante;

class LoadRestauranteData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $restaurantes = [
            'A Taberna de Vento',            
            'A Taberna do Cazador',
            'Bar Manolo',
            'Bar O Polo',
            'Bar Restaurante Suso',
            'Casa Currás',
            'Hostal As Vilas',
            'Hostal Camiño de Santiago',
            'Hotel Parrillada Villanueva',
            'Hotel Restaurante Pontiñas',
            'Hotel Spa Norat Torre do Deza',
            'Mesón O Cruce',
            'Mesón Os Arcos',
            'Parrillada O Mordisco',
            'Parrillada Taboada',
            'Pazo de Bendoiro',
            'Pensión As Palmeras',
            'Pulpería Roque',
            'Restaurante Agarimo',
            'Restaurante Asador O Toxo',
            'Restaurante Cabanas',
            'Restaurante Casa do Patrón',
            'Restaurante Casa Pablo',
            'Restaurante Catro Camiños',
            'Restaurante La Diligencia',
            'Restaurante La Estación',
            'Restaurante La Molinera',
            'Restaurante Onde Antonio',
            'Restaurante Parrillada La Robleda',
            'Restaurante San Martín',
            'Parrillada San Martín II',
        ];
        
        foreach($restaurantes as $nome) {
            $restaurante = new Restaurante();
            $restaurante->setNome($nome);
    
            $manager->persist($restaurante);
            $manager->flush();    
        }
        
    }
}