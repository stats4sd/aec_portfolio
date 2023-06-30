currencyField = crud.field('currency')
orgCurrencyField = crud.field('org_currency')
startDateField = crud.field('start_date')
exchangeRateField = crud.field('exchange_rate')

async function getConversionRatio(currency, baseCurrency, date) {

    const result = await axios.post('/exchange-rate', {
        date: date,
        base_currency_id: baseCurrency,
        target_currency_id: currency,
    });

    return result.data

}

async function getExchangeRate() {
    if (currencyField.value === null || currencyField.value === '') {
        alert('Please enter the currency');
        return
    }

    const result = await getConversionRatio(currencyField.value, orgCurrencyField.value, startDateField.value)


    if (result) {
        console.log('result', result)
        console.log(Number(result.rate))
        exchangeRateField.input.value = Number(result.rate)

        exchangeRateField.input.classList.add('border-success');
        exchangeRateField.input.classList.add('bg-light-success');

    } else {
        await swal.fire(
            'Exchange Rate Not Found',
            `Exchange rate for ${currencyField.value} to ${orgCurrencyField.value} could not be retrieved automatically. Please enter the exchange rate to be used manually.`,
            'warning'
        )
        exchangeRateField.input.classList.remove('border-success');
        exchangeRateField.input.classList.remove('bg-light-success');

    }


}
