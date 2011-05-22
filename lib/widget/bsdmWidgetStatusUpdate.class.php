<?php

/**
 * bsdmWidgetStatusUpdate represents a widget to send social media status updates
 *
 * @package    SocialMedia
 * @subpackage widget
 */
class bsdmWidgetStatusUpdate extends sfWidgetForm
{

  /**
   * Configure widget
   * Optional option:
   * * template.html:	 override default template
   *
   * @param array $options
   * @param array $attributes
   */
  
  public function configure($options = array(), $attributes = array())
  {
    $this->addOption('template.html', "
    <ul class=\"dm_form_elements\">\n
      <li class=\"dm_form_element clearfix\">{select.clients}</li>\n
      <li class=\"dm_form_element clearfix\">{input.message}</li>\n
    </ul>
    ");
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // avoid any notice errors to invalid $value format
    $value['clients'] = isset($value['clients']) ? $value['clients'] : array();
    $value['message'] = isset($value['message']) ? $value['message'] : '';
    
    // define main template variables
    $template_vars = array();
    
    // define the select clients widget
    $clients = new sfWidgetFormChoice(array(
      'expanded' => true, 
      'multiple' => true, 
      'choices' => Doctrine_Core::getTable('StatusUpdate')->getAvailableClients()
    ));
    $template_vars['{select.clients}'] = $clients->render($name . '[clients]', $value['clients']);
    
    // define the message widget
    $message = new sfWidgetFormTextarea(array(), array(
      'rows' => 2
    ));
    $template_vars['{input.message}'] = $message->render($name . '[message]', $value['message']);
    
    // merge template and variables
    return strtr($this->getOption('template.html'), $template_vars);
  }
}