<?php
/**
 * Jkl_Og
 * v. 1.0
 */
class Jk_Og
{
    private $_data = array(
        'fbAppId' => null,
        'siteName' => null,
        'title' => null,
        'description' => null,
        'image' => null,
        'siteName' => null,
        'type' => null,
        'url' => null,
        );

    function __construct($siteName)
    {
        $this->siteName = $siteName;
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid ' . get_class() . ' property "' . $name . '"');
        }

        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid property "' . $name . '"');
        }

        return $this->_data[$name];
    }

    public function getMetaData()
    {
        if (null === $this->fbAppId) {
            throw new Exception('Facebook App ID must be set before getting OG Meta tags', 1);
        }

        $ogMeta = '';
        if (null !== $this->title) {
            $ogMeta .= '<meta property="og:title" content="' . str_replace('"', '&quot;', $this->title) . '" />' . "\n";
        }

        if (null !== $this->description) {
            // need to trim the description
            $ogMeta .= '<meta property="og:description" content="' . str_replace('"', '&quot;', $this->description) . '" />' . "\n";
        }

        if (null !== $this->image) {
            $ogMeta .= '<meta property="og:image" content="' . $this->image . '" />' . "\n";
        }

        if (null !== $this->type) {
            $ogMeta .= '<meta property="og:type" content="' . $this->type . '" />' . "\n";
        }

        if (null !== $this->url) {
            $ogMeta .= '<meta property="og:url" content="' . $this->url . '" />' . "\n";
        }

        $ogMeta .= '<meta property="og:site_name" content="' . $this->siteName . '" />' . "\n";
        $ogMeta .= '<meta property="fb:app_id" content="' . $this->fbAppId . '" />' . "\n";

        return $ogMeta;
    }

}