<?php

namespace Aroha\GpsCacheBundle\Command;

use Aroha\GpsCacheBundle\Doctrine\Point;
use Aroha\GpsCacheBundle\Entity\GpsCache;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GpscacheFindByGpsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'gpscache:find-by-gps';

    protected function configure()
    {
        $this
            ->setDescription('Find adress from latitude and longitude')
            ->addArgument('latitude', InputArgument::REQUIRED)
            ->addArgument('longitude', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $latitude = $input->getArgument('latitude');
        $longitude = $input->getArgument('longitude');
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw new \Exception("latitude or longitude is not numbers");
        }
        $gps = new Point($longitude, $latitude);
        $gpsCacheRepository = $doctrine = $this->getContainer()->get('doctrine')->getRepository(GpsCache::class);
        $address = $gpsCacheRepository->findByGps($gps);
        dump($address);
    }
}
