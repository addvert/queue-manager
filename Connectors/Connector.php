<?php namespace Addvert\Queue;

/**
 * Connector Interface
 *
 * @author Riccardo Mastellone <rmastellone@addvert.it>
 */
interface Connector{
    
        /**
	 * Push a new job onto the queue.
	 *
	 * @param  mixed   $payload
	 * @return mixed
	 */
	public function push($payload);
        
        /**
	 * Pop the next job off of the queue.
	 *
	 * @return \Addvert\Queue\Jobs\Job|null
	 */
	public function pop();
    
}
