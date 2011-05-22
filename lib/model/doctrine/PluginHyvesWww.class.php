<?php

/**
 * PluginHyvesWww
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginHyvesWww extends BaseHyvesWww
{

  public function fromDOMElement(DOMElement $www)
  {
    foreach ($www->childNodes as $field) {
      switch ($field->nodeName) {
        case 'wwwid':
          $this->setWwwId($field->nodeValue);
          break;
        case 'userid':
          $this->setUserid($field->nodeValue);
          break;
        case 'emotion':
          $this->setEmotion($field->nodeValue);
          break;
        case 'where':
          $this->setWwwWhere($field->nodeValue);
          break;
        case 'created':
          $createdAt = new DateTime();
          $createdAt->setTimestamp($field->nodeValue);
          $this->setCreatedAt($createdAt->format('Y-m-d H:i:s'));
          break;
      }
    }
  }
}