<template>
    <div class="card w-100 px-4 py-4">
        <div class="card-body">
            <div class="px-5 mx-5">

                <h3 class="text-bright-green font-weight-bold">Agrocological Principles Assessment</h3>

                <div class="mb-5">

                    This is the main section of the review. Below are the 13 Agroecology Principles, and you should rate the project against each one.
                    <br/><br/>
                    For each principle, you should give:

                    <ul>
                        <li>A rating: This is a number between 0 and 2, based on your appreciation of the value of the principle in the project design / activities, and following the Spectrum defined for each principle. Decimal digits are allowed.</li>
                        <li>A comment: Please add any comments to help explain the rating, and about how the principle is seen within the project.</li>
                    </ul>
                    Each principle also lists a set of example activities relevant to that principle. Please tick all activities that are present in the project.
                    <br/><br/>
                    To help track progress, once a rating is given for a principle, that principle name will turn green. Once you have completed and reviewed every
                    principle, please click the button mark the assessment as complete. Once done, you will return to the main projects list to view the final result.
                    <br/><br/><br/>
                    <b class="text-deep-green">Click on a principle to begin:</b>

                </div>

            </div>
        </div>
    </div>

</template>

<script>
export default {
    mounted() {
        console.log('Component mounted.')
    },

    data() {
        return {
            principles: [],
        }
    }
}
</script>
