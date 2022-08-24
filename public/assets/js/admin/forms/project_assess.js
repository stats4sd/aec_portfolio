crud.field('redLines')
    .subfield('value').onChange(function (field) {

    console.log('changes', field.value)
    if (field.value === "1") {
        for (let i = 1; i < 14; i++) {
            console.log('+i', i);
            crud.field(i + "_rating").disable();
            crud.field(i + "_rating_comment").disable();
            crud.field(i + "_scoreTags").disable();
        }
    } else {

        // check all others to see if we can enable them
        let toEnable = true

        for (let i = 1; i < 14; i++) {
            console.log('-i', i);

            if (crud.field('redLines').subfield('value', i - 1).value === 1) {
                toEnable = false;
            }
        }

        if (toEnable) {
            for (let i = 1; i < 14; i++) {
                crud.field(i + "_rating").enable();
                crud.field(i + "_rating_comment").enable();
                crud.field(i + "_scoreTags").enable();
            }
        } else {
            for (let i = 1; i < 14; i++) {
                crud.field(i + "_rating").disable();
                crud.field(i + "_rating_comment").disable();
                crud.field(i + "_scoreTags").disable();
            }
        }
    }
})

crud.field('redLines').subfield('value').change()
