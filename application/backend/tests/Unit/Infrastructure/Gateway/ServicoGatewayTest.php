<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ServicoGatewayTest extends TestCase
{
    private RepositorioInterface $repositorio;
    private ServicoGateway $gateway;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_encontrar_por_identificador_unico_delega_ao_repositorio(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Teste',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-456', 'uuid')
            ->willReturn($entidade);

        $resultado = $this->gateway->encontrarPorIdentificadorUnico('uuid-456', 'uuid');

        $this->assertSame($entidade, $resultado);
    }

    public function test_encontrar_retorna_null_quando_nao_existe(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $resultado = $this->gateway->encontrarPorIdentificadorUnico('inexistente', 'uuid');

        $this->assertNull($resultado);
    }

    public function test_criar_delega_ao_repositorio(): void
    {
        $dados = ['nome' => 'Servico', 'valor' => 5000];

        $this->repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn(['uuid' => 'new-uuid', ...$dados]);

        $resultado = $this->gateway->criar($dados);

        $this->assertEquals('new-uuid', $resultado['uuid']);
    }

    public function test_listar_delega_ao_repositorio_com_colunas_especificas(): void
    {
        $this->repositorio->expects($this->once())
            ->method('listar')
            ->with(['uuid', 'nome', 'valor', 'criado_em', 'atualizado_em'])
            ->willReturn([['uuid' => '1'], ['uuid' => '2']]);

        $resultado = $this->gateway->listar();

        $this->assertCount(2, $resultado);
    }

    public function test_deletar_delega_ao_repositorio(): void
    {
        $this->repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-456')
            ->willReturn(true);

        $resultado = $this->gateway->deletar('uuid-456');

        $this->assertTrue($resultado);
    }

    public function test_atualizar_delega_ao_repositorio(): void
    {
        $novosDados = ['nome' => 'Novo Servico', 'valor' => 7000];

        $this->repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-456', $novosDados)
            ->willReturn(['uuid' => 'uuid-456', ...$novosDados]);

        $resultado = $this->gateway->atualizar('uuid-456', $novosDados);

        $this->assertEquals('Novo Servico', $resultado['nome']);
    }
}
