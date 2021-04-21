<?php

namespace Aroha\GpsCacheBundle\Repository;

use Aroha\GpsCacheBundle\Doctrine\Point;
use Aroha\GpsCacheBundle\Entity\GpsCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GpsCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method GpsCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method GpsCache[]    findAll()
 * @method GpsCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GpsCacheRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GpsCache::class);
    }

    public function updateAddressIfNeeded(GpsCache $gpsCache) {
        if ($gpsCache->isValid() && $gpsCache->getUpdateOn()) {
            $nextUpdate = new \DateTime();
            $nextUpdate->setTimestamp(strtotime(GpsCache::$refreshSetting['valid'], $gpsCache->getUpdateOn()->getTimestamp()));
            if(new \DateTime() < $nextUpdate) {
                return;
            }
        } elseif (!$gpsCache->isValid() && $gpsCache->getUpdateOn()) {
            $nextUpdate = new \DateTime();
            $nextUpdate->setTimestamp(strtotime(GpsCache::$refreshSetting['invalid'], $gpsCache->getUpdateOn()->getTimestamp()));
            if(new \DateTime() < $nextUpdate) {
                return;
            }
        }
        $this->updateAddress($gpsCache);
    }

    private function loadAndParseGoogleUrl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $geoloc = json_decode($result, true);
        $address_components = [];
        if (isset($geoloc['results'][0]['address_components'])) {
            foreach($geoloc['results'][0]['address_components'] as $address_component) {
                foreach($address_component['types'] as $type) {
                    if (isset($address_components[$type])) {
                        continue;
                    }
                    $address_components[$type] = $address_component;
                }
            }
        }

        $valid = isset($geoloc['status']) && $geoloc['status'] == 'OK';
        $address = [
            'latitude' => '',
            'longitude' => '',
            'street' => '',
            'number' => '',
            'city' => '',
            'address' => '',
            'valid' => $valid,
        ];

        if (!$valid) {
            return $address;
        }
        if (!isset($geoloc['results'][0]['geometry']['location']['lat'])) {
            return $address;
        }
        if (!isset($geoloc['results'][0]['geometry']['location']['lng'])) {
            return $address;
        }
        if (!isset($geoloc['results'][0]['formatted_address'])) {
            return $address;
        }

        $address['latitude'] = $geoloc['results'][0]['geometry']['location']['lat'];
        $address['longitude'] = $geoloc['results'][0]['geometry']['location']['lng'];
        $address['street'] = isset($address_components['route']['long_name']) ? $address_components['route']['long_name'] : '';
        $address['number'] = isset($address_components['premise']['long_name']) ? [$address_components['premise']['long_name']] : [];
        if (isset($address_components['street_number']['long_name'])) {
            $address['number'][] = isset($address_components['street_number']['long_name']);
        }
        $address['number'] = implode('/', $address['number']);
        $address['city'] = isset($address_components['sublocality']['long_name']) ? $address_components['sublocality']['long_name'] : '';
        $address['address'] = $geoloc['results'][0]['formatted_address'];

        return $address;
    }

    private function loadAddressFromGps(Point $gps) {
        $params = [
            'latlng' => $gps->getLatitude().','.$gps->getLongitude(),
            'key' => getenv('gpscache_googleApiKey'),
        ];
        $address = $this->loadAndParseGoogleUrl('https://maps.googleapis.com/maps/api/geocode/json?'.http_build_query($params));
        return $address;
    }

    public function updateAddress(GpsCache $gpsCache) {
        $address = $this->loadAddressFromGps($gpsCache->getGps());
        $gpsCache->setStreet($address['street']);
        $gpsCache->setNumber($address['number']);
        $gpsCache->setCity($address['city']);
        $gpsCache->setAddress($address['address']);
        $gpsCache->setValid($address['valid']);
        $gpsCache->setUpdateOn(new \DateTime());
        $this->getEntityManager()->persist($gpsCache);
        $this->getEntityManager()->flush();
    }

    public function findByGps(Point $gps) {
        $gps = $gps->round();
        $address = $this->findOneBy(['gps' => $gps]);
        if (!$address) {
            $address = new GpsCache();
            $address->setGps($gps);
            $address->setCreateOn(new \DateTime());
        }
        $this->updateAddressIfNeeded($address);
        return $address;
    }

}
