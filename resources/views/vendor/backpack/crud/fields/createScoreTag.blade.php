{{-- checklist --}}

@include('crud::fields.inc.wrapper_start')

<div class="d-flex justify-content-end">
    <button class="btn btn-info" onclick="inlineCreate">Add New Example / Indicator</button>

</div>

@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        @loadOnce('bpFieldInitChecklist')
        <script>
            function inlineCreate(element) {
                //
                ajax.post()
            }

            // find current subfield principle_id and filter available values:





        </script>


        @endLoadOnce
    @endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
