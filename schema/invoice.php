<?php

namespace shgysk8zer0\PHPAPI\Schema;

/**
 * @see https://schema.org/Invoice
 */
class Invoice extends Thing
{
	const TYPE = 'Invoice';

	protected function _setData(\StdClass $data)
	{
		$this->_set('accountId', md5(time()));
		$this->_set('provider', new Person(1));
		$this->_set('customer', 'Mary Hanawalt');
		$this->_set('paymentStatus', 'due');
		$this->_set('totalPaymentDue', new MonetaryAmount(3.14));
	}
}
