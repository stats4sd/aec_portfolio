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

document.querySelectorAll("[data-update-tab='1']")

    .forEach((el) => {
        el.addEventListener('change', (e) => {
            if (e.target.value) {
                setRelatedTabToComplete(e.target);
            } else {
                setRelatedTabToIncomplete(e.target);
            }
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
        el.addEventListener('change', e => {
            let principleId = e.target.getAttribute('data-to-disable');

            if (e.target.checked) {

                crud.field(principleId + '_rating').disable();
                crud.field(principleId + '_rating_comment').disable();
                crud.field(principleId + '_scoreTags').disable();

                setRelatedTabToComplete(crud.field(principleId + '_rating').input)

            } else {
                crud.field(principleId + '_rating').enable();
                crud.field(principleId + '_rating_comment').enable();
                crud.field(principleId + '_scoreTags').enable();

                if (crud.field(principleId + '_rating').value === "") {
                    setRelatedTabToIncomplete(crud.field(principleId + '_rating').input)
                }
            }

            updateCount()
        })

        //set initial state
        if (el.checked) {
            let principleId = el.getAttribute('data-to-disable');

            crud.field(principleId + '_rating').disable();
            crud.field(principleId + '_rating_comment').disable();
            crud.field(principleId + '_scoreTags').disable();

            setRelatedTabToComplete(crud.field(principleId + '_rating').input)
        }
    })

document.querySelector('[data-check-complete]').addEventListener('change', e => {

    let tab = document.querySelector('[tab_name="confirm-assessment"]')
    if(e.target.checked) {
        setTabAsComplete(tab)
    } else {
        setTabAsIncomplete(tab)
    }
})

let tabComplete = document.querySelector('[tab_name="confirm-assessment"]')
if(document.querySelector('[data-check-complete]').checked) {
    setTabAsComplete(tabComplete)
} else {
    setTabAsIncomplete(tabComplete)
}