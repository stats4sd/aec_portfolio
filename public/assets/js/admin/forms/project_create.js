currencyField = crud.field('currency')
orgCurrencyField = crud.field('org_currency')
startDateField = crud.field('start_date')
exchangeRateField = crud.field('exchange_rate')
exchangeRateEurField = crud.field('exchange_rate_eur')

crud.field('currency').onChange(function (field) {

    crud.field('get_exchange_rate_button').hide(field.value.toUpperCase() == orgCurrencyField.value).disable(field.value.toUpperCase() == orgCurrencyField.value);
    crud.field('exchange_rate').hide(field.value.toUpperCase() == orgCurrencyField.value)

    if (field.value.toUpperCase() == orgCurrencyField.value) {
        crud.field('exchange_rate').input.value = 1;
    }

    crud.field('get_exchange_rate_eur_button').hide(field.value.toUpperCase() == 'EUR').disable(field.value.toUpperCase() == 'EUR');
    crud.field('exchange_rate_eur').hide(field.value.toUpperCase() == "EUR")

    if (field.value.toUpperCase() == 'EUR') {
        crud.field('exchange_rate_eur').input.value = 1;
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


async function getExchangeRateEur() {
    if (currencyField.value === null || currencyField.value === '') {
        alert('Please enter the currency');
        return
    }

    const result = await getConversionRatio(currencyField.value, 'EUR', startDateField.value)


    if (result) {
        console.log('result', result)
        console.log(Number(result.rate))
        exchangeRateEurField.input.value = Number(result.rate)

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
