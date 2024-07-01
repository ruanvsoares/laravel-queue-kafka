<?php

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Rapide\LaravelQueueKafka\Queue\Jobs\KafkaJob;
use Rapide\LaravelQueueKafka\Queue\KafkaQueue;
use RdKafka\Message;
use Tests\Jobs\TestJob;

/**
 * @property MockInterface producer
 * @property MockInterface consumer
 * @property MockInterface $container
 * @property array config
 * @property KafkaQueue queue
 */
class KafkaQueueTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->producer = Mockery::mock(\RdKafka\Producer::class);
        $this->consumer = Mockery::mock(\RdKafka\KafkaConsumer::class);
        $this->container = Mockery::mock(\Illuminate\Container\Container::class);

        $this->config = [
            'queue' => str()->random(),
            'sleep_error' => true,
        ];

        $this->queue = new KafkaQueue($this->producer, $this->consumer, $this->config);
        $this->queue->setContainer($this->container);
    }

    public function test_size()
    {
        $size = $this->queue->size();
        $messageCount = 1;

        $this->assertEquals($messageCount, $size);
    }

    public function test_later()
    {
        $delay = 5;
        $job = Mockery::mock(KafkaJob::class);

        $this->expectException(Exception::class);

        $this->queue->later($delay, $job);
    }

    public function test_pop_no_error()
    {
        $queue = $this->config['queue'];
        $message = Mockery::mock(Message::class);
        $message->err = RD_KAFKA_RESP_ERR_NO_ERROR;

        $this->consumer->shouldReceive('subscribe')->with([$queue => $queue]);
        $this->consumer->shouldReceive('consume')->andReturn($message);

        $job = $this->queue->pop($queue);

        $this->assertInstanceOf(KafkaJob::class, $job);
    }

    public function test_pop_end_of_partition()
    {
        $queue = $this->config['queue'];
        $message = Mockery::mock(Message::class);
        $message->err = RD_KAFKA_RESP_ERR__PARTITION_EOF;

        $this->consumer->shouldReceive('subscribe')->with([$queue => $queue]);
        $this->consumer->shouldReceive('consume')->andReturn($message);

        $job = $this->queue->pop($queue);

        $this->assertNull($job);
    }

    public function test_pop_timed_out()
    {
        $queue = $this->config['queue'];
        $message = Mockery::mock(Message::class);
        $message->err = RD_KAFKA_RESP_ERR__TIMED_OUT;

        $this->consumer->shouldReceive('subscribe')->with([$queue => $queue]);
        $this->consumer->shouldReceive('consume')->andReturn($message);

        $job = $this->queue->pop($queue);

        $this->assertNull($job);
    }

    public function test_pop_not_catched_exception()
    {
        $queue = $this->config['queue'];
        $message = Mockery::mock(Message::class);
        $message->err = RD_KAFKA_RESP_ERR__ALL_BROKERS_DOWN;
        $message->shouldReceive('errstr');

        $this->consumer->shouldReceive('subscribe')->with([$queue => $queue]);
        $this->consumer->shouldReceive('consume')->andReturn($message);

        $this->expectException(Exception::class);

        $this->queue->pop($queue);
    }

    public function test_setCorrelationId()
    {
        $id = str()->random();

        $this->queue->setCorrelationId($id);

        $setId = $this->queue->getCorrelationId();

        $this->assertEquals($id, $setId);
    }

    public function test_getConsumer()
    {
        $consumer = $this->queue->getConsumer();

        $this->assertEquals($consumer, $this->consumer);
    }
}
