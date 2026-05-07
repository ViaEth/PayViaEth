=== Payments Via Ethereum ===
Contributors: viaeth
Donate link: https://Pay.ViaEth.io
Tags: ethereum, woocommerce, blockchain, payments
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.420.69
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Direct ETH payment gateway for WooCommerce. No middlemen, no transaction fees, no PII collection.

== Description ==

Payments Via Ethereum is a direct-payment gateway for WooCommerce. Customers pay merchants directly in ETH, with no intermediaries and no transaction fees beyond standard Ethereum network gas.

[Substantive description, screenshots, and detailed feature list to be added in a later release per roadmap P04.T7.]

**Removed dependencies (from earlier alpha builds):**

* phpqrcode (QR generation moved to client-side JS)
* Etherscan API (replaced with manual verification workflow)
* CurrencyConvertor library (replaced with bcmath conversion)
* Custom database table (now uses WP options API and order meta)

== Installation ==

1. Upload the `pay-via-eth` directory to `/wp-content/plugins/`
2. Activate the plugin via the 'Plugins' menu in WordPress
3. Configure your merchant ETH addresses under WooCommerce > Settings > Payments

== Frequently Asked Questions ==

= Does this plugin work for Bitcoin payments? =

No. Payments Via Ethereum is specific to the Ethereum network. Bitcoin payments require a separate plugin.

= Can I use any other Ethereum tokens with this plugin? =

Not currently. The plugin only supports native ETH payments. Support for ERC-20 and other tokens is planned for a future release.

= Can I use this plugin on an Ethereum testnet? =

Yes. The plugin is network-agnostic and works with Ethereum testnets when configured with appropriate merchant addresses and a testnet block explorer URL.

= Does this plugin collect personally identifiable information? =

No. The plugin does not collect or store any customer PII.

= Are there any transaction fees beyond the Ethereum network gas? =

No. Payments go directly from customer wallet to merchant wallet. The plugin charges nothing.

= Where can I get help or support? =

Additional information about the plugin is available on the project website at https://Pay.ViaEth.io. For community support, visit the forum at https://viaeth.io/community/forum/support/payviaeth/.

== Screenshots ==

[Screenshots to be added in a later release per roadmap P04.T7.]

== Changelog ==

= 1.420.69 =
* Initial pre-release.

== Upgrade Notice ==

= 1.420.69 =
Initial pre-release.