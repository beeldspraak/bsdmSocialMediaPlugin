<?php

interface iSocialMedia
{

  /**
   * Get the id of the last fetched tweet
   *
   * @param integer $excludeId id of the message to exclude
   * @return integer
   */
  public function getLatestFetchedId($excludeId = null);

  /**
   * Sync messages with the API and save them to the database
   *
   * @param integer $refreshTime the refresh time for messages is 10 minutes by default
   * @param integer $excludeId id of the message to exclude
   * @return void
   */
  public function sync($refreshTime = 600, $excludeId = null);

  /**
   * Send a message with the API and save the message object
   * 
   * @param string $message
   * @param StatusUpdate $statusUpdate the status update object that is the trigger for the message
   * @return [message object] if the message is send and saved succesfully, if not send and saved the error is thrown
   */
  public function send($message, StatusUpdate $statusUpdate = null);
}