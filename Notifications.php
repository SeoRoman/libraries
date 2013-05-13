<?php

use Illuminate\Support\MessageBag;

Class Notifications {

    const SESSION_KEY = 'notifications';
    protected $bag = null;

    protected $bs = false;    
    protected $bsClose = false;
    protected $bsCloseMessage = '';
    protected $bsFormat = "<div class='alert alert-:key'>".($this->bsClose ? '<button type="button" class="close" data-dismiss="alert">'.$this->bsCloseMessage."</button>:message</div>";    

    public function __construct() {
      $this->bag = new MessageBag();
      $this->bag->setFormat("<div class='notification_:key'><span class=':key'>:message</span></div>");
    }

    public function bs($value) {
      $this->bs = $value;
    }

    public function bsClose($value) {
      $this->bsClose = $value;      
    }

    public function bsCloseMessage($value) {
      $this->bsCloseMessage = $value;
    }

    private function _setBsFormat() {
      $this->bag->setFormat($this->bsFormat);
    }

    public function add($class, $message = '') {

      if (is_array($class)) {
        foreach($class as $c=>$messages) {
          if (is_array($messages)) {
            $this->addArray($c, $messages);
          }
          else {
            $this->add($c, $message);
          }
        }
      }
      else {
        if (is_array($message)) {
          $this->addArray($message);
        }
        else {
          $this->bag->add($class, $message);
        }              
      }

    }

    private function addArray($class, $messages) {
      foreach($messages as $index=>$message) {
        $this->bag->add("{$class} {$class}{$index}", $message);
      }
    }

    public function save() {
      if ($this->bs) {
        $this->set->format($this->bsFormat);
      }
      $messages = '';
      foreach($this->bag->all() as $message) {
        $messages .= $message;
      }

      Session::flash('notifications',$messages);
    }

    public static function has() {
      return Session::has(self::SESSION_KEY);
    }

    public static function get() {
      return Session::get(self::SESSION_KEY);
    }

    public static function __callStatic($name, $args) {
      $notification = new static;
      $message = $args[0];
      if (isset($args[1])) {
        $this->bs = $args[1];
      }
      if (isset($args[2])) {
        $this->bsClose = $args[2];
      }
      if (isset($args[3])) {
        $this->bsCloseMessage = $args[3];
      }
      if (is_array($message)) {
        foreach($message as $index=>$m) {
          $notification->add("{$name} {$name}{$index}",$m);
        }
      }
      else {
        $notification->add("{$name}",$message);
      }
      return $notification;
    }

  }

?>