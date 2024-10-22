<?php

namespace App\Services;

class SeoService
{
    protected $title = 'Default Title';
    protected $description = 'Default description';
    protected $canonical = null;
    protected $ogImage = null;

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setCanonical($url)
    {
        $this->canonical = $url;
        return $this;
    }

    public function setOgImage($url)
    {
        $this->ogImage = $url;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCanonical()
    {
        return $this->canonical;
    }

    public function getOgImage()
    {
        return $this->ogImage;
    }
}
