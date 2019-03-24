<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 24/03/2019
 * Time: 10:04
 */

namespace App\Utils;


use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{

    public static function validate(ValidatorInterface $validator, $entity)
    {
        $violations = $validator->validate($entity);
        $messages = [];
        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }
            return [
                'status' => false,
                'messages' => $messages
            ];
        } else {
            return [
                'status' => true,
                'messages' => $messages
            ];
        }
    }

}