function validateScore(el) {

}


// when a principle rating is added; mark that tab as green / complete:

function setTabAsComplete(tab) {
    tab.parentElement.classList.add('bg-success')
    tab.classList.add('text-dark')
}

function setTabAsIncomplete(tab) {
    tab.parentElement.classList.remove('bg-success')
    tab.classList.remove('text-dark')
}

function setRelatedTabToComplete(el) {

    let tabName = el.getAttribute('data-tab');
    // there should only be one!
    let tab = document.querySelectorAll('[tab_name=' + tabName + ']')[0];

    setTabAsComplete(tab);
}

function setRelatedTabToIncomplete(el) {

    let tabName = el.getAttribute('data-tab')
    let tab = document.querySelectorAll('[tab_name=' + tabName + ']')[0];

    setTabAsIncomplete(tab);
}

function updateCount() {
    let countRequired = document.querySelectorAll("[data-update-tab='1']").length;
    let countFinished = 0;

    document.querySelectorAll('[data-update-tab="1"]').forEach(el => {
        console.log(el.value)
        if (el.value !== "") {
            countFinished++;
        }
    })

    // also count any 'na' principles
    document.querySelectorAll('[data-to-disable]').forEach(el => {
        console.log(el.checked)

        var relatedInput = crud.field(el.getAttribute('data-to-disable') + '_rating').input
        if (el.checked && !relatedInput.value) {
            countFinished++;
        }
    })

    console.log('required', countRequired)
    console.log('finished', countFinished)

    if (countRequired === countFinished) {
        crud.field('assessment_complete').show();
        crud.field('assessment_incomplete').hide();
        crud.field('assessment_incomplete_note').hide();
    } else {
        crud.field('assessment_complete').hide();
        crud.field('assessment_incomplete_note').show();
        crud.field('assessment_incomplete').show();
    }
}

// mark the 'compeleted' field as un-clickable by default;
crud.field('assessment_complete').hide();
crud.field('assessment_incomplete').show();


// add Event listeners to all rating fields
document.querySelectorAll("[data-update-tab='1']")

    .forEach((el) => {

        el.addEventListener('keydown', checkEnter)
        el.addEventListener('change', (e) => {

            if (!e.target.value) {
                setRelatedTabToIncomplete(e.target);
                return;
            }

            if (isNaN(Number(e.target.value))) {
                e.target.classList.add('is-invalid')
                let validationMessage = document.getElementById("custom-validation-text-rating_" + e.target.getAttribute('data-tab'))
                if (validationMessage) validationMessage.remove()

                validationMessage = document.createElement("p");
                validationMessage.setAttribute("id", "custom-validation-text-rating_" + e.target.getAttribute('data-tab'))
                validationMessage.innerHTML = '<p class="invalid-feedback d-block">Ratings must be a number between 0 and 2</p>'
                e.target.after(validationMessage)

                setRelatedTabToIncomplete(e.target);
                updateCount()

                return;
            }

            // validate value =< 2
            if (e.target.value > 2 || e.target.value < 0) {
                console.log('invalid');
                e.target.classList.add('is-invalid')
                let validationMessage = document.getElementById("custom-validation-text-rating_" + e.target.getAttribute('data-tab'))
                if (validationMessage) validationMessage.remove()
                validationMessage = document.createElement("p");
                validationMessage.setAttribute("id", "custom-validation-text-rating_" + e.target.getAttribute('data-tab'))
                validationMessage.innerHTML = '<p class="invalid-feedback d-block">Ratings must be between 0 and 2</p>'

                e.target.after(validationMessage)
                setRelatedTabToIncomplete(e.target);
                updateCount()
                return;
            }


            console.log('VALID');
            e.target.classList.remove('is-invalid')
            let validationMessage = document.getElementById("custom-validation-text-rating_" + e.target.getAttribute('data-tab'))
            if (validationMessage) validationMessage.remove()


            setRelatedTabToComplete(e.target);

            updateCount()
        })

        // set initial state
        if (el.value) {
            setRelatedTabToComplete(el)
            updateCount()
        }
    })

document.querySelectorAll("[data-to-disable]")
    .forEach(el => {
        console.log('data-to-disable', el);
        el.addEventListener('change', e => {
            let principleId = e.target.getAttribute('data-to-disable');
            toggleFieldEnabled(principleId, e.target.checked)
            updateCount()
        })

        //set initial state
        if (el.checked) {
            let principleId = el.getAttribute('data-to-disable');

            // hack to fix issue where checklist and table fields are disabled before they are fully initialised:
            setTimeout(() => toggleFieldEnabled(principleId, true), 500)

        }
    })

document.querySelector('[data-check-complete]').addEventListener('change', e => {

    let tab = document.querySelector('[tab_name="confirm-assessment"]')
    if (e.target.checked) {
        setTabAsComplete(tab)
    } else {
        setTabAsIncomplete(tab)
    }
})

let tabComplete = document.querySelector('[tab_name="confirm-assessment"]')
if (document.querySelector('[data-check-complete]').checked) {
    setTabAsComplete(tabComplete)
} else {
    setTabAsIncomplete(tabComplete)
}

function toggleFieldEnabled(principleId, shouldDisable) {

    console.log('principleId', principleId);
    console.log('should disable', shouldDisable);

    if (shouldDisable) {
        crud.field(principleId + '_rating').disable();
        crud.field(principleId + '_rating_comment').disable();

        crud.field('scoreTags' + principleId).disable();
        crud.field('customScoreTags' + principleId).disable();

        setRelatedTabToComplete(crud.field(principleId + '_rating').input)

    } else {

        crud.field(principleId + '_rating').enable();
        crud.field(principleId + '_rating_comment').enable();
        crud.field('scoreTags' + principleId).enable();
        crud.field('customScoreTags' + principleId).enable();

        if (crud.field(principleId + '_rating').value === "") {
            setRelatedTabToIncomplete(crud.field(principleId + '_rating').input)
        }
    }
}

function checkEnter(e) {
    var key = e.charCode || e.keyCode || 0;
    if (key == 13) {
        e.preventDefault();
    }
}

