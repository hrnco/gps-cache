<?php

namespace Aroha\GpsCacheBundle\Doctrine;


/**
 * Point object for spatial mapping
 */
class Point extends \CrEOF\Spatial\PHP\Types\Geography\Point {

    public static $roundMeters = 20;

    private function roundValue($value, $meters) {
        $coef = 10000;
        $coefMeters = $coef * ($meters/11);
        $value = $coefMeters * $value;
        $value = round($value);
        $value = $value / $coefMeters;
        return $value;
    }

    public function getLatitude()
    {
        return parent::getLatitude();
    }

    public function getLongitude()
    {
        return parent::getLongitude();
    }

    /**
     * tato metoda "zaokrukli" gps priblizne na self::$roundMeters metrov
     */
    public function round() {
        $meters = self::$roundMeters;
        $newLatitude = $this->roundValue($this->getLatitude(), $meters);
        $newLongitude = $this->roundValue($this->getLongitude(), $meters);
        return new Point($newLongitude, $newLatitude);
    }
}