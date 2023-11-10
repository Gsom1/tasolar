# Payment Service

## Getting Started
1. **Start Containers**  
   Run `docker-compose up -d` to start all required containers.

2. **Install Dependencies**  
   Access the `tasolar_fpm` container terminal and execute:

```composer install```  


3. **Database Setup**
- Create the database:
  ```
  php bin/console doctrine:database:create
  ```
- Apply migrations:
  ```
  php bin/console doctrine:migrations:migrate
  ```

## Endpoints
The project has two main endpoints. You can use the following `curl` requests or import them into Postman.

1. **Payment Gateway New Payment Endpoint**

```
curl --location 'http://localhost:9080/api/v1/payments' \
--header 'Content-Type: application/json' \
--data '{
"cardNumber": "4665691196396372",
"expiryDate": "11/23",
"cvv": 123,
"amount": 123,
"currency": "USD",
"merchantId": "1"
}'
```


2. **Payment Provider New Payment Endpoint**
```
curl --location 'http://localhost:9080/psp1/payments' \
--header 'Content-Type: application/json' \
--data '{
"cardNumber": "4355068868972142",
"expiryDate": "11/2024",
"cvv": 123,
"amount": 234,
"currency": "USD",
"merchantId": "1"
}'
```
There are 3 providers: psp1, psp2, psp3

**Note:** Any currency can be submitted, but it is recommended to use USD as other currencies may fail during the end-of-day settlement. To generate test cards, visit [DNSChecker Credit Card Generator](https://dnschecker.org/credit-card-generator.php).

## The Exercise
- I use redis for messages, it should be either redis with persistence or other queue like rabbitmq.
- Routing (see PspResolver) is done in very simple form in real project with 100 rules it should be DB or YAML easy-manageable config and parser.

### Concurency
Concurency and multithreading at endpoint level provided by fpm, it runs every request in separate thread.

### End of day settlement
I did 2 implementations for end of day settlement.
1. Command batch processing, see TransactionsSettleBulkCommand.
It fetches specified amount of transactions from DB, 
updates balances and statuses in a transaction and then commits all changes.
In current implementation only one instance of such command should work, if we need to run many simultaneous bulk processing the command should be updated:
- need to keep last fetched transaction id in redis, so every instance gets different transactions
- need to crate shared lock for every card number in a bulk, or change transactions isolation level to read uncommitted.
To run the command use:
```
php bin/console transactions:settle_bulk
```

3. Message consumer. See NewTransactionMessageHandler
Here when new transaction is ready I dispatch an event via redis queue, and there is async consumer,
you can run instances as many as you want
```
php bin/console messenger:consume new_transactions
```
it processes transactions one by one but in many threads concurently, there is a shared lock for card number as well in case different consumer get transaction for the same card.

