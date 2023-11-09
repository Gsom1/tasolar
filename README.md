# tasolar
Payment Service

## Getting started
Create database
```php bin/console doctrine:database:create```

Apply migrations
```php bin/console doctrine:migrations:migrate```

- I use redis for messages, it should be either redis with persistence or other queue like rabbitmq.

```php bin/console transactions:settle_bulk```