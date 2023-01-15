<?php

namespace App\Controller;

use App\Service\SendRequests;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DataValidation;

class StackoverflowapiController extends AbstractController
{
    #[Route('/stackoverflowapi', name: 'app_stackoverflowapi')]
    public function index(Request $request): JsonResponse
    {
        $datavalidation = new DataValidation();
        $sendRequest = new SendRequests();

        /** get parameters from the config file */
        $apidata = $this->getParameter('stackoverflow');
        $url = $apidata['responseapi']['url'];
        $parameters = $apidata['responseapi']['parameters'];
        $parameters_to_send = [];
        $returnData = [
            'ErrorMessages' => [],
            'Data' => []
        ];

        /** validate the parameters and add them to the parameters to make the request  */
        foreach($parameters as $p){
            $parameter_url_data = $request->get($p);
            $mandatory = false;
            if(in_array($p,$apidata['responseapi']['mandatory'])) {
                $mandatory = true;
            }
            switch($p) {
                case 'tagged':
                    if(!$datavalidation->tagValidation($parameter_url_data,$mandatory,$apidata['responseapi']['parametervalidation'][$p]['tagsseparation'],$apidata['responseapi']['parametervalidation'][$p]['maxtags'])) {
                        $returnData['ErrorMessages'][] = $apidata['responseapi']['errormessages'][$p];
                    } else {
                        $parameters_to_send[$p] = $parameter_url_data;
                    }
                    break;
                case 'fromdate':
                case 'todate':
                    if(!$datavalidation->dateValidation($parameter_url_data,$mandatory,$apidata['responseapi']['parametervalidation'][$p]['regex'])) {
                        $returnData['ErrorMessages'][] = $apidata['responseapi']['errormessages'][$p];
                    } else {
                        if($parameter_url_data!='' and $parameter_url_data != NULL)
                        $parameters_to_send[$p] = strtotime($parameter_url_data);
                    }
                    break;
            }
        }

        /** If there aren't errors, make the request */
        if(count($returnData['ErrorMessages']) == 0) {
            $data_string = gzinflate(substr($sendRequest->curl($url,'GET',$parameters_to_send),10));
            $data_array = json_decode($data_string,true);
            $returnData['Data'] = $data_array;
        }
        return $this->json($returnData);
    }
}
