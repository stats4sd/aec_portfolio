currencyField = crud.field('currency')
orgCurrencyField = crud.field('org_currency')
startDateField = crud.field('start_date')
exchangeRateTitle = crud.field('exchange_rate_title')
exchangeRateField = crud.field('exchange_rate')
exchangeRateEurField = crud.field('exchange_rate_eur')

crud.field('currency').onChange(function (field) {

    // only hide "Get Exchange Rate" button if organisation currency, initiative currency are both EUR, no exchange rate is required
    if ((field.value.toUpperCase() == orgCurrencyField.value) && (field.value.toUpperCase() === 'EUR')) {
        crud.field('exchange_rate_title').hide();
        crud.field('get_exchange_rate_button').hide();
    } else {
        crud.field('exchange_rate_title').show();
        crud.field('get_exchange_rate_button').show();
    }

    crud.field('exchange_rate').hide(field.value.toUpperCase() == orgCurrencyField.value)

    if (field.value.toUpperCase() == orgCurrencyField.value) {
        crud.field('exchange_rate').input.value = 1;
    }

    crud.field('exchange_rate_eur').hide(field.value.toUpperCase() == "EUR" || orgCurrencyField.value == "EUR")

    if (field.value.toUpperCase() == 'EUR') {
        crud.field('exchange_rate_eur').input.value = 1;
    }

}).change();


crud.field('exchange_rate').onChange(function (field) {

    // exchange_rate_eur is hidden when organisation currency is EUR, because it is not necessary to show two same exchange rate in front end.
    // copy exchange_rate value to exchange_rate_eur when exchange_rate value changed
    if (orgCurrencyField.value == 'EUR') {
        crud.field('exchange_rate_eur').input.value = crud.field('exchange_rate').input.value;
    }

}).change();


async function getConversionRatio(baseCurrency, currency, date) {

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

    const result = await getConversionRatio(currencyField.value, orgCurrencyField.value, startDateField.value);

    const result2 = await getConversionRatio(currencyField.value, 'EUR', startDateField.value);


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


    if (result2) {
        console.log('result2', result2)
        console.log(Number(result2.rate))
        exchangeRateEurField.input.value = Number(result2.rate)

        exchangeRateEurField.input.classList.add('border-success');
        exchangeRateEurField.input.classList.add('bg-light-success');

    } else {
        await swal.fire(
            'Exchange Rate Not Found',
            `Exchange rate for ${currencyField.value} to EUR could not be retrieved automatically. Please enter the exchange rate to be used manually.`,
            'warning'
        )
        exchangeRateEurField.input.classList.remove('border-success');
        exchangeRateEurField.input.classList.remove('bg-light-success');

    }

}


function checkInitiativeCategoryOtherField() {
    if (crud.field('initiativeCategory').value === '5') {
        crud.field('initiative_category_other').enable()
    } else {
        crud.field('initiative_category_other').disable()
    }
}

// only enable "category other" field if 'other' is selected
checkInitiativeCategoryOtherField();
crud.field('initiativeCategory').onChange(function (field) {
    checkInitiativeCategoryOtherField()
})
