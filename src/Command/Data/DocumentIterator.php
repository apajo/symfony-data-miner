<?php

namespace DataMiner\Command\Data;

use ATV\BaseBundle\Client\Entity\AbstractClient;
use ATV\BaseBundle\Entity\PInvoice;
use Core\BaseBundle\Entity\Media\Media;
use Core\BaseBundle\Media\MediaManagerTrait;
use Core\BaseBundle\Traits\ContainerAwareTrait;
use Sonata\MediaBundle\Provider\FileProvider;

class DocumentIterator extends \IteratorIterator
{
    use MediaManagerTrait;

    /**
     * @var AbstractClient|null
     */
    private ?AbstractClient $client = null;

    public function getClient(): ?AbstractClient
    {
        return $this->client;
    }

    public function setClient(?AbstractClient $client): void
    {
        $this->client = $client;
    }

    public function current(): ?DataItem
    {
        /** @var PInvoice $item */
        $item = parent::current();
        $result = new DataItem();

        if ($item->getFiles()->isEmpty()) {
            return null;
        }

        $filePath = $this->mediaManager->getMediaPath($item->getFiles()->first());

        if (is_file($filePath)) {
            $content = shell_exec('pdftotext -layout ' . $filePath . ' -');

            $result->setContent($content);
        }

        return $result;
    }
}
