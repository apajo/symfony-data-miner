<?php

namespace DataMiner\Command\Data;

use ATV\BaseBundle\Client\Entity\AbstractClient;

class DataItem
{
    /**
     * @var AbstractClient|null
     */
    protected ?AbstractClient $client = null;

    protected $document = null;
    
    /**
     * @var string|null
     */
    private ?string $content = null;

    function __construct () {

    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getClient(): ?AbstractClient
    {
        return $this->client;
    }

    public function setClient(?AbstractClient $client): void
    {
        $this->client = $client;
    }


    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument($document): void
    {
        $this->document = $document;
    }
}
