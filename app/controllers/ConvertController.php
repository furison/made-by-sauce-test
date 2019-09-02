<?php

class ConvertController extends BaseController {

    /*
    * Converts an amount from one currency to another
	*/

	public function getCurrencyConversion()
	{
        $from = Input::get('from');
        $to = Input::get('to');
        $amount = Input::get('amount', 1);

        //get conversion rates for the base currency
        $rates = $this->getCurrencyRates($from);

        //get rate converting to and multiply amount by the rate
        $convertRate = $rates[$to];
        $converted = $amount*$convertRate;

		return Response::json(array('from'=> $from, 'to' => $to, 'converted_amount' => $converted));
	}

    private function getCurrencyRates($from)
    {
        //TODO: possibly cache this
        $from = strtolower($from);
        $rateUrl = 'http://www.floatrates.com/daily/'. $from .'.xml';

        //get rate xml data
        $ch = curl_init($rateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xmlData = curl_exec($ch);

        //parse XML data
        $rateData = simplexml_load_string($xmlData);

        //loop through and create array of currencies
        $currencyConversion = array();
        foreach ($xmlData->channel->item as $item)
        {
            $currencyConversion[$item->targetCurrency] = $item->exchangeRate;
        }

        return $currencyConversion;
    }
}