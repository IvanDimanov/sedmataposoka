<?php
/**
 * 
 * 
 * 
 */
class Event {
  
  // 
  static private $callbacks = array();
  
  
  /**
   * 
   * 
   * 
   */
  static public function on($eventName, $callback) {
    
    // 
    if (!array_key_exists($eventName, self::$callbacks)) {
      self::$callbacks[$eventName] = array();
    }
    
    // 
    array_push(self::$callbacks[$eventName], $callback);
    
    // 
    end(self::$callbacks[$eventName]);
    return key(self::$callbacks[$eventName]);
  }
  
  
  /**
   * 
   * 
   * 
   * 
   */
  static public function off($eventName, $callbackId = -1) {
    
    // 
    if ($callbackId != -1) {
      unset(self::$callbacks[$eventName][$callbackId]);
    } else {
      unset(self::$callbacks[$eventName]);
    }
  }
  
  
  /**
   * 
   * 
   * 
   */
  static public function trigger($eventName, $callbackArg = null) {
    if (array_key_exists($eventName, self::$callbacks)) {
      foreach(self::$callbacks[$eventName] as $callback) {
        call_user_func($callback, $callbackArg);
      }
    }
  }
}