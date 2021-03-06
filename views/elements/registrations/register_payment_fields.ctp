<?php
if (!isset($allowed)) {
	return;
}

echo $this->element('messages');

if ($allowed) {
	$cost = $price['cost'] + $price['tax1'] + $price['tax2'];
	$options = array();
	if ($price['allow_deposit']) {
		if ($price['fixed_deposit']) {
			$options['Deposit'] = sprintf(__('Deposit ($%s)', true), $price['minimum_deposit']);
			if ($price['deposit_only']) {
				// The only option is a fixed-price deposit, so there will be no input fields at all,
				// but we want to let them know that the amount will be different than they might expect
				echo $this->Html->para('warning-message', sprintf(__('This option requires a $%s deposit, with the balance to be paid off-line.', true), $price['minimum_deposit']));
			}
		} else {
			$options['Deposit'] = sprintf(__('Deposit (minimum $%s)', true), $price['minimum_deposit']);
		}
	}
	if (!$price['deposit_only']) {
		$options['Full'] = sprintf(__('Full ($%s)', true), $cost);
	}

	if (!empty($registration)) {
		$default_type = ($registration['deposit_amount'] > 0 ? 'Deposit' : 'Full');
	} else {
		$default_type = 'Full';
	}
	if ($default_type == 'Full') {
		$default_deposit = $price['minimum_deposit'];
	} else {
		$default_deposit = $registration['deposit_amount'];
	}
	echo $this->ZuluruForm->input('Registration.payment_type', array(
			'options' => $options,
			'hide_single' => true,
			'default' => $default_type,
	));
	if ($price['allow_deposit'] && !$price['fixed_deposit']) {
		echo $this->Form->input('Registration.deposit_amount', array(
				'default' => $default_deposit,
		));
	}

	if (empty($for_edit) && $price['allow_reservations']) {
		echo $this->Html->para('warning-message', sprintf(__('After clicking "Submit", your registration will be reserved for you for %s. During this time, your spot is guaranteed. After this time, if you have not yet paid, it will revert to "unpaid" status, meaning that someone else can take it.', true), Price::duration($price['reservation_duration'])));
	}

	echo $this->Html->scriptBlock("
function typeChanged() {
	if (jQuery('#RegistrationPaymentType').val() == 'Full') {
		jQuery('#RegistrationDepositAmount').prop('disabled', true);
		jQuery('#RegistrationDepositAmount').closest('div').css('display', 'none');
	} else if (jQuery('#RegistrationPaymentType').val() == 'Deposit') {
		jQuery('#RegistrationDepositAmount').prop('disabled', false);
		jQuery('#RegistrationDepositAmount').closest('div').css('display', '');
	}
}
	");
	$this->Js->buffer('
jQuery("#RegistrationPaymentType").on("change", function(){typeChanged();});
typeChanged();
	');
}
?>
