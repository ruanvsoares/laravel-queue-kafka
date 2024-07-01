Kafka Queue driver for Laravel
======================
[![Latest Stable Version](https://poser.pugx.org/rapide/laravel-queue-kafka/v/stable?format=flat-square)](https://packagist.org/packages/ruanvsoares/laravel-queue-kafka)
[![Build Status](https://travis-ci.org/rapideinternet/laravel-queue-kafka.svg?branch=master)](https://travis-ci.org/rapideinternet/laravel-queue-kafka)
[![Total Downloads](https://poser.pugx.org/rapide/laravel-queue-kafka/downloads?format=flat-square)](https://packagist.org/packages/ruanvsoares/laravel-queue-kafka)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

#### Installation

1. Install [librdkafka c library](https://github.com/edenhill/librdkafka)

    ```bash
    $ sudo apt-get install -y librdkafka-dev
    ```
2. Install the [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) PECL extension

    ```bash
    $ pecl install rdkafka
    ```
    
3. a. Add the following to your php.ini file to enable the php-rdkafka extension
    `extension=rdkafka.so`
    
   b. Check if rdkafka is installed  
   __Note:__ If you want to run this on php-fpm restart your php-fpm first.
   
       php -i | grep rdkafka
   
   Your output should look something like this
   
       rdkafka
       rdkafka support => enabled
       librdkafka version (runtime) => 1.0.0-RC2
       librdkafka version (build) => 0.11.4.0

    
4. Install this package via composer using:

	    composer require ruanvsoares/laravel-queue-kafka

5. Add LaravelQueueKafkaServiceProvider to `providers` array in `config/app.php` or in `bootstrap/providers.php`:

	    Rapide\LaravelQueueKafka\LaravelQueueKafkaServiceProvider::class,

6. Add these properties to `.env` with proper values:

		QUEUE_DRIVER=kafka

7. If you want to run a worker for a specific consumer group add to `.env`

   KAFKA_CONSUMER_GROUP_ID="group2"
    
    Explaination of consumergroups can be found in this article 
    http://blog.cloudera.com/blog/2018/05/scalability-of-kafka-messaging-using-consumer-groups/

#### Usage

Once you completed the configuration you can use Laravel Queue API. If you used other queue drivers you do not need to change anything else. If you do not know how to use Queue API, please refer to the official Laravel documentation: http://laravel.com/docs/queues

#### Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```

#### Acknowledgement 

This library is inspired by [laravel-queue-rabbitmq](https://github.com/vyuldashev/laravel-queue-rabbitmq) by vyuldashev.
And the Kafka implementations by [Superbalist](https://github.com/Superbalist/php-pubsub-kafka) be sure to check those out. 

#### Contribution

You can contribute to this package by discovering bugs and opening issues. Please, add to which version of package you create pull request or issue.

#### Supported versions of Laravel 

Tested on: [11.*]

## Fixes by Ruan Soares
- Adjust to work with Laravel 11
- Ajust to work with subscribe and commit at consumer, this make the consumer to work with multiple topics and partitions
- Add auto create of default topic
- Fully tested with Laravel 11
