{{-- radio --}}
@php
    $optionValue = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';

    // check if attribute is casted, if it is, we get back un-casted values
    if(Arr::get($crud->model->getCasts(), $field['name']) === 'boolean') {
        $optionValue = (int) $optionValue;
    }

    // if the class isn't overwritten, use 'radio'
    if (!isset($field['attributes']['class'])) {
        $field['attributes']['class'] = 'radio';
    }

    $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
    $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitRadioElement';
@endphp

@include('crud::fields.inc.wrapper_start')


        <label class="d-block">{!! $field['label'] !!}</label>
        @include('crud::fields.inc.translatable_icon')


    <input type="hidden" value="{{ $optionValue }}" name="{{$field['name']}}" />

    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )

        <ul class="red-line">


        @foreach ($field['options'] as $value => $label )

            <li class="form-check {{ isset($field['inline']) && $field['inline'] ? 'form-check-inline' : '' }} {{ $value === 1 ? 'yes' : 'no' }}">
                <input  type="radio"
                        class="form-check-input"
                        value="{{$value}}"
                        @include('crud::fields.inc.attributes')
                        >
                <label class="{{ isset($field['inline']) && $field['inline'] ? 'radio-inline' : '' }} form-check-label font-weight-normal">{!! $label !!}</label>
            </li>

        @endforeach
        </ul>

    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

@include('crud::fields.inc.wrapper_end')

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    @loadOnce('bpFieldInitRadioElement')
    <script>
        function bpFieldInitRadioElement(element) {
            var hiddenInput = element.find('input[type=hidden]');
            var value = hiddenInput.val();
            var id = 'radio_'+Math.floor(Math.random() * 1000000);

            // set unique IDs so that labels are correlated with inputs
            element.find('.form-check input[type=radio]').each(function(index, item) {
                $(this).attr('id', id+index);
                $(this).siblings('label').attr('for', id+index);
            });

            hiddenInput.on('CrudField:disable', function(e) {
                element.find('.form-check input[type=radio]').each(function(index, item) {
                    $(this).prop('disabled', true);
                });
            });

            hiddenInput.on('CrudField:enable', function(e) {
                element.find('.form-check input[type=radio]').each(function(index, item) {
                    $(this).removeAttr('disabled');
                });
            });

            // when one radio input is selected
            element.find('input[type=radio]').change(function(event) {
                // the value gets updated in the hidden input and the 'change' event is fired
                hiddenInput.val($(this).val()).change();
                // all other radios get unchecked
                element.find('input[type=radio]').not(this).prop('checked', false);
            });

            // select the right radios
            element.find('input[type=radio][value="'+value+'"]').prop('checked', true);
        }
    </script>
    @endLoadOnce
    @endpush
