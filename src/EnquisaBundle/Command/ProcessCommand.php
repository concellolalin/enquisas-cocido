<?php
namespace EnquisaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('enquisa:procesar')
            ->setDescription('Procesar un lote de enquisas')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('ficheiro', 'i', InputOption::VALUE_REQUIRED),
                ))
            )
            ->addArgument(
                'restaurante',
                InputArgument::OPTIONAL,
                'De que restaurante son as enquisas?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var  $logger */
        $logger = $container->get('logger');

        $restaurante = $input->getArgument('restaurante');
        $ficheiro    = $input->getOption('ficheiro');

        $em = $container->get('doctrine')->getManager();

        if(empty($restaurante)) {
            $restaurantes = $em->getRepository('EnquisaBundle:Restaurante')->findAll();

            $helper = $this->getHelper('question');
            $question = new Question('Nome do restaurante: ');
            $question->setAutocompleterValues($this->restaurantesToArray($restaurantes));

            $restaurante = $helper->ask($input, $output, $question);
        }

        $logger->info('Restaurante: ' . $restaurante);

        $resultado = $em->getRepository('EnquisaBundle:Restaurante')->findByNome($restaurante);
        if(count($resultado) !== 1) {
            throw new Exception('Restaurante non recoñecido');
        }

        $scanner = $container->get('scanner');

        // Pasar o path completo ao ficheiro
        $scanner->run($resultado[0], $ficheiro);
    }

    private function restaurantesToArray($restaurantes)
    {
        $resultado = array();

        foreach($restaurantes as $restaurante) {
            //$resultado[$restaurante->getId()] = $restaurante->getNome();
            $resultado[] = $restaurante->getNome();
        }

        return $resultado;
    }


}