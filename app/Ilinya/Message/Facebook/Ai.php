<?php

namespace App\Ilinya\Message\Facebook;

use App\Ilinya\Bot;
use App\Ilinya\BotTracker;
use App\Ilinya\Http\Curl;
use App\Ilinya\Message\Facebook\Codes;
use App\Ilinya\Message\Facebook\Form;
use App\Ilinya\Response\Facebook\PostbackResponse;
use App\Ilinya\Response\Facebook\CategoryResponse;
use App\Ilinya\Response\Facebook\EditResponse;
use App\Ilinya\Response\Facebook\SearchResponse;
use App\Ilinya\Response\Facebook\DialogResponse;
use App\Ilinya\Response\Facebook\EditDetailsResponse;
use App\Ilinya\Response\Facebook\DetailsResponse;
use App\Ilinya\Response\Facebook\AiResponse;
use App\Ilinya\Response\Facebook\FoodResponse;
use App\Ilinya\Response\Facebook\RoomResponse;
use App\Ilinya\Response\Facebook\PackageResponse;
use App\Ilinya\Webhook\Facebook\Messaging;
use App\Ilinya\Helper\Validation;
use App\Ilinya\API\QueueCardFields;

class Ai{
    protected $form;
    protected $post;
    protected $search;
    protected $code;
    protected $tracker;
    protected $edit;
    protected $editDetails;
    protected $validation;
    protected $details;
    protected $curl;
    protected $aiResponse;
    protected $food;
    protected $package;
    protected $room;
    protected $dialog;
  function __construct(Messaging $messaging){
      $this->bot    = new Bot($messaging);
      $this->post   = new PostbackResponse($messaging);
      $this->category = new CategoryResponse($messaging);
      $this->form   = new Form($messaging);
      $this->tracker= new BotTracker($messaging);
      $this->code   = new Codes(); 
      $this->edit   = new EditResponse($messaging);
      $this->search = new SearchResponse($messaging);
      $this->editDetails = new EditDetailsResponse($messaging);
      $this->validation = new Validation($messaging);
      $this->details = new DetailsResponse($messaging);
      $this->aiResponse   = new AiResponse($messaging);
      $this->dialog   = new DialogResponse($messaging);
      $this->curl = new Curl();
      $this->room = new RoomResponse($messaging);
      $this->package = new PackageResponse($messaging);
      $this->food = new FoodResponse($messaging);

  }

  public function manage($reply){
    \Log::info("message : ".$reply);
    $reply = strtolower($reply);
    $this->curl->whitelistWebView();
    $track_flag = $this->tracker->getStage();
    if ($track_flag!=null) {
        $this->bot->reply($this->dialog->manage($reply) , false);
    } else {
        $this->tracker->delete();
        if(strpos($reply, 'hi') !== false || strpos($reply, 'hello') !== false ||strpos($reply, 'help') !== false || strpos($reply, 'hola') !== false){
            $this->bot->reply($this->post->banner(), false);
            $this->bot->reply($this->post->start(), false);
            $this->bot->reply($this->post->inquiry(), false);
            
        }
        else if(strpos($reply, 'thank you') !== false){
            $this->bot->reply($this->aiResponse->thankYou(),  false);
            
        }
        else if(strpos(strtolower($reply) ,"food") !== false){ 
            $this->bot->reply($this->food->foods(), false);
            
        }
        
        else if(strpos(strtolower($reply) ,"rooms") !== false){ 
            $this->bot->reply($this->room->roomMenuStart(), false);
            $this->bot->reply($this->room->roomMenu(), false);
            
        }
        
        else if(strpos(strtolower($reply) ,"packages") !== false){ 
            $this->bot->reply($this->package->packageMenu(), false);
            
        }
        else {
            $this->bot->reply($this->post->banner(), false);
            $this->bot->reply($this->post->start(), false);
            $this->bot->reply($this->post->inquiry(), false);
            
        }
        return response('',200);
    }
  }

}
