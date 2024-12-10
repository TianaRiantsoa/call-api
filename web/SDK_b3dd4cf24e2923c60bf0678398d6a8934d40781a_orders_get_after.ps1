# Développement Vaisonet
# Mars 2024, basé sur la version 2024-01 de l'API REST Shopify
# Ticket n° 29585
# Objectif : En fonction du couple location_id et méthode de paiement Shopify, remplacer par une autre méthode de paiement
#
# Tests : powershell -executionpolicy bypass ./SDK_XXXX_orders_get_after.ps1 -data cmd_test.json
#

param(
	[string]$data
)

try {
	# Lire le contenu du fichier en tant qu'objet JSON
	$jsonContent = Get-Content -Path $data
	
	# Doublons sensibles à la casse
	$keysToCheck = @("Timestamp", "Ack", "Version", "Build", "Token")

	# Itérer sur les clés et remplacer dans le contenu JSON
	foreach ($key in $keysToCheck) {
		# Utiliser une expression régulière pour ajouter "Renamed" uniquement aux clés concernées
		$pattern = '"' + $key + '":'
		$replacement = '"' + $key + 'Renamed":'
		$jsonContent = $jsonContent -replace $pattern, $replacement		
	}
	# Convertir le contenu JSON en objet PowerShell
	$api = ConvertFrom-Json $jsonContent

	if ($null -ne $api -and
		$null -ne $api.order -and
		$null -ne $api.order.fulfillments) {

		# Parcourir les fulfillments pour obtenir la location_id
		foreach ($fulfillment in $api.order.fulfillments) {
			switch ($fulfillment.location_id) {
				67519086758 { # POS Saint Val
					switch ($api.order.payment_gateway_names) {
						"Carte Bancaire" { $api.order.payment_gateway_names[0] = "CB005" } # OK
						"Chèque" { $api.order.payment_gateway_names[0] = "CH005" } # OK
						"Espèces" { $api.order.payment_gateway_names[0] = "ES005" } # OK
						"Avoir" { $api.order.payment_gateway_names[0] = "AV001" } # OK
						"gift_card" { $api.order.payment_gateway_names[0] = "SCHC" } # OK
					}
				}
				67039396006 { # POS Odéon
					switch ($api.order.payment_gateway_names) {
						"Carte Bancaire" { $api.order.payment_gateway_names[0] = "CB003" }
						"Chèque" { $api.order.payment_gateway_names[0] = "CH003" } # OK
						"Espèces" { $api.order.payment_gateway_names[0] = "ES003" } # OK
						"Avoir" { $api.order.payment_gateway_names[0] = "AV001" } # OK
						"gift_card" { $api.order.payment_gateway_names[0] = "SCHC" } # OK
						default { $object.order.payment_gateway_names }
					}
				}
				67519152294 { # POS Le Marais
					switch ($api.order.payment_gateway_names) {
						"Carte Bancaire" { $api.order.payment_gateway_names[0] = "CB004" } # OK
						"Chèque" { $api.order.payment_gateway_names[0] = "CH004" } # OK
						"Espèces" { $api.order.payment_gateway_names[0] = "ES004" } # OK
						"Avoir" { $api.order.payment_gateway_names[0] = "AV001" } # OK
						"gift_card" { $api.order.payment_gateway_names[0] = "SCHC" } # OK
						default { $object.order.payment_gateway_names }
					}
				}
			}
		}
	}
	# Convertir l'objet JSON modifié en une chaîne JSON
	$jsonString = $api | ConvertTo-Json -Depth 100

	# Renvoyer le contenu JSON modifié
	Write-Output $jsonString
}
catch {
	Write-Error "Erreur : $_"
}
