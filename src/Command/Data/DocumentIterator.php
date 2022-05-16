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
        $result->setDocument($item);

        if ($item->getFiles()->isEmpty()) {
            return null;
        }


        $basePath = '/var/www/apajo.ee/hosts/atv.apajo.ee/uploads';
        $filePath = realpath($this->mediaManager->getMediaPath($item->getFiles()->first()));
        //$filePath = str_replace($basePath, '', $filePath);

        if (is_file($filePath)) {
            $content = shell_exec('pdftotext -layout ' . $filePath . ' -');

            $result->setContent($content);
        }

        return $result;
    }
}
