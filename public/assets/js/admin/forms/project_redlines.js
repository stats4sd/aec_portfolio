var allRedlines = document.querySelectorAll("[data-required-wrapper='1']")

// do the initial check for completeness
checkComplete()


for (const radioField of allRedlines.values()) {
    // on change, check all the others to see if they are completed.
    radioField.addEventListener('change', e => {
        checkComplete()
    })
}

// check for completeness + passed when the complete checkbox is updated.
crud.field('redlines_complete').onChange((field) => checkComplete())


function checkComplete() {
    let values = []
    for (const innerField of allRedlines.values()) {
        values.push(crud.field(innerField.getAttribute('bp-field-name')).value)
    }

    if (values.includes('')) {
        crud.field('redlines_complete').hide()
        crud.field('redlines_incomplete').show()
    } else {
        crud.field('redlines_complete').show()
        crud.field('redlines_incomplete').hide()

        // enable or disable principle assessment link
        const passed = checkPassed(values)
        const nextButton = document.getElementById('start-principle-assessment-button')

        if(passed) {

            nextButton.classList.add('active')
            nextButton.classList.add('btn-success')
            nextButton.classList.remove('btn-secondary')


        } else {
            nextButton.classList.remove('active')
            nextButton.classList.add('btn-secondary')
            nextButton.classList.remove('btn-success')
        }
    }
}

function checkPassed(values) {

    // also return false if the user has not confirmed the assessment as complete
    if(crud.field('redlines_complete').value !== '1') {
        return false;
    }

    if(values.includes('1')) {
        return false;
    }

    return true;
}
