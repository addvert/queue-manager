<?php namespace Addvert\Queue;

/**
 * Job Queue Library
 * @author      Riccardo Mastellone <r.mastellone@addvert.it>
 * 
 */

require_once 'Jobs/Job.php';
require_once 'Jobs/SqsJob.php';
require_once 'Connectors/Connector.php';
require_once 'Connectors/SqsConnector.php';

class Queue {

	// Default seconds between polling for jobs
	private $_interval = 5;

        // Connector 
        private $connector;

        
	public function __construct() {
            $configs = include('config.php');
            $this->connector = new \Addvert\Queue\SqsConnector($configs);
		
	}

	/**
	 * Enqueue a job for execution.
	 *
	 * @access	public
         * @param 	string $controller The name of the class that contains the code to execute the job.
	 * @param 	string $class The name of the method that contains the code to execute the job.
	 * @param 	array $args Any optional arguments that should be passed when the job is executed.
	 * @param 	string $description Task description
	 * @return	bool
	 */
	public function create($controller, $method = 'index', $params = null, $description = null) {
		// Validate if job is correct
		$this->validateJob($controller);

		// Check if $params is a valid type
		if ($params !== null && !is_array($params)) {
			$params = array($params);
		}

		// Convert job data to hash
		$job = $this->encodeJob($controller, $method, $params, $description);

		// Enqueue the job
		return $this->connector->push($job);

	}


	/**
	 * Generate hash of all job properties to be saved in the scheduled queue.
	 *
	 * @access	private
	 * @param 	string $controller Name of the job controller.
	 * @param 	string $method Name of the job method.
	 * @param 	array $args Array of job arguments.
         * @param 	string $description Description of the job.
	 * @return	string
	 */

	private function encodeJob($controller, $method, $params = null, $description = null) {
		return json_encode(array('controller' => $controller, 'method' => $method, 'params' => $params, 'description' => $description,));
	}

	/**
	 * Ensure that supplied job controller is valid.
	 *
	 * @access	private
	 * @param	string $controller Name of job controller.
	 * @return	bool
	 * @throws	Exception
	 */
	private function validateJob($controller) {
		if (empty($controller)) {
			throw new Exception('Jobs must be given a controller.');
		} 

		return true;
	}


	/**
	 * Get some work done from queues (Order is important)
	 *
	 * @access  public
	 * @param   int $interval Interval to sleep
	 * @return  void
	 */
	public function worker($interval = null) {


		// Check if interval exists
		if ($interval !== null) {
			$this -> _interval = $interval;
		}

		// Log initialized worked
		log_message('debug', 'Worker initialized.');

		// Start infinite loop
		while (true) {
			try {
				// Attempt to find a job
				$job = $this->connector->pop();
				// Check exist job
				if (!$job) {
                                    // Sleep worker during interval
                                    sleep($this -> _interval);
                                    continue;
				}

				// Log got message
				log_message('debug', 'Worker: Got a Job -> ' . $job->getJobId());

				// Execute job
				$job->fire();
                                
                                $job->delete();


			} catch (Exception $e) {
				// Log error
				log_message('error', 'Exception in worker: ' . $e -> getMessage());
			}
		}

		// Log finish worked
		log_message('debug', 'Worker finished.');

	}

        
        



}
