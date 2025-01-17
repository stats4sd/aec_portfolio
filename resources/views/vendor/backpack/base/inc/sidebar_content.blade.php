<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

@if(Auth::user()->can('maintain institutions'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation-crud') }}"><i class="la la-home nav-icon"></i> Institutions</a></li>
@endif

@if(Auth::user()->can('maintain admin user invites'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('role-invite') }}"><i class="nav-icon la la-envelope"></i> Admin User Invites</a></li>
@endif

@if(Auth::user()->canAny(['maintain institutions', 'maintain admin user invites']))
<hr />
@endif



@if(Auth::user()->can('manage revisions'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('revision') }}"><i class="nav-icon la la-user-edit"></i> Revisions</a></li>
@endif

@if(Auth::user()->can('manage user feedback'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user-feedback') }}"><i class="nav-icon la la-comment"></i> User feedback</a></li>
@endif

@if(Auth::user()->can('manage removal requests'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('removal-request') }}"><i class="nav-icon la la-trash-alt"></i> Removal Requests</a></li>
@endif

@if(Auth::user()->can('manage help text entries'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('help-text-entry') }}"><i class="nav-icon la la-trash-alt"></i> Help Text Entry</a></li>
@endif

@if(Auth::user()->canAny(['manage revisions', 'manage user feedback', 'manage removal requests', 'manage help text entries']))
<hr />
@endif



@if(Auth::user()->can('manage definitions'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('institution-type') }}"><i class="nav-icon la la-university"></i> Institution types</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('initiative-category') }}"><i class="nav-icon la la-project-diagram"></i> Initiative categories</a></li>
<hr />
@endif



@if(Auth::user()->can('maintain red lines'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('red-flag') }}"><i class="nav-icon la la-exclamation-triangle"></i> Red flags</a></li>
@endif

@if(Auth::user()->can('maintain principles'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('principle') }}"><i class="nav-icon la la-stream"></i> Principles</a></li>
@endif

@if(Auth::user()->can('maintain score tags'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('score-tag') }}"><i class="nav-icon la la-tag"></i> Score tags</a></li>
@endif

@if(Auth::user()->can('review custom score tags'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('custom-score-tag') }}"><i class="nav-icon la la-tag"></i> Custom Score tags</a></li>
@endif

@if(Auth::user()->canAny(['maintain red lines', 'maintain principles', 'maintain score tags', 'review custom score tags']))
<hr />
@endif



@if(Auth::user()->can('view continents'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('continent') }}"><i class="nav-icon la la-globe"></i> Continents</a></li>
@endif

@if(Auth::user()->can('view regions'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('region') }}"><i class="nav-icon la la-arrows-alt"></i> Regions</a></li>
@endif

@if(Auth::user()->can('view countries'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('country') }}"><i class="nav-icon la la-flag"></i> Countries</a></li>
@endif