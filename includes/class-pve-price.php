<?php
/**
 * PVE_Price
 *
 * Handles ETH/USD price retrieval and fallback. This means fetching the current price from the Kraken public ticker API, 
 * caching it in a transient, maintaining the 50-entry rolling average in pve_eth_price_history, 
 * incrementing the failure counter pve_eth_fetch_failures when a fetch fails, 
 * and returning a fallback average when the live fetch is unavailable. It does not convert prices to ETH amounts, 
 * it does not write to order meta, and it does not know anything about orders or the checkout process. 
 * It returns a USD price and nothing more.
 *
 * Hooks registered by this class:
 *
 * Options read:
 *
 * Order meta read:
 *
 * @package Payments_Via_Ethereum
 * @since 1.420.69
 */

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

