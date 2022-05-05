<?php

namespace DataMiner\Command\Data;

use PhpDataMiner\Helpers\OptionsBuilderTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Iterator;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var array
     */
    protected array $options = [];

    public function getOption(string $name)
    {
        return $this->options[$name];
    }

    public function buildOptions (array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    abstract public function getIterator (array $options = []): Iterator;

    abstract public function configureOptions(OptionsResolver $resolver);
}
