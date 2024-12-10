<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;

function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Vaisonet e-connecteur");
    $get = curl_exec($ch);
    curl_close($ch);

    return $get;
}

//Récupérer la langue française
function getLang($url, $api)
{
	try {
		$webService = new PrestaShopWebservice($url, $api, false);
		$opt = [
			'resource' => 'languages',
			'filter[iso_code]' => 'fr'
		];

		$xml = $webService->get($opt);

		$resources = $xml->children()->children()->language->attributes()['id'];
		return $resources;
	} catch (PrestaShopWebserviceException $e) {

		$trace = $e->getTrace();
		if ($trace[0]['args'][0] == 404)
			echo 'Bad ID';
		else if ($trace[0]['args'][0] == 401)
			echo 'Bad auth key';
		else
			echo 'Other error<br />' . $e->getMessage();
	}
}

class Product
{
	// Récupérer l'ID du produit et vérifier s'il y a des doublons
	static function get($url, $api, $ref)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);

			$opt = [
				'resource' => 'products',
				'filter[reference]' => $ref
			];

			$xml = $webService->get($opt);

			$id = [];
			foreach ($xml->children()->children()->product as $product) {
				$id[] = $product->attributes()['id'];
			}

			if (count($id) > 0) {
				for ($i = 0; $i <= count($id) - 1; $i++) {
					$product = self::getProduct($url, $api, $id[$i], getLang($url, $api));

					$results[] = $product;
				}
				return $results;
			} else {
				return [];
			}
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404) {
				echo 'Mauvais ID';
			} else if ($trace[0]['args'][0] == 401) {
				echo 'Mauvaise clé d\'authentification';
			} else {
				echo 'Autre erreur<br />' . $e->getMessage();
			}
		}
	}

	static function getProduct($url, $api, $id, $lang)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);
			$opt = [
				'resource' => 'products',
				'id' => $id,
				'languages' => $lang,
			];

			$xml = $webService->get($opt);

			$fields = $xml->product[0]->children();

			$result = array(
				'id' => Html::encode($fields->id),
				'reference' => Html::encode($fields->reference),
				'name' => Html::encode($fields->name->language),
				'active' => Html::encode($fields->active),
				'price' => sprintf('%.2f', $fields->price),
				'pttc' => sprintf('%.2f', $fields->price + ($fields->price * 0.2)),
				'ecotax' => sprintf('%.2f', $fields->ecotax),
				'stock_available' => $fields->associations->stock_availables,
				'date_add' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_add))),
				'date_upd' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_upd))),
			);

			return $result;
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404)
				echo 'Mauvais ID';
			else if ($trace[0]['args'][0] == 401)
				echo 'Mauvaise clé d\'authentification';
			else
				echo 'Autre erreur<br />' . $e->getMessage();
		}
	}

	static function StockAvailable($url, $api, $id)
	{

		try {
			$webService = new PrestaShopWebservice($url, $api, false);
			$opt = [
				'resource' => 'stock_availables',
				'id' => $id,
			];

			$xml = $webService->get($opt);

			$fields = $xml->stock_available->children();

			$result = array(
				'id' => $fields->id,
				'id_product' => $fields->id_product,
				'id_product_attribute' => $fields->id_product_attribute,
				'id_shop' => $fields->id_shop,
				'id_shop_group' => $fields->id_shop_group,
				'quantity' => $fields->quantity,
			);

			return $result;
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404)
				echo 'Bad ID';
			else if ($trace[0]['args'][0] == 401)
				echo 'Bad auth key';
			else
				echo 'Other error<br />' . $e->getMessage();
		}
	}

	static function isStock()
	{
	}
}

class Order
{
	static function get($url, $api, $ref)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);

			$opt = [
				'resource' => 'orders',
				'filter[reference]' => $ref
			];

			$xml = $webService->get($opt);

			$id = [];
			foreach ($xml->children()->children()->order as $order) {
				$id[] = $order->attributes()['id'];
			}

			if (count($id) > 0) {
				for ($i = 0; $i <= count($id) - 1; $i++) {
					$order = self::getOrder($url, $api, $id[$i], getLang($url, $api));

					$results[] = $order;
				}
				return $results;
			} else {
				return [];
			}
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404) {
				echo 'Mauvais ID';
			} else if ($trace[0]['args'][0] == 401) {
				echo 'Mauvaise clé d\'authentification';
			} else {
				echo 'Autre erreur<br />' . $e->getMessage();
			}
		}
	}

	static function getOrder($url, $api, $id, $lang)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);
			$opt = [
				'resource' => 'orders',
				'id' => $id,
				'languages' => $lang,
			];

			$xml = $webService->get($opt);

			$fields = $xml->product[0]->children();

			$result = array(
				'id' => Html::encode($fields->id),
				'id_address_delivery' => Html::encode($fields->id_address_delivery),
				'id_address_invoice ' => Html::encode($fields->id_address_delivery),
				'active' => Html::encode($fields->active),
				'price' => sprintf('%.2f', $fields->price),
				'pttc' => sprintf('%.2f', $fields->price + ($fields->price * 0.2)),
				'ecotax' => sprintf('%.2f', $fields->ecotax),
				'stock_available' => $fields->associations->stock_availables,
				'date_add' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_add))),
				'date_upd' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_upd))),
			);

			return $result;
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404)
				echo 'Mauvais ID';
			else if ($trace[0]['args'][0] == 401)
				echo 'Mauvaise clé d\'authentification';
			else
				echo 'Autre erreur<br />' . $e->getMessage();
		}
	}
}

class Customer
{
	static function get($url, $api, $ref)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);

			$opt = [
				'resource' => 'customers',
				'filter[email]' => $ref
			];

			$xml = $webService->get($opt);

			$id = [];
			foreach ($xml->children()->children()->customer as $customer) {
				$id[] = $customer->attributes()['id'];
			}

			if (count($id) > 0) {
				for ($i = 0; $i <= count($id) - 1; $i++) {
					$res = self::getCustomer($url, $api, $id[$i], getLang($url, $api));

					$results[] = $res;
				}
				return $results;
			} else {
				return [];
			}
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404) {
				echo 'Mauvais ID';
			} else if ($trace[0]['args'][0] == 401) {
				echo 'Mauvaise clé d\'authentification';
			} else {
				echo 'Autre erreur<br />' . $e->getMessage();
			}
		}
	}

	static function getCustomer($url, $api, $id, $lang)
	{
		try {
			$webService = new PrestaShopWebservice($url, $api, false);
			$opt = [
				'resource' => 'customers',
				'id' => $id,
				'languages' => $lang,
			];

			$xml = $webService->get($opt);

			$fields = $xml->customer[0]->children();

			$result = array(
				'id' => Html::encode($fields->id),
				'id_default_group' => Html::encode($fields->id_default_group),
				'lastname' => Html::encode($fields->lastname),
				'firstname' => Html::encode($fields->firstname),
				'email' => Html::encode($fields->email),
				'company' => Html::encode($fields->company),
				'siret' => Html::encode($fields->siret),
				'active' => Html::encode($fields->active),
				'date_add' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_add))),
				'date_upd' => Html::encode(date("d/m/Y H:i:s", strtotime($fields->date_upd))),
			);

			return $result;
		} catch (PrestaShopWebserviceException $e) {
			$trace = $e->getTrace();
			if ($trace[0]['args'][0] == 404)
				echo 'Mauvais ID';
			else if ($trace[0]['args'][0] == 401)
				echo 'Mauvaise clé d\'authentification';
			else
				echo 'Autre erreur<br />' . $e->getMessage();
		}
	}
}
