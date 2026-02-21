<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\Mapper;
use App\Models\ServicoModel;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    public function test_from_model_to_entity(): void
    {
        $model = new ServicoModel();
        $model->uuid = 'uuid-456';
        $model->nome = 'Servico Teste';
        $model->valor = 5000;
        $model->criado_em = '2024-01-01 10:00:00';
        $model->atualizado_em = '2024-01-02 10:00:00';
        $model->deletado_em = null;

        $mapper = new Mapper();
        $entidade = $mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-456', $entidade->uuid);
        $this->assertEquals('Servico Teste', $entidade->nome);
        $this->assertEquals(5000, $entidade->valor);
        $this->assertNull($entidade->deletadoEm);
    }

    public function test_from_model_to_entity_com_deletado_em(): void
    {
        $model = new ServicoModel();
        $model->uuid = 'uuid-456';
        $model->nome = 'Servico Teste';
        $model->valor = 5000;
        $model->criado_em = '2024-01-01 10:00:00';
        $model->atualizado_em = '2024-01-02 10:00:00';
        $model->deletado_em = '2024-01-03 10:00:00';

        $mapper = new Mapper();
        $entidade = $mapper->fromModelToEntity($model);

        $this->assertNotNull($entidade->deletadoEm);
        $this->assertTrue($entidade->estaExcluido());
    }

    public function test_valor_convertido_para_inteiro(): void
    {
        $model = new ServicoModel();
        $model->uuid = 'uuid-456';
        $model->nome = 'Servico Teste';
        $model->valor = '5000'; // string do banco
        $model->criado_em = '2024-01-01 10:00:00';
        $model->atualizado_em = '2024-01-02 10:00:00';
        $model->deletado_em = null;

        $mapper = new Mapper();
        $entidade = $mapper->fromModelToEntity($model);

        $this->assertIsInt($entidade->valor);
        $this->assertEquals(5000, $entidade->valor);
    }

    public function test_construtor_vazio(): void
    {
        $mapper = new Mapper();

        $this->assertInstanceOf(Mapper::class, $mapper);
    }
}
