<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppBirthdayType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'years' => $this->generateYears(),
            'label' => 'Date de naissance',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BirthdayType::class;
    }

    /**
     * @return array
     */
    private function generateYears()
    {
        $endYear = date('Y') - 18;
        $range = range($endYear, $endYear - 110);

        return array_combine($range, $range);
    }
}
