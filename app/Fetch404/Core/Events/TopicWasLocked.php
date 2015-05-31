<?php namespace Fetch404\Core\Events;

use App\Events\Event;
use Fetch404\Core\Models\Topic;
use Fetch404\Core\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TopicWasLocked extends Event implements ShouldBroadcast {

	use SerializesModels;

	public $topic;
	public $responsibleUser;

	/**
	 * Create a new event instance.
	 *
	 * @param Topic $topic
	 * @param User $responsibleUser
	 * @type mixed
	 */
	public function __construct(Topic $topic, User $responsibleUser)
	{
		//
		$this->topic = $topic;
		$this->responsibleUser = $responsibleUser;
	}

	/**
	 * Get the topic for this event.
	 *
	 * @return Topic
	 */
	public function getTopic()
	{
		return $this->topic;
	}

	/**
	 * Get the user responsible for this action.
	 *
	 * @return User
	 */
	public function getResponsibleUser()
	{
		return $this->responsibleUser;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['topic-' . $this->topic->id];
	}
}
