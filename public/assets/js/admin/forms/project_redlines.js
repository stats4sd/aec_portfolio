var allRedlines = document.querySelectorAll("[data-required-wrapper='1']")

console.log('hi', allRedlines.values())


// do the initial check for completeness
checkComplete()


for (const radioField of allRedlines.values()) {


    // on change, check all the others to see if they are completed.
    radioField.addEventListener('change', e => {
        checkComplete()
    })

    console.log('hi')
    console.log(crud.field(radioField.getAttribute('bp-field-name')).value);
}


function checkComplete() {
    let values = []
    for (const innerField of allRedlines.values()) {
        values.push(crud.field(innerField.getAttribute('bp-field-name')).value)
    }

    console.log(values);
    if (values.includes('')) {
        crud.field('redlines_complete').hide()
        crud.field('redlines_incomplete').show()
    } else {
        crud.field('redlines_complete').show()
        crud.field('redlines_incomplete').hide()
    }
}
