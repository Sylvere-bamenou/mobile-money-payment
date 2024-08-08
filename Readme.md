=== Paiement Mobile Money ===

Contributors: Sylvere BAMENOU

Tags: woocommerce, mobile money, payment gateway, sckaler, payment

Requires at least: 5.0

Tested up to: 6.3

Requires PHP: 7.2

Stable tag: 1.0.0

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Une passerelle de paiement WooCommerce personnalisée pour les paiements Mobile Money utilisant l'API SCKALER.

== Description ==

Le plugin "Paiement Mobile Money" permet aux utilisateurs de WooCommerce d'accepter des paiements via Mobile Money en utilisant l'API SCKALER. Ce plugin prend en charge plusieurs pays en Afrique de l'Ouest, y compris le Bénin, la Côte d'Ivoire, le Burkina Faso, le Mali et le Sénégal. Il intègre des fournisseurs de Mobile Money tels que MTV, Moov, Celtis, Wave, Orange, et Free.

== Installation ==

1. Téléchargez le plugin et extrayez le contenu.
2. Uploadez le dossier `mobile-money-payment` dans le répertoire `wp-content/plugins/`.
3. Activez le plugin via le menu "Plugins" dans WordPress.
4. Allez dans "WooCommerce" -> "Réglages" -> "Paiements" et activez "Paiement Mobile Money".
5. Configurez les paramètres de la passerelle en fournissant votre token API SCKALER.

== FAQ ==

= Quels pays sont pris en charge par ce plugin ? =
Le plugin prend en charge les pays suivants : Bénin, Côte d'Ivoire, Burkina Faso, Mali, et Sénégal.

= Quels fournisseurs de Mobile Money sont pris en charge ? =
Les fournisseurs pris en charge incluent MTV, Moov, Celtis, Wave, Orange, et Free.

= Où puis-je obtenir le token API SCKALER ? =
Vous pouvez obtenir votre token API en vous inscrivant sur le site de SCKALER et en générant un token via leur tableau de bord.

le token test est : "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjUwNWM0ZDYwLWY4NmItNGU2Ny05ODI4LTkyNWU5NTE1ZjViYiIsImFjY291bnRfaWQiOiJjbHloOTd0eTYwMDAxaGZma3ppZTFkMGxwIiwiYWRtaW5fYWNjb3VudF9pZCI6ImE3ZGUwNzFjLTc4NjgtNDRlOSIsInJvbGUiOiJDTElFTlQiLCJpYXQiOjE3MjExNzQ4Njl9.sQ5-UyRbpjzyguCacLmrOtLW7zKr-21JsTABdgRJox8"

== Changelog ==

= 1.0.0 =
* Première version du plugin.

== Upgrade Notice ==

= 1.0.0 =
Première version du plugin. Installez et configurez votre token API SCKALER pour commencer à accepter les paiements via Mobile Money.

== Arbitrary section ==

Vous pouvez utiliser cette section pour des informations supplémentaires, des recommandations ou des liens vers des ressources.

== Screenshots ==

1. **Capture d'écran 1** - Page de configuration du plugin dans WooCommerce.
2. **Capture d'écran 2** - Sélection du mode de paiement Mobile Money lors du checkout.
3. **Capture d'écran 3** - Formulaire de paiement Mobile Money avec sélection du pays et du fournisseur.

