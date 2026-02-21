<?php

namespace App\Infrastructure\Repositories;

class MercadoPagoMongoRepository
{
    public static function getMongoDB(): \MongoDB\Database
    {
        $config = config('database.connections.mongodb');

        $client = new \MongoDB\Client(
            "mongodb://{$config['host']}:{$config['port']}",
            [
                'username'   => $config['username'],
                'password'   => $config['password'],
                'authSource' => $config['options']['authSource']
            ]
        );

        return $client->selectDatabase(env('MONGO_DATABASE'));
    }
}
