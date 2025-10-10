<?php

namespace app\services;

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;

/**
 * Service class pour gérer les produits PrestaShop
 */
class PrestaShopProductService
{
    private $webService;
    private $url;
    private $apiKey;
    private $languageId;

    public function __construct($url, $apiKey)
    {
        $this->url = $this->normalizeUrl($url);
        $this->apiKey = Html::encode($apiKey);
        $this->webService = new PrestaShopWebservice($this->url, $this->apiKey, false);
    }

    /**
     * Normalise l'URL pour forcer HTTP/HTTPS selon le contexte
     */
    private function normalizeUrl($url)
    {
        $url = Html::encode($url);

        if (strpos($url, 'localhost') !== false) {
            return "http://" . $url;
        } else {
            $headers = @get_headers("http://" . $url);
            return ($headers && strpos($headers[0], '200') !== false)
                ? "https://" . $url
                : "https://" . $url;
        }
    }

    /**
     * Récupère l'ID de la langue basé sur le code ISO
     */
    public function getLanguageId($languageIso)
    {
        if ($this->languageId) {
            return $this->languageId;
        }

        try {
            $languageOpt = [
                'resource' => 'languages',
                'filter[iso_code]' => $languageIso,
                'display' => 'full',
            ];

            $languageXml = $this->webService->get($languageOpt);
            $languages = $languageXml->languages->children();

            foreach ($languages as $language) {
                $this->languageId = (int)$language->id;
                return $this->languageId;
            }

            throw new PrestaShopWebserviceException('Langue introuvable dans la boutique.');
        } catch (\Exception $e) {
            throw new PrestaShopWebserviceException('Erreur lors de la récupération de la langue : ' . $e->getMessage());
        }
    }

    /**
     * Récupère les produits simples par référence
     */
    public function getSimpleProducts($reference, $languageIso)
    {
        $languageId = $this->getLanguageId($languageIso);

        $opt = [
            'resource' => 'products',
            'filter[reference]' => $reference,
            'language' => $languageId,
            'display' => 'full',
        ];

        try {
            $xml = $this->webService->get($opt);
            $products = $xml->products->children();

            if (count($products) === 0) {
                throw new \Exception('Aucun produit trouvé avec cette référence.');
            }

            $productList = [];
            foreach ($products as $product) {
                $this->validateSimpleProduct($product);

                $productList[] = [
                    'id' => (int)$product->id,
                    'name' => (string)$product->name->language,
                    'reference' => (string)$product->reference,
                    'price' => (float)$product->price,
                    'description' => (string)$product->description->language,
                    'date_add' => (string)$product->date_add,
                    'date_upd' => (string)$product->date_upd,
                ];
            }

            return $productList;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des produits : ' . $e->getMessage());
        }
    }

    /**
     * Valide qu'un produit est de type simple
     */
    private function validateSimpleProduct($product)
    {
        $productType = (string)$product->type;
        $productTypeCheck = (string)$product->product_type;

        if ($productType !== 'simple' && $productTypeCheck === 'combinations' && $productType === 'virtual') {
            throw new \Exception('Le produit n\'est pas de type "simple". Veuillez vérifier et choisir "Produit déclinaison" dans le formulaire.');
        }
    }

    /**
     * Récupère un produit parent avec ses déclinaisons
     */
    public function getParentProductWithVariations($reference, $languageIso)
    {
        $languageId = $this->getLanguageId($languageIso);

        $opt = [
            'resource' => 'products',
            'language' => $languageId,
            'filter[reference]' => $reference,
            'display' => 'full',
        ];

        try {
            $xml = $this->webService->get($opt);
            $products = $xml->products->children();

            if (count($products) === 0) {
                throw new \Exception('Aucun produit trouvé avec cette référence.');
            }

            $productData = null;
            $combinationList = [];

            foreach ($products as $product) {
                if (!$this->isVariationProduct($product)) {
                    throw new \Exception('Le produit n\'est pas trouvé en tant que produit parent. Essayer avec Produit Déclinaison > Enfant ou Produit Simple');
                }

                $productData = $this->buildProductData($product);
                $combinationList = $this->getProductCombinations((int)$product->id);
            }

            return [
                'product' => [$productData],
                'combinations' => $combinationList
            ];
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération du produit parent : ' . $e->getMessage());
        }
    }

    /**
     * Vérifie si un produit a des variations
     */
    private function isVariationProduct($product)
    {
        $productType = (string)$product->type;
        $productTypeCheck = (string)$product->product_type;
        return $productType !== 'simple' || $productTypeCheck === 'combinations';
    }

    /**
     * Construit les données d'un produit
     */
    private function buildProductData($product)
    {
        $productData = [
            'id' => (int)$product->id,
            'name' => (string)$product->name->language,
            'reference' => (string)$product->reference,
            'active' => (int)$product->active,
            'price' => (float)$product->price,
            'date_add' => (string)$product->date_add,
            'date_upd' => (string)$product->date_upd,
        ];

        // Récupération du stock
        $productData['quantity'] = $this->getProductStock((int)$product->id);

        return $productData;
    }

    /**
     * Récupère le stock d'un produit
     */
    private function getProductStock($productId, $attributeId = 0)
    {
        try {
            $stockOpt = [
                'resource' => 'stock_availables',
                'filter[id_product]' => $productId,
                'filter[id_product_attribute]' => $attributeId,
                'display' => 'full',
            ];

            $stock = $this->webService->get($stockOpt);
            $stockXML = $stock->stock_availables->children();

            foreach ($stockXML as $stocks) {
                if (isset($stocks->quantity)) {
                    return (string)$stocks->quantity;
                }
            }
        } catch (\Exception $e) {
            return 'Erreur lors de la récupération';
        }

        return '0';
    }

    /**
     * Récupère les combinaisons d'un produit
     */
    private function getProductCombinations($productId)
    {
        try {
            $combOpt = [
                'resource' => 'combinations',
                'filter[id_product]' => $productId,
                'display' => '[id,reference,price]',
            ];

            $comb = $this->webService->get($combOpt);
            $combXML = $comb->combinations->children();

            $combinationList = [];
            foreach ($combXML as $combs) {
                $combinationList[] = [
                    'id' => (int)$combs->id,
                    'reference' => (string)$combs->reference,
                    'price' => (float)$combs->price,
                    'quantity' => $this->getProductStock($productId, (int)$combs->id)
                ];
            }

            return $combinationList;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupère les détails d'une combinaison enfant
     */
    public function getChildCombination($reference, $languageIso)
    {
        $languageId = $this->getLanguageId($languageIso);

        $opt = [
            'resource' => 'combinations',
            'language' => $languageId,
            'filter[reference]' => $reference,
            'display' => 'full',
        ];

        try {
            $xml = $this->webService->get($opt);
            $combinations = $xml->combinations->children();

            if (count($combinations) === 0) {
                throw new \Exception('Aucun produit enfant trouvé avec cette référence.');
            }

            $combinationList = [];
            $tarifList = [];

            foreach ($combinations as $combination) {
                $combinationData = $this->buildCombinationData($combination, $languageId);
                $combinationList[] = $combinationData;

                // Récupération des tarifs spécifiques
                $tarifs = $this->getSpecificPrices((int)$combination->id, $languageId);
                $tarifList = array_merge($tarifList, $tarifs);
            }

            // Calculer les différences de prix
            $this->calculatePriceDifferences($tarifList, $combinationList);

            return [
                'combinations' => $combinationList,
                'specific_prices' => $tarifList
            ];
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération de la combinaison : ' . $e->getMessage());
        }
    }

    /**
     * Construit les données d'une combinaison
     */
    private function buildCombinationData($combination, $languageId)
    {
        $combinationData = [
            'id' => (int)$combination->id,
            'reference' => (string)$combination->reference,
            'price' => (float)$combination->price,
            'parent_reference' => $this->getParentReference((int)$combination->id_product, $languageId),
            'name' => $this->getParentName((int)$combination->id_product, $languageId),
            'quantity' => $this->getProductStock((int)$combination->id_product, (int)$combination->id),
            'option_values' => $this->getOptionValues($combination, $languageId)
        ];

        return $combinationData;
    }

    /**
     * Récupère la référence du produit parent
     */
    private function getParentReference($productId, $languageId)
    {
        try {
            $parentOpt = [
                'resource' => 'products',
                'id' => $productId,
                'language' => $languageId,
            ];

            $parent = $this->webService->get($parentOpt);
            return (string)$parent->product->reference;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Récupère le nom du produit parent
     */
    private function getParentName($productId, $languageId)
    {
        try {
            $parentOpt = [
                'resource' => 'products',
                'id' => $productId,
                'language' => $languageId,
            ];

            $parent = $this->webService->get($parentOpt);
            return (string)$parent->product->name->language;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Récupère les valeurs des options pour une combinaison
     */
    private function getOptionValues($combination, $languageId)
    {
        $optionValues = [];

        if (!isset($combination->associations->product_option_values->product_option_value)) {
            return '';
        }

        foreach ($combination->associations->product_option_values->product_option_value as $optionValue) {
            try {
                $optionValueOpt = [
                    'resource' => 'product_option_values',
                    'id' => $optionValue->id,
                    'language' => $languageId,
                ];

                $optionValueResponse = $this->webService->get($optionValueOpt);
                $optionValueXML = $optionValueResponse->product_option_value;

                $value = (string)$optionValueXML->name->language;
                $id_axe = (int)$optionValueXML->id_attribute_group;

                $axeOpt = [
                    'resource' => 'product_options',
                    'id' => $id_axe,
                    'language' => $languageId,
                ];

                $axeResponse = $this->webService->get($axeOpt);
                $axeXML = $axeResponse->product_option;
                $axeName = (string)$axeXML->name->language;

                $optionValues[] = "{$axeName} = {$value}";
            } catch (\Exception $e) {
                continue;
            }
        }

        return implode('<br>', $optionValues);
    }

    /**
     * Récupère les prix spécifiques pour une combinaison
     */
    private function getSpecificPrices($combinationId, $languageId)
    {
        try {
            $tarifOpt = [
                'resource' => 'specific_prices',
                'filter[id_product_attribute]' => $combinationId,
                'display' => 'full',
            ];

            $tarif = $this->webService->get($tarifOpt);
            $tarifXML = $tarif->specific_prices->children();

            $tarifList = [];
            foreach ($tarifXML as $tarifItem) {
                $groupName = $this->getGroupName((int)$tarifItem->id_group, $languageId);

                $tarifList[] = [
                    'id' => (int)$tarifItem->id,
                    'id_product' => (int)$tarifItem->id_product,
                    'id_product_attribute' => (int)$tarifItem->id_product_attribute,
                    'group_id' => (int)$tarifItem->id_group,
                    'id_group' => (int)$tarifItem->id_group . " (" . $groupName . ")",
                    'id_customer' => (int)$tarifItem->id_customer,
                    'price' => (float)$tarifItem->price,
                    'from' => (string)$tarifItem->from,
                    'to' => (string)$tarifItem->to,
                ];
            }

            return $tarifList;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupère le nom d'un groupe
     */
    private function getGroupName($groupId, $languageId)
    {
        try {
            $groupOpt = [
                'resource' => 'groups',
                'id' => $groupId,
                'language' => $languageId,
            ];

            $group = $this->webService->get($groupOpt);
            return (string)$group->group->name->language;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Calcule les différences de prix pour les tarifs spécifiques
     */
    private function calculatePriceDifferences(&$tarifList, $combinationList)
    {
        $combinationPrices = [];
        foreach ($combinationList as $combination) {
            $combinationPrices[$combination['id']] = $combination['price'];
        }

        foreach ($tarifList as &$tarif) {
            $combinationPrice = $combinationPrices[$tarif['id_product_attribute']] ?? null;

            if ($combinationPrice !== null) {
                $differenceAmount = $tarif['price'] - $combinationPrice;
                $differencePercentage = $combinationPrice != 0 ? ($differenceAmount / $combinationPrice) * 100 : 0;

                $tarif['difference_amount'] = $differenceAmount;
                $tarif['difference_percentage'] = $differencePercentage;
            } else {
                $tarif['difference_amount'] = 'N/A';
                $tarif['difference_percentage'] = 'N/A';
            }
        }
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }
}
