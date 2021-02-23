<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrivadoEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    // public $actionData;

    /**
     * Create a new event instance.
     *
     * @author Author
     *
     * @return void
     */

    // $actionId, $actionData
    public function __construct($id)
    {
        $this->id = $id;
        // $this->actionId = $actionId;
        // $this->actionData = $actionData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @author Author
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel.' . $this->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @author Author
     *
     * @return array
     */
    // public function broadcastWith()
    // {
    //     return [
    //         'actionId' => $this->actionId,
    //         'actionData' => $this->actionData,
    //     ];
    // }

    public function broadcastWith()
    {
        return [
            'message' => "Mensaje por canal privado",
            // 'actionData' => $this->actionData,
        ];
    }


    public function broadcastAs(){
        return 'test-privado';
    }
}