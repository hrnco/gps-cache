<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 25.1.2019
 * Time: 21:34
 */

namespace Aroha\GpsCacheBundle\Doctrine\DBAL\Types;

use Aroha\GpsCacheBundle\Doctrine\Point;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Mapping type for spatial POINT objects
 */
class PointType extends \CrEOF\Spatial\DBAL\Types\Geography\PointType
{

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /**
         * @var \CrEOF\Spatial\PHP\Types\Geography\Point $data
         */
        $data = parent::convertToPHPValue($value, $platform);
        if (!$data) {
            return;
        }
        return new Point($data->getLongitude(), $data->getLatitude());
    }

}