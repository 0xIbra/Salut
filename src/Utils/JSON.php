<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 22/03/2019
 * Time: 18:33
 */

namespace App\Utils;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class JSON
{
    public static function JSONResponse($data, $status, SerializerInterface $serializer)
    {
        $data = $serializer->serialize([
            'code' => $status,
            'data' => $data
        ], 'json');
        $response = new Response($data, $status);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public static function JSONResponseWithGroups($data, $status, SerializerInterface $serializer, $groups)
    {
        $data = $serializer->serialize([
            'code' => $status,
            'data' => $data
        ], 'json', SerializationContext::create()->setGroups($groups));
        $response = new Response($data, $status);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}