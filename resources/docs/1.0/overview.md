
* [GetStatus](#section-1)
* [Store](#section-2)
* [CaptureAmount](#section-3)
* [Index](#section-4)
* [Show](#section-5)
* [Update](#section-6)
* [Delete](#section-7)


# Introdução

---

<larecipe-card shadow>
    :school_satchel: Teste técnico Brasil Cash!<br><br>
    :school_satchel: by: Carlos Eduardo Rios Pontes
</larecipe-card>

<a name="section-1"></a>

## getStatus

:small_blue_diamond: Essa função é chamada na `public function store` para definir o status da transação de acordo com a seguinte regra:
<br> O status padrão da transação deve ser processing e após processar a transação o status irá mudar de acordo com o final do cartão: <br>
a. Se o final do cartão estiver entre 1 e 4 o status deve ser paid <br>
b. Se o final do cartão estiver entre 5 e 8 o status deve ser refused <br>
c. Se o final do cartão for 9 o status deve ser aleatório entre: authorized, paid, refused.<br>


<a name="section-2"></a>


## Store

:small_blue_diamond: Para realizar a transação utilize a rota **`/api/transactions`** com o método **`POST`**. Segue o exemplo de dados enviados:

<larecipe-badge type="primary">POST</larecipe-badge>

```http
/api/transactions
```
<br>

## :bulb: Exemplo do request body


```javascript
{
    "installments": "12",
    "amount": 1025,
    "payment_method": "credit_card",
    "async": "false",
    "capture": "true",
    "updated_at": "2022-06-11T00:12:46.000000Z",
    "created_at": "2022-06-11T00:12:45.000000Z",
    "card": {
        "card_number": "1234560000004441",
        "card_expiration_date": "0722",
        "card_holder_name": "Teste BR_cash",
        "card_cvv": "123",
    }
}
```

## :bulb: Exemplo do response body


```javascript
{   
    "id": 47,
    "installments": "12",
    "amount": 10.25,
    "captured_amount": 10.25,
    "paid_amount": 10.25,
    "payment_method": "credit_card",
    "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
    "status": 1,
    "updated_at": "2022-06-11T00:15:41.000000Z",
    "created_at": "2022-06-11T00:15:41.000000Z",
    "card": {
        "card_id": 47,
        "card_holder_name": "Teste BR_cash",
        "card_first_digits": "123456",
        "card_last_digits": "4441",
        "created_at": "2022-06-11T00:15:41.000000Z",
        "updated_at": "2022-06-11T00:15:41.000000Z"
    }
}
```
<br>

::round_pushpin: Dados referentes à tabela **`transactions`**

<br>

| Parâmetro         | Tipo      | Validações                                            | Descrição do campo
| :-                | :-        | :-                                                    |
| installments      | integer   | integer - between:1,12             | Número de parcelas escolhido para a transação
| amount            | integer   | required - integer - min:100   | À principio será o valor autorizado 
| captured_amount   | integer   | --                                                    | Valor capturado na transação
| paid_amount       | integer   | --                                                    | Valor pago na transação
| ref_id            | string    | --                                                    | id único da transação
| status            | integer   | --                                                    | Status da transação
| payment_method    | string    | required                                     | Método de pagamento
| async             | boolean   | boolean                          | Informa se o transação será imediata ou entrará em uma fila
| capture           | boolean   | boolean                        | Informa se o valor será capturado na hora da transação ou posteriormente
________________________________________________________________________________________________________________________________________________________________________________

:black_small_square: Campo 'async' -> Enviar false caso queira manter o processamento síncrono de uma transação. Ou seja, a resposta da transação é recebida na hora.
 <br>
::black_small_square: Campo 'capture' -> Enviar true se a transação deve ser capturada nomomento da criação ou false para ser capturada posteriormente.

<br><br><br>

::round_pushpin: Dados referentes à tabela **` Cards `**

<br>

| Parâmetro             | Tipo   | Validações                                                                       | Descrição do campo
| :-                    | :-     | :-                                                                               | 
| card_number           | string | required_if:payment_method,credit_card - integer - digits:16 - ends_with:1 à 9   | Número do cartão
| card_expiration_date  | string | required_if:payment_method,credit_card - digits:4                                | Data de validade do cartão
| card_holder_name      | string | required_if:payment_method,credit_card - max:255                                 | Nome do cartão
| card_cvv              | string | required_if:payment_method,credit_card - integer - digits:3                      | Código de segurança
| card_first_digits     | string | --                                                                               | 6 primeiros digitos do cartão
| card_last_digits      | string | --                                                                               | 4 útimos digitos do cartão
| transaction_id        | id     | --                                                                        | chave estrangeira da tabela **` transactions `**

<br>

<a name="section-3"></a>

## captureAmount

:small_blue_diamond: Quando a transação criada na rota anterior receber `capture = false` a mesma transação deve ser executa na seguinte rota utilizando o seu id  `/api/transactions/:transaction_id/capture` com o método **`POST`**. Segue o exemplo de dados enviados:

<larecipe-badge type="primary">POST</larecipe-badge>

```http
/api/transactions/:transaction_id/capture
```
<br>

## :bulb: Exemplo do request body

```javascript
{   
    "amount": 1023,
}
```
:black_small_square: O amount não poderá ser menor do que o valor autorizado antes.
::black_small_square: Apenas transações com o status Authorized poderão ser utilizadas.

## :bulb: Exemplo do response body

```javascript
{
    "id": 44,
    "installments": 12,
    "amount": 10.25,
    "captured_amount": 10.23,
    "paid_amount": 10.23,
    "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
    "status": 1,
    "payment_method": "credit_card",
    "created_at": "2022-06-10T23:48:28.000000Z",
    "updated_at": "2022-06-10T23:48:33.000000Z",
    "card": {
        "card_id": 44,
        "card_holder_name": "Teste BR_cash",
        "card_first_digits": "123456",
        "card_last_digits": "4441",
        "created_at": "2022-06-10T23:48:28.000000Z",
        "updated_at": "2022-06-10T23:48:28.000000Z"
    }
}
```

<a name="section-4"></a>

## Index

Para retornar todas transaçãoes e seus respectivos cartões utilize a rota **`/api/transactions/index`** com o método **`GET`**. Segue o exemplo de dados enviados:

<larecipe-badge type="primary">GET</larecipe-badge>

```http
/api/transactions/index
```

```javascript
{
    "sucess": [
        {
            "id": 2,
            "installments": 12,
            "amount": 10.25,
            "captured_amount": 10.25,
            "paid_amount": 10.25,
            "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
            "status": 0,
            "payment_method": "credit_card",
            "async": 1,
            "capture": 1,
            "created_at": "2022-06-11T04:17:38.000000Z",
            "updated_at": "2022-06-11T04:17:38.000000Z",
            "card": null
        },
        {
            "id": 3,
            "installments": 12,
            "amount": 10.25,
            "captured_amount": 10.25,
            "paid_amount": 10.25,
            "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
            "status": 0,
            "payment_method": "credit_card",
            "async": 1,
            "capture": 1,
            "created_at": "2022-06-11T04:34:22.000000Z",
            "updated_at": "2022-06-11T04:34:22.000000Z",
            "card": null
        },
        {
            "id": 4,
            "installments": 12,
            "amount": 10.25,
            "captured_amount": 10.25,
            "paid_amount": 10.25,
            "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
            "status": 0,
            "payment_method": "credit_card",
            "async": 1,
            "capture": 1,
            "created_at": "2022-06-11T04:35:00.000000Z",
            "updated_at": "2022-06-11T04:35:00.000000Z",
            "card": null
        },
        {
            "id": 9,
            "installments": 12,
            "amount": 10.25,
            "captured_amount": 10.25,
            "paid_amount": 10.25,
            "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
            "status": 1,
            "payment_method": "credit_card",
            "async": 0,
            "capture": 1,
            "created_at": "2022-06-11T04:38:08.000000Z",
            "updated_at": "2022-06-11T04:38:08.000000Z",
            "card": {
                "card_id": 1,
                "card_number": "1234560000004441",
                "card_expiration_date": "0722",
                "card_holder_name": "Teste BR_cash",
                "card_cvv": "123",
                "card_first_digits": "123456",
                "card_last_digits": "4441",
                "transaction_id": 9,
                "created_at": "2022-06-11T04:38:08.000000Z",
                "updated_at": "2022-06-11T04:38:08.000000Z"
            }
        }
]}
```

<a name="section-5"></a>

## Show

Para retornar apenas uma transação cujo id será indicado na rota e seu respectivo cartão utilize a rota **`/api/transactions/show/:id`** com o método **`GET`**. Segue o exemplo de dados enviados:<br>

<larecipe-badge type="primary">GET</larecipe-badge>

```http
/api/transactions/show/:id
```

```javascript
{
    "sucess": [
        {
            "id": 9,
            "installments": 12,
            "amount": 10.25,
            "captured_amount": 10.25,
            "paid_amount": 10.25,
            "ref_id": "d41d8cd98f00b204e9800998ecf8427e",
            "status": 1,
            "payment_method": "credit_card",
            "async": 0,
            "capture": 1,
            "created_at": "2022-06-11T04:38:08.000000Z",
            "updated_at": "2022-06-11T04:38:08.000000Z",
            "card": {
                "card_id": 1,
                "card_number": "1234560000004441",
                "card_expiration_date": "0722",
                "card_holder_name": "Teste BR_cash",
                "card_cvv": "123",
                "card_first_digits": "123456",
                "card_last_digits": "4441",
                "transaction_id": 9,
                "created_at": "2022-06-11T04:38:08.000000Z",
                "updated_at": "2022-06-11T04:38:08.000000Z"
            }
        }
]}
```

<a name="section-6"></a>


## Update

Para atualizar o campo `installments` de uma transação cujo o id será indicado na rota, utilize **`/api/transactions/update/:id`** com o método **`PUT`**.

<larecipe-badge type="primary">PUT</larecipe-badge>

```http
/api/transactions/update/:id
```

<a name="section-7"></a>


## Delete

Para excluir transação cujo o id será indicado na rota, utilize **`/api/transactions/delete/:id`** com o método **`DELETE`**.

<larecipe-badge type="primary">DELETE</larecipe-badge>

```http
/api/transactions/delete/:id
```
