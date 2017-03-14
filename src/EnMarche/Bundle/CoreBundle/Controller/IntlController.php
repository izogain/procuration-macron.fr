<?php

namespace EnMarche\Bundle\CoreBundle\Controller;

use EnMarche\Bundle\CoreBundle\Intl\FranceCitiesBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IntlController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchInseeCodeAction(Request $request): JsonResponse
    {
        return new JsonResponse(FranceCitiesBundle::getPostalCodeCities($request->get('term')));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchCityAction(Request $request): JsonResponse
    {
        return new JsonResponse(FranceCitiesBundle::getCity($request->get('postalCode'), $request->get('city')));
    }
}
