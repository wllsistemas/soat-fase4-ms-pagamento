<?php

namespace App\Providers;

// use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepository;
// use App\Infrastructure\Service\JsonWebToken;
// use App\Infrastructure\Repositories\UsuarioEloquentRepository;
// use App\Infrastructure\Service\LaravelAuthService;
// use App\Signature\AuthServiceInterface;
// use App\Signature\TokenServiceInterface;
use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

// use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepository;
// use App\Infrastructure\Repositories\ServicoEloquentRepository;

// use App\Domain\Entity\Material\RepositorioInterface as MaterialRepository;
// use App\Infrastructure\Repositories\MaterialEloquentRepository;

// use App\Domain\Entity\MaterialMovimentacoes\RepositorioInterface as MaterialMovimentacoesRepository;
// use App\Infrastructure\Repositories\MaterialMovimentacoesEloquentRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // "servicos" repository binding
        // $this->app->bind(
        //     ServicoRepository::class,
        //     ServicoEloquentRepository::class
        // );

        // "material" repository binding
        // $this->app->bind(
        //     MaterialRepository::class,
        //     MaterialEloquentRepository::class
        // );

        // "material movimentacoes" repository binding
        // $this->app->bind(
        //     MaterialMovimentacoesRepository::class,
        //     MaterialMovimentacoesEloquentRepository::class
        // );

        // "token" service binding
        // $this->app->bind(
        //     TokenServiceInterface::class,
        //     JsonWebToken::class
        // );

        // "auth" service binding
        // $this->app->bind(
        //     AuthServiceInterface::class,
        //     LaravelAuthService::class
        // );

        $this->app->singleton('rabbitmq.connection', function () {
            return new AMQPStreamConnection(
                config('queue.connections.rabbitmq.hosts.0.host'),
                config('queue.connections.rabbitmq.hosts.0.port'),
                config('queue.connections.rabbitmq.hosts.0.user'),
                config('queue.connections.rabbitmq.hosts.0.password')
            );
        });
    }

    public function boot(): void {}
}
