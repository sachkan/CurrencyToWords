# CurrencyToWords
get currency words IDR, USD etc

note to add any language:
  - add & set the method (currency, punctuation, numeral & digit) for any language
  - add special condition to digit method if needed
  - follow the role of the previous two points


Example:

	$currency = new CurrencyToWords('id');
	$currency->amount = "Rp11108111331.8";
	echo json_encode($currency->getWords());

Return:

    {
        "amount" : 11108111331.8,
        "code" : "IDR",
        "type" : "string",
        "words" : "Sebelas Milyar Seratus Delapan Juta Seratus Sebelas Ribu Tiga Ratus Tiga Puluh Satu Titik Delapan Puluh IDR",
    }
