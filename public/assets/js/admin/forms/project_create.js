currencyField = crud.field('currency')
orgCurrencyField = crud.field('org_currency')
startDateField = crud.field('start_date')
exchangeRateField = crud.field('exchange_rate')

async function getConversionRatio(currency, baseCurrency, date) {

    const result = await axios.post('/exchange-rate', {
        date: date,
        base_currency_id: currency,
        conversion_currency_id: baseCurrency,
    });

    console.log(result.data)
    return result.data

}

async function getExchangeRate() {
    if (currencyField.value === null || currencyField.value === '') {
        alert('Please enter the currency');
        exit;
    }

    console.log(currencyField.value)
    console.log(orgCurrencyField.value)
    console.log(startDateField.value)

    const result = await getConversionRatio(currencyField.value, orgCurrencyField.value, startDateField.value)


    if(result) {
        exchangeRateField.value = result.rate
        exchangeRateField.unrequire()
    } else {
        alert('The exchange rate for the currency used is not available on the platform. Please enter the exchange rate manually')
        exchangeRateField.require()
    }



}


function getDateString(date) {
    let currentDay = String(date.getDate()).padStart(2, '0');
    let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
    let currentYear = date.getFullYear();

    return `${currentDay}-${currentMonth}-${currentYear}`;

}
