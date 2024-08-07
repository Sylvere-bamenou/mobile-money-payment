<fieldset>
	<p class="form-row form-row-wide">
		<label for="mobile_money_country"><?php _e( 'Pays', 'mobile-money-payment' ); ?></label>
		<select id="mobile_money_country" name="mobile_money_country">
			<option value=""><?php _e( 'Sélectionnez un pays', 'mobile-money-payment' ); ?></option>
			<?php 
			$countries = Mobile_Money_API::get_supported_countries();
			if (is_array($countries) && !empty($countries)) {
				foreach ($countries as $country_code => $country_name) : ?>
					<option value="<?php echo esc_attr( $country_code ); ?>"><?php echo esc_html( $country_name ); ?></option>
				<?php endforeach; 
			} else {
				echo '<option value="">'.__('Aucun pays disponible', 'mobile-money-payment').'</option>';
			}
			?>
		</select>
	</p>
	<p class="form-row form-row-wide">
		<label for="mobile_money_provider"><?php _e( 'Réseau mobile', 'mobile-money-payment' ); ?></label>
		<select id="mobile_money_provider" name="mobile_money_provider">
			<option value=""><?php _e( 'Sélectionnez un fournisseur', 'mobile-money-payment' ); ?></option>
		</select>
	</p>
	<p class="form-row form-row-wide">
		<label for="mobile_money_phone"><?php _e( 'Numéro de téléphone', 'mobile-money-payment' ); ?></label>
		<input id="mobile_money_phone" name="mobile_money_phone" type="text" placeholder="<?php _e( 'ex. 123456789', 'mobile-money-payment' ); ?>" />
	</p>
</fieldset>
