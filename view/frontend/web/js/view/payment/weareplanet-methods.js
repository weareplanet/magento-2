/**
 WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com).
 *
 * @package WeArePlanet_Payment
 * @author Planet Merchant Services Ltd (https://www.weareplanet.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)

 */
define([
	'jquery',
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
], function(
	$,
	Component,
	rendererList
) {
	'use strict';
	
	// Loads the WeArePlanet Javascript File
	if (window.checkoutConfig.weareplanet.javascriptUrl) {
		$.getScript(window.checkoutConfig.weareplanet.javascriptUrl);
	}
	
	// Loads the WeArePlanet Lightbox File
	if (window.checkoutConfig.weareplanet.lightboxUrl) {
		$.getScript(window.checkoutConfig.weareplanet.lightboxUrl);
	}
	
	// Registers the WeArePlanet payment methods
	$.each(window.checkoutConfig.payment, function(code){
		if (code.indexOf('weareplanet_payment_') === 0) {
			rendererList.push({
			    type: code,
			    component: 'WeArePlanet_Payment/js/view/payment/method-renderer/weareplanet-method'
			});
		}
	});
	
	return Component.extend({});
});