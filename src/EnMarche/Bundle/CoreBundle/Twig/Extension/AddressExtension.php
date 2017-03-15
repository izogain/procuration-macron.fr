<?php

namespace EnMarche\Bundle\CoreBundle\Twig\Extension;

use AppBundle\Entity\Address;
use AppBundle\Mediator\AddressMediator;
use Symfony\Component\Intl\Intl;

class AddressExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('format_address', [$this, 'formatAddress'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param Address $address
     *
     * @return string
     */
    public function formatAddress(Address $address): string
    {
        $output = [];

        if ($streetName = $this->filterUcWording($address->getStreetName())) {
            $streetLine = [];

            if ($streetNumber = $address->getStreetNumber()) {
                $streetLine[] = $streetNumber;
            }

            if (null !== $streetRepeater = $address->getStreetRepeater()) {
                $streetLine[] = AddressMediator::getStreetRepeaterLabel($streetRepeater);
            }

            if (null !== $streetType = $address->getStreetType()) {
                $streetLine[] = AddressMediator::getStreetTypeLabel($streetType);
            }

            $streetLine[] = $streetName;
            $output[] = implode(' ', $streetLine);
        }

        $output[] = $address->getPostalCode().' '.$this->filterUcWording($address->getCityName());
        $output[] = mb_strtoupper(Intl::getRegionBundle()->getCountryName($address->getCountryCode()));

        return implode('<br>', $output);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function filterUcWording($string)
    {
        return ucwords(mb_strtolower($string));
    }
}
