<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Database;

class PriceModifier extends Modifier
{
    private $isNetto = false;
    private $showNetto = false;
    private $defaultVAT = 16;

    /**
     * PriceModifier constructor.
     * @param bool $isNetto
     * @param bool $showNetto
     * @param int $defaultVAT
     */
    public function __construct($isNetto, $showNetto, $defaultVAT)
    {
        $this->isNetto = $isNetto;
        $this->showNetto = $showNetto;
        $this->defaultVAT = $defaultVAT;
    }

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param Database $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, Database $database)
    {
        if ($this->isNetto && !$this->showNetto) {
            $prices = array_filter(
                array_keys(get_object_vars($product)),
                function ($key) {
                    return strpos(strtolower($key), 'price') !== false;
                }
            );
            $vat = 1 + (isset($product->OXVAT) ? $product->OXVAT : $this->defaultVAT)/100.0;
            foreach ($prices as $price) {
                $product->$price *= $vat;
            }
        }
        return $product;
    }
}
