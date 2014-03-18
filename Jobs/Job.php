<?php namespace Addvert\Queue\Jobs;

use DateTime;

abstract class Job {

	/**
	 * The job handler instance.
	 *
	 * @var mixed
	 */
	protected $instance;

	/**
	 * The CodeIgniter instance.
	 *
	 */
	protected $ci;

	/**
	 * The name of the queue the job belongs to.
	 *
	 * @var string
	 */
	protected $queue;

	/**
	 * Indicates if the job has been deleted.
	 *
	 * @var bool
	 */
	protected $deleted = false;

	/**
	 * Fire the job.
	 *
	 * @return void
	 */
	abstract public function fire();

	/**
	 * Delete the job from the queue.
	 *
	 * @return void
	 */
	public function delete()
	{
		$this->deleted = true;
	}

	/**
	 * Determine if the job has been deleted.
	 *
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deleted;
	}

	/**
	 * Release the job back into the queue.
	 *
	 * @param  int   $delay
	 * @return void
	 */
	abstract public function release($delay = 0);

	/**
	 * Get the number of times the job has been attempted.
	 *
	 * @return int
	 */
	abstract public function attempts();

	/**
	 * Get the raw body string for the job.
	 *
	 * @return string
	 */
	abstract public function getRawBody();

	/**
	 * Resolve and fire the job handler method.
	 *
	 * @param  array  $payload
	 * @return void
	 */
	protected function resolveAndFire($payload)
	{
		$job = $this->parseJob($payload);
                
                // Create url from codeigniter
                $params = (isset($job['params'])) ? implode('/', $job['params']) : null;
                $url = array($job['controller'], $job['method'], $params);
                $url = implode('/', $url);
                
                
                $ch = curl_init($url);
                if(!curl_exec($ch)) {
                    return $this->logFailedJob($payload);
                }
              
		// Log complete perform
		log_message('debug', 'Sucess processing job <' . $job['description'] . '> -> ' . $url);

		
	}


	/**
	 * Parse the job declaration into class and method.
	 *
	 * @param  string  $job
	 * @return array
	 */
	protected function parseJob($job)
	{
		return json_decode($job, TRUE);
	}


	/**
	 * Calculate the number of seconds with the given delay.
	 *
	 * @param  \DateTime|int  $delay
	 * @return int
	 */
	protected function getSeconds($delay)
	{
		if ($delay instanceof DateTime)
		{
			return max(0, $delay->getTimestamp() - $this->getTime());
		}
		else
		{
			return intval($delay);
		}
	}

	/**
	 * Get the name of the queue the job belongs to.
	 *
	 * @return string
	 */
	public function getQueue()
	{
		return $this->queue;
	}
        
        
        /**
	 * Log a failed job into storage.
	 *
	 * @param  array $job
	 * @return void
	 */
	protected function logFailedJob(array $job)
	{
            //$this->log($job->getQueue(), $job);
            $job->delete();
	}

}
