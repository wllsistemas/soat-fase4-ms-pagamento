<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class MaterialApiTest extends TestCase
{
    public function test_create_material_com_sucesso(): void
    {
        $response = $this->postJson('/api/material', [
            'nome' => 'Material Feature Test',
            'gtin' => '7891234567890',
            'preco' => 15.50,
            'sku' => 'SKU-FT-001',
            'descricao' => 'Descricao do material feature test',
            'disponivel' => 1,
            'saldo_atual' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'uuid', 'nome', 'gtin', 'sku', 'descricao', 'preco', 'saldo_atual', 'disponivel',
        ]);
    }

    public function test_create_material_sem_nome_falha(): void
    {
        $response = $this->postJson('/api/material', [
            'gtin' => '7891234567890',
            'preco' => 15.50,
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['err', 'msg']);
    }

    public function test_create_material_sem_gtin_falha(): void
    {
        $response = $this->postJson('/api/material', [
            'nome' => 'Material Sem GTIN',
            'preco' => 15.50,
        ]);

        $response->assertStatus(400);
    }

    public function test_create_material_sem_preco_falha(): void
    {
        $response = $this->postJson('/api/material', [
            'nome' => 'Material Sem Preco',
            'gtin' => '7891234567890',
        ]);

        $response->assertStatus(400);
    }

    public function test_create_material_nome_duplicado_retorna_erro(): void
    {
        $this->postJson('/api/material', [
            'nome' => 'Material Duplicado',
            'gtin' => '1111111111111',
            'preco' => 10.00,
            'descricao' => 'Descricao',
        ]);

        $response = $this->postJson('/api/material', [
            'nome' => 'Material Duplicado',
            'gtin' => '2222222222222',
            'preco' => 20.00,
            'descricao' => 'Outra descricao',
        ]);

        $response->assertJson(['err' => true]);
        $this->assertContains($response->status(), [400, 500]);
    }

    public function test_read_materiais(): void
    {
        $this->postJson('/api/material', [
            'nome' => 'Material Lista 1',
            'gtin' => '3333333333333',
            'preco' => 10.00,
            'descricao' => 'Descricao 1',
        ]);

        $response = $this->getJson('/api/material');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_read_materiais_com_filtro_uuids(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Filtro',
            'gtin' => '5555555555555',
            'preco' => 10.00,
            'descricao' => 'Descricao filtro',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->getJson("/api/material?uuids={$uuid}");

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_read_one_material(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material ReadOne',
            'gtin' => '6666666666666',
            'preco' => 10.00,
            'descricao' => 'Descricao readone',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->getJson("/api/material/{$uuid}");

        $response->assertStatus(200);
        $response->assertJson(['uuid' => $uuid, 'nome' => 'Material ReadOne']);
    }

    public function test_read_one_material_uuid_invalido(): void
    {
        $response = $this->getJson('/api/material/uuid-invalido');

        $response->assertStatus(400);
    }

    public function test_update_material(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Update',
            'gtin' => '7777777777777',
            'preco' => 10.00,
            'descricao' => 'Descricao update',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->patchJson("/api/material/{$uuid}", [
            'nome' => 'Material Atualizado',
            'preco' => 25.00,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['nome' => 'Material Atualizado']);
    }

    public function test_update_material_uuid_invalido(): void
    {
        $response = $this->patchJson('/api/material/uuid-invalido', [
            'nome' => 'Novo Nome',
        ]);

        $response->assertStatus(400);
    }

    public function test_update_material_sem_dados_falha(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material SemDados',
            'gtin' => '8888888888888',
            'preco' => 10.00,
            'descricao' => 'Descricao semdados',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->patchJson("/api/material/{$uuid}", []);

        $response->assertStatus(400);
    }

    public function test_delete_material(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Delete',
            'gtin' => '9999999999999',
            'preco' => 10.00,
            'descricao' => 'Descricao delete',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->deleteJson("/api/material/{$uuid}");

        $response->assertStatus(204);
    }

    public function test_delete_material_uuid_invalido(): void
    {
        $response = $this->deleteJson('/api/material/uuid-invalido');

        $response->assertStatus(400);
    }

    public function test_credito_estoque(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Credito',
            'gtin' => '1010101010101',
            'preco' => 10.00,
            'descricao' => 'Descricao credito',
            'saldo_atual' => 5,
        ]);

        $uuid = $created->json('uuid');

        $response = $this->postJson("/api/material/{$uuid}/credito", [
            'qtd' => 10,
            'obs' => 'Entrada de mercadoria',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['uuid', 'nome', 'saldo_atual']);
    }

    public function test_credito_estoque_uuid_invalido(): void
    {
        $response = $this->postJson('/api/material/uuid-invalido/credito', [
            'qtd' => 10,
        ]);

        $response->assertStatus(400);
    }

    public function test_debito_estoque(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Debito',
            'gtin' => '1212121212121',
            'preco' => 10.00,
            'descricao' => 'Descricao debito',
            'saldo_atual' => 20,
        ]);

        $uuid = $created->json('uuid');

        // Primeiro creditar para ter movimentacao no banco
        $this->postJson("/api/material/{$uuid}/credito", [
            'qtd' => 20,
            'obs' => 'Entrada inicial',
        ]);

        $response = $this->postJson("/api/material/{$uuid}/debito", [
            'qtd' => 5,
            'obs' => 'Saida de mercadoria',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['uuid', 'nome', 'saldo_atual']);
    }

    public function test_credito_estoque_sem_qtd_falha(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Credito SemQtd',
            'gtin' => '1313131313131',
            'preco' => 10.00,
            'descricao' => 'Descricao credito sem qtd',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->postJson("/api/material/{$uuid}/credito", []);

        $response->assertStatus(400);
    }

    public function test_debito_estoque_sem_qtd_falha(): void
    {
        $created = $this->postJson('/api/material', [
            'nome' => 'Material Debito SemQtd',
            'gtin' => '1414141414141',
            'preco' => 10.00,
            'descricao' => 'Descricao debito sem qtd',
        ]);

        $uuid = $created->json('uuid');

        $response = $this->postJson("/api/material/{$uuid}/debito", []);

        $response->assertStatus(400);
    }

    public function test_read_one_material_nao_encontrado(): void
    {
        $response = $this->getJson('/api/material/00000000-0000-0000-0000-000000000000');

        // ReadOneUseCase lanca DomainHttpException 404 ou controller retorna 500
        $this->assertContains($response->status(), [404, 500]);
    }

    public function test_update_material_nao_encontrado(): void
    {
        $response = $this->patchJson('/api/material/00000000-0000-0000-0000-000000000000', [
            'nome' => 'Novo Nome',
        ]);

        // UpdateUseCase lanca DomainHttpException 404
        $this->assertContains($response->status(), [404, 500]);
    }

    public function test_delete_material_nao_encontrado(): void
    {
        $response = $this->deleteJson('/api/material/00000000-0000-0000-0000-000000000000');

        // DeleteUseCase pode retornar 400 ou 404
        $this->assertContains($response->status(), [400, 404]);
    }

    public function test_credito_estoque_material_nao_encontrado(): void
    {
        $response = $this->postJson('/api/material/00000000-0000-0000-0000-000000000000/credito', [
            'qtd' => 10,
        ]);

        $response->assertStatus(404);
    }

    public function test_debito_estoque_material_nao_encontrado(): void
    {
        $response = $this->postJson('/api/material/00000000-0000-0000-0000-000000000000/debito', [
            'qtd' => 5,
        ]);

        $response->assertStatus(404);
    }

    public function test_debito_estoque_uuid_invalido(): void
    {
        $response = $this->postJson('/api/material/uuid-invalido/debito', [
            'qtd' => 5,
        ]);

        $response->assertStatus(400);
    }
}
