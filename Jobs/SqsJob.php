<?php namespace Addvert\Queue\Jobs;

use Aws\Sqs\SqsClient;

class SqsJob extends Job {

	/**
	 * The Amazon SQS client instance.
	 *
	 * @var \Aws\Sqs\SqsClient
	 */
	protected $sqs;

	/**
	 * The Amazon SQS job instance.
	 *
	 * @var array
	 */
	protected $job;

	/**
	 * Create a new job instance.
	 *
	 * @param  \Aws\Sqs\SqsClient  $sqs
	 * @param  string  $queue
	 * @param  array   $job
	 * @return void
	 */
	public function __construct(SqsClient $sqs,
                                $queue,
                                array $job)
	{
		$this->sqs = $sqs;
		$this->job = $job;
		$this->queue = $queue;
        }
	/**
	 * Fire the job.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->resolveAndFire($this->getRawBody());
	}

	/**
	 * Get the raw body string for the job.
	 *
	 * @return string
	 */
	public function getRawBody()
	{
		return $this->job['Body'];
	}

	/**
	 * Delete the job from the queue.
	 *
	 * @return void
	 */
	public function delete()
	{
		parent::delete();

		$this->sqs->deleteMessage(array(

			'QueueUrl' => $this->queue, 'ReceiptHandle' => $this->job['ReceiptHandle'],

		));
	}

	/**
	 * Get the number of times the job has been attempted.
	 *
	 * @return int
	 */
	public function attempts()
	{
		return (int) $this->job['Attributes']['ApproximateReceiveCount'];
	}

	/**
	 * Get the job identifier.
	 *
	 * @return string
	 */
	public function getJobId()
	{
		return $this->job['MessageId'];
	}



}