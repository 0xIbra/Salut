<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 24/03/2019
 * Time: 10:59
 */

namespace App\Serializer;


use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class ObjectConstructor implements ObjectConstructorInterface
{

    /**
     * Constructs a new object.
     *
     * Implementations could for example create a new object calling "new", use
     * "unserialize" techniques, reflection, or other means.
     *
     * @param mixed $data
     * @param array $type ["name" => string, "params" => array]
     */
    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        $class = $metadata->name;
        return new $class();
    }

}