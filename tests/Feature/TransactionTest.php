<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_create_transaction()
    {
        $response = $this->postJson('/api/transactions/', [
            'installments' => '2',
            'amount'    => 1000,
            'payment_method' => 'credit_card',
            'async' => 0,
            'capture' => 0,
            'card_number' => '1111110000002225',
            'card_expiration_date' => '0822',
            'card_holder_name' => 'Transação teste',
            'card_cvv' => '333'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure();
    }

    public function test_create_transaction_payment_method_other_credit_card()
    {
        $response = $this->postJson('/api/transactions/', [
            'id' => 500,
            'installments' => '2',
            'amount'    => 1000,
            'payment_method' => 'qualquercoisa',
            'async' => 0,
            'capture' => 0,
            'card_number' => '1111110000002225',
            'card_expiration_date' => '0822',
            'card_holder_name' => 'Transação teste',
            'card_cvv' => '333'
        ]);

        $response->assertStatus(406)
            ->assertJsonStructure();
    }

    public function test_create_transaction_async_true()
    {
        $response = $this->postJson('/api/transactions/', [
            'installments' => '2',
            'amount'    => 1000,
            'payment_method' => 'credit_card',
            'async' => 1,
            'capture' => 0,
            'card_number' => '1111110000002225',
            'card_expiration_date' => '0822',
            'card_holder_name' => 'Transação teste',
            'card_cvv' => '333'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'transação criada aguardando o cartão.'
            ]);
    }

    public function test_transactions_capture_amount()
    {
        $response = $this->postJson('/api/transactions/1/capture', [
            'amount' => 500
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure();
    }

    public function test_transactions_capture_amount_id_invalid()
    {
        $response = $this->postJson('/api/transactions/100/capture', [
            'amount' => 500
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure();
    }

    public function test_transactions_index()
    {
        $response = $this->get('/api/transactions/index');

        $response->assertStatus(200)
            ->assertJsonStructure();
    }

    public function test_transactions_show()
    {
        $response = $this->get('/api/transactions/show/1');

        $response->assertStatus(200)
            ->assertJsonStructure();
    }

    public function test_transactions_show_id_invalid()
    {
        $response = $this->get('/api/transactions/show/100');

        $response->assertStatus(404)
            ->assertJsonStructure();
    }

    public function test_transactions_update()
    {
        $response = $this->put('/api/transactions/update/1', [
            'installments' => '5',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure();
    }

    public function test_transactions_update_id_invalid()
    {
        $response = $this->put('/api/transactions/update/100', [
            'installments' => '5',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure();
    }

    public function test_transactions_delete()
    {
        $response = $this->delete('/api/transactions/delete/1');
        $this->delete('/api/transactions/delete/2');

        $response->assertStatus(200)
            ->assertJsonStructure();
    }
}
