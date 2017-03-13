<?php

namespace App;

use Spot\EntityInterface;
use Spot\MapperInterface;
use Spot\EventEmitter;
use Psr\Log\LogLevel;
use Tuupola\Base62;

class Example extends \Spot\Entity
{
    use \Psr\Log\LoggerAwareTrait;

    protected static $table = "examples";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "uid" => ["type" => "string",  "length" => 36],
            "name" => ["type" => "string",  "length" => 255],

            "created_at" => ["type" => "datetime", "value" => new \DateTime()],
            "updated_at" => ["type" => "datetime", "value" => new \DateTime()]
        ];
    }

    public static function relations(MapperInterface $mapper, EntityInterface $entity)
    {
        return [
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->uid = (new Base62)->encode(random_bytes(9));
        });

        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->updated_at = new \DateTime();
        });

        $emitter->on("afterInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->log(LogLevel::INFO, "[{$entity->uid}] Created new example {$entity->name}.");
        });

        $emitter->on("afterUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->log(LogLevel::INFO, "[{$entity->uid}] Updated example {$entity->name}.");
        });

        $emitter->on("afterDelete", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->log(LogLevel::INFO, "[{$entity->uid}] Deleted example {$entity->name}.");
        });
    }

    public function timestamp()
    {
        return $this->updated_at->getTimestamp();
    }

    public function etag()
    {
        return md5($this->uuid . $this->timestamp());
    }
}
