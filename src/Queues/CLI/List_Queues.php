<?php

namespace Tribe\Libs\Queues\CLI;

use cli\Table as CLI_Table;
use Tribe\Libs\CLI\Command;
use Tribe\Libs\Queues\Queue_Collection;
use Tribe\Libs\Queues\Contracts\Queue;

class List_Queues extends Command {

	/**
	 * @var Queue_Collection
	 */
	protected $queues;

	public function __construct( Queue_Collection $queues ) {
		$this->queues  = $queues;
		parent::__construct();
	}

	public function description() {
		return __( 'List Backends.', 'tribe' );
	}

	public function arguments() {
		return [];
	}

	public function command() {
		return 'queues list';
	}

	public function run_command( $args, $assoc_args ) {
		$queues = [];
		foreach ( $this->queues->queues() as $queue ) {

			/** @var Queue $queue */
			$parts    = explode( '\\', $queue->get_backend_type() );
			$queues[] = [
				'Queue'        => $queue->get_name(),
				'Backend'      => end( $parts ),
				'Pending Jobs' => $queue->count(),
			];
		}


		$table = new CLI_Table( $queues );
		$table->display();
	}
}
