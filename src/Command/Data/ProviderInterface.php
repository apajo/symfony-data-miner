<?php

namespace DataMiner\Command\Data;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Iterator;

interface ProviderInterface
{
    public function getIterator( array $options = []): Iterator;

    public function configureOptions(OptionsResolver $resolver);

    public function getOption(string $name);

    public function buildOptions(array $options = []);
}
