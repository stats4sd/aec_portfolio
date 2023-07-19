function updateCount() {

    var countRequired = document.querySelectorAll("[data-required='1']").length
    var countFinished = 0;

    document.querySelectorAll("[data-required='1']").forEach(el => {
        if (el.value !== "") {
            countFinished++
        }
    })

    if(countRequired === countFinished) {
        crud.field('redlines_complete').show();
        crud.field('redlines_incomplete').hide();


    } else {
        crud.field('redlines_complete').hide();
        crud.field('redlines_incomplete').show();
    }

}

document.querySelectorAll("[data-required='1']")

    .forEach((el) => {
        el.addEventListener('change', (e) => {
            updateCount()
        })
    })

updateCount()
