<?php
/**
* Jkl_Og
*/
class Jk_Og
{
  private $_title;
  private $_description;
  private $_image;
  private $_siteName;
  private $_type;

  private $FB_APP_ID;
  
  function __construct($siteName)
  {
    $this->_siteName = $siteName;
  }

  public function setFbAppId($id)
  {
      $this->FB_APP_ID = $id;
  }
  
  public function setTitle($title)
  {
    $this->_title = $title;
  }
  
  public function setDescription($description)
  {
    $this->_description = $description;
  }
  
  public function setImage($image)
  {
    $this->_image = $image;
  }
  
  public function setType($type)
  {
    $this->_type = $type;
  }
  
  public function getMetaData()
  {

    if (!isset($this->FB_APP_ID)) {
        throw new Exception("Facebook App ID must be set before getting OG Meta tags", 1);
    }

    $ogMeta = '';
    if (isset($this->_title)) {
      $ogMeta .= '<meta property="og:title" content="' . str_replace('"', '&quot;', $this->_title) . '" />' . "\n";
    }
    
    if (isset($this->_description)) {
      // need to trim the description
      $ogMeta .= '<meta property="og:description" content="' . str_replace('"', '&quot;', $description) . '" />' . "\n";
    }
    
    if (isset($this->_image)) {
      $ogMeta .= '<meta property="og:image" content="' . $this->_image . '" />' . "\n";
    }
    
    if (isset($this->_type)) {
      $ogMeta .= '<meta property="og:type" content="' . $this->_type . '" />' . "\n";
    }
    
    $ogMeta .= '<meta property="og:site_name" content="' . $this->_siteName . '" />' . "\n";
    $ogMeta .= '<meta property="fb:app_id" content="' . $this->FB_APP_ID . '" />' . "\n";
    
    return $ogMeta;
  }
  
}