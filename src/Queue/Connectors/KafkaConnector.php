<?php

namespace Rapide\LaravelQueueKafka\Queue\Connectors;

use Illuminate\Container\Container;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Rapide\LaravelQueueKafka\Queue\KafkaQueue;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RdKafka\TopicConf;

class KafkaConnector implements ConnectorInterface
{

    /**
     * KafkaConnector constructor.
     *
     * @param Container $container
     */
    public function __construct(
        private Container $container,
    )
    {
    }

    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {

        /** @var TopicConf $topicConf */
        $topicConf = $this->container->make('queue.kafka.topic_conf', []);
        $topicConf->set('auto.offset.reset', 'largest');

        /** @var Conf $conf */
        $conf = $this->container->make('queue.kafka.conf', []);
        $conf->set('metadata.broker.list', $config['brokers']);

        if (true === $config['sasl_enable']) {
            $conf->set('sasl.mechanisms', 'PLAIN');
            $conf->set('sasl.username', $config['sasl_plain_username']);
            $conf->set('sasl.password', $config['sasl_plain_password']);
            $conf->set('ssl.ca.location', $config['ssl_ca_location']);
        }

        /** @var Producer $producer */
        $producer = $this->container->make('queue.kafka.producer', ['conf' => $conf]);

        /** Autocreate default topic */
            $producer->newTopic($config['queue'],$topicConf);

        $conf->set('group.id', $config['consumer_group_id'] ?? 'php-pubsub');
        $conf->set('enable.auto.commit', 'false');
        $conf->setDefaultTopicConf($topicConf);

        /** @var KafkaConsumer $consumer */
        $consumer = $this->container->makeWith('queue.kafka.consumer', ['conf' => $conf]);

        return new KafkaQueue(
            $producer,
            $consumer,
            $config
        );
    }
}
