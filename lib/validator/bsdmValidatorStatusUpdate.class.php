<?php
/**
 * bsdmValidatorStatusUpdate represents a validator for the StatusUpdate widget.
 *
 * @package    surSocialMediavey
 * @subpackage widget
 */
class bsdmValidatorStatusUpdate extends sfValidatorBase
{

  protected function doClean($value)
  {
    if ( !is_array($value) ) {
      throw new sfValidatorError($this, 'invalid');
    }
    
    try {
      $clients = new sfValidatorChoice(array(
        'required' => false, 
        'choices' => array_keys(Doctrine_Core::getTable('StatusUpdate')->getAvailableClients()), 
        'multiple' => true
      ));
      $value['clients'] = $clients->clean(isset($value['clients']) ? $value['clients'] : null);
      
      $message = new sfValidatorString(array(
        'required' => false, 
        'max_length' => 140
      ));
      $value['message'] = $message->clean(isset($value['message']) ? $value['message'] : null);
    } catch (sfValidatorError $e) {
      throw $e;
    }
    
    return $value;
  }
}