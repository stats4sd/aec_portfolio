@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.assess') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                            class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="{{ $crud->getEditContentClass() }}">
            {{-- Default box --}}

            @include('crud::inc.grouped_errors')

            <form
                id="redlines-form"
                method="post"
                action="{{ url($crud->route.'/'.$entry->getKey()).'/redline' }}"
                @if ($crud->hasUploadFields('update', $entry->getKey()))
                    enctype="multipart/form-data"
                @endif
            >
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}

                @if ($crud->model->translationEnabled())
                    <div class="mb-2 text-right">
                        {{-- Single button --}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                {{trans('backpack::crud.language')}}
                                : {{ $crud->model->getAvailableLocales()[request()->input('_locale')?request()->input('_locale'):App::getLocale()] }}
                                &nbsp; <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($crud->model->getAvailableLocales() as $key => $locale)
                                    <a class="dropdown-item"
                                       href="{{ url($crud->route.'/'.$entry->getKey().'/redline') }}?_locale={{ $key }}">{{ $locale }}</a>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                {{-- load the view from the application if it exists, otherwise load the one in the package --}}
                @if(view()->exists('vendor.backpack.crud.form_content'))
                    @include('vendor.backpack.crud.form_content', ['fields' => $crud->fields(), 'action' => 'redline'])
                @else
                    @include('crud::form_content', ['fields' => $crud->fields(), 'action' => 'redline'])
                @endif
                {{-- This makes sure that all field assets are loaded. --}}
                <div class="d-none" id="parentLoadedAssets">{{ json_encode(Assets::loaded()) }}</div>

                <div class="form-group" id="saveActions">

                    <input type="hidden" id="_redirect" name="_redirect" value="project">


                    <a href="{{ url($crud->route) }}" class="btn btn-default">
                        <span class="la la-ban"></span>
                        <span>Cancel</span>
                    </a>

                    <div class="btn btn-primary active" id="save-and-return-button" data-value="save_and_return_to_projects"
                            onclick="startAssessment(this, 'project')">
                        <span class="la la-save" role="presentation" aria-hidden="true"></span>
                        <span>Save and Return</span>
                    </div>

                    <div id="start-principle-assessment-button" class="btn btn-secondary"
                         data-value="save_and_start_assessment"
                         onclick="startAssessment(this, '{{"assessment/".$entry->getKey()."/assess"}}')" disabled>
                        <span class="la la-arrow-right" role="presentation" aria-hidden="true"></span>
                        <span>Save and Start Principle Assessment</span>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('after_scripts')

    <script>

        // this function checks if form is valid.
        function checkFormValidity(form) {
            // the condition checks if `checkValidity` is defined in the form (browser compatibility)
            if (form[0].checkValidity) {
                return form[0].checkValidity();
            }
            return false;
        }

        // this function checks if any of the inputs has errors and report them on page.
        // we use it to report the errors after form validation fails and making the error fields visible
        function reportValidity(form) {
            // the condition checks if `reportValidity` is defined in the form (browser compatibility)
            if (form[0].reportValidity) {
                // validate and display form errors
                form[0].reportValidity();
            }
        }


    </script>

@endpush
