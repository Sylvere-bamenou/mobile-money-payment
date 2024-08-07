jQuery(document).ready(function($) {
	$('#mobile_money_country').change(function() {
		var country = $(this).val();
		var providers = mobileMoneyPaymentData.providers[country];
		var providerSelect = $('#mobile_money_provider');

		providerSelect.empty();
		providerSelect.append('<option value="">' + mobileMoneyPaymentData.selectProviderText + '</option>');

		if (providers) {
			$.each(providers, function(index, provider) {
				providerSelect.append('<option value="' + provider + '">' + provider + '</option>');
			});
		}
	});
});
