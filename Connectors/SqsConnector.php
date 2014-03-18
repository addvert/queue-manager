<?php namespace Addvert\Queue;

/**
 * SqsConnector implements Interface
 *
 * @author Riccardo Mastellone <rmastellone@addvert.it>
 */
class SqsConnector implements Connector {
    
    private $queue;
    private $sqs;
  
    function __construct(array $config) {
        $sqsConfig = array_only($config, array('key', 'secret', 'region', 'default_cache_config'));
        $this->sqs = \Aws\Sqs\SqsClient::factory($sqsConfigs);
        $this->queue = $config['queue'];
    }
    
    
        /**
	 * Push a new job onto the queue.
	 *
	 * @param  mixed   $payload
	 * @return mixed
	 */
	public function push($payload)
	{
		$response = $this->sqs->sendMessage(array('QueueUrl' => $this->getQueue(), 'MessageBody' => $payload));
                
		return $response->get('MessageId');
	}
        
        
        /**
	 * Pop the next job off of the queue.
	 *
	 * @return \Addvert\Queue\Jobs\Job|null
	 */
	public function pop()
	{
            
		$response = $this->sqs->receiveMessage(
			array('QueueUrl' => $this->getQueue(), 'AttributeNames' => array('ApproximateReceiveCount'))
		);

		if (count($response['Messages']) > 0)
		{
			return new Jobs\SqsJob($this->sqs, $this->getQueue(), $response['Messages'][0]);
		}
	}
        
        
        
        private function getQueue() {
            return $this->queue;
        }


}
