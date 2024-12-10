# Développement Vaisonet
# Novembre 2023
# Devis n°20230420-091451095
# Objectif :Si un produit d'une commande a, dans l'API
# 	REST de WooCommerce, des metadatas
# 	indiquant une composition en kit incluant le
# 	code article EBP, alors ce produit sera
# 	supprimé de la commande et remplacé par
# 	les composants du kit lors de l'import de la
# 	commande dans EBP.
# 	Les composants doivent exister dans EBP.
#
# Tests : powershell -executionpolicy bypass ./SDK_6c7e95028cc46aa26ad7402822d209685b6f4be2_orders_get_after.ps1 -data cmd_test.json
#
# En cas d'erreur de ce type : Erreur : Impossible d’appeler la méthode. L’appel aux méthodes n’est pris en charge que sur les types principaux dans ce mode de langage.
# C'est que les droits sur Powershell sont insuffisants. Voir KT dans ce cas pour donner les instructions au sysadmin.

param(
	[string]$data
)

# Fonction pour récupérer le SKU WooCommerce à partir de l'ID d'un produit, selon les directives du client sur le projet Asana
function Get-WooCommerceProductById($productId) {
	
	# Paramétrage de la connexion à l'API REST
	$baseUrl = "https://www.tissage-moutet.com/wp-json/wc/v3"
	#encodage en base64 au format consumerKey:consumerSecret de l'API REST WooCommerce
	$encodedAuthInfo = "Y2tfN2Q5ZmM1YzA2MGI1OGUxYTY3YzhhZjQzODI3YTAxNWJiYTFhMGU2Nzpjc19mNmRhMjY5OGI2YTY5YmIxNmM0Zjk3ZTNiZTBmOTdmMjBkYWQyZjBh"
	# Fin du paramétrage

	$url = "$baseUrl/products/$productId"
	$headers = @{
		Authorization = "Basic $encodedAuthInfo"
	}

	try {
		$response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers
		return $response.sku
	}
	catch {
		Write-Error "Echec de récupération de l'UGS WooCommerce par le fichier SDK : $_"
	}
}

try {
	# Lire le contenu du fichier en tant qu'objet JSON
	$api = Get-Content -Path $data -Encoding UTF8 | ConvertFrom-Json
		
	if ($api -isnot [System.Array]) {
		# Vérification et traitement des produits dans la commande
		foreach ($item in $api.line_items) {
			if ($item.meta_data.Count -gt 0) {
			
				$is_kit = 0
				for ($i = 0; $i -lt $item.meta_data.Count; $i++) {
				
					$meta = $item.meta_data[$i]		

					#Calcul du nombre de composant du kit
					$nb_composant = $item.meta_data.Count / 2;
			
					#On filtre pour ne conserver que les composants dans les metadatas
					if ($meta.key -match '^Mod.*le$') {
						#Pour être insensible aux problèmes d'encodage et améliorer la fiabilité
					
						# Extraction des informations des composants
						$sku = Get-WooCommerceProductById $item.meta_data[$i + 1].value
						$name = $meta.value
						$quantity = $item.quantity
						$subtotal = $item.subtotal / $nb_composant
						$total = $item.total / $nb_composant
						$tax_class = $item.tax_class
						$total_tax = $item.total_tax / $nb_composant

						# Création et ajout du nouveau produit
						$newProduct = New-Object PSObject -Property @{
							sku       = $sku
							name      = $name
							quantity  = $quantity
							subtotal  = $subtotal
							total     = $total
							tax_class = $tax_class
							total_tax = $total_tax
						}
						$api.line_items += $newProduct
						$is_kit = 1					
					}
				}

				# Suppression du produit de la commande
				if ($is_kit -gt 0) {
					$api.line_items = $api.line_items | Where-Object { $_.id -ne $item.id }
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
