<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

<!--

Question: How should user select an institutions?

We have three situations:
1. For site admin and site manager, they need to access all institutions
2. For institutional admin, assessor and member, they need to access one institution only
3. For user belongs to multiple institutions, they need a way to select / view different institutions

I can imagine two options:

1. Select an institution in different features
   E.g., Portfolio-level dashboard, user selects institution then select a corresponding portfolio
   E.g., When creating a new project, user selects institution then portfolio selection box only shows portfolios belong to the selected institution.

2. Select an institution immediately after login. Store the selected institution in session object for access across the whole application.
   E.g., When creating a new project, user does not need to select institution (it is done at the beginning after login).
   Portfolio selection box shows portfolios belong to the selected institution.

I would recommend option 2, because:
1. It is more initutive as institutions are considered as individually separated entities
2. It is indirectly mentioning institutional data are seperated
3. It simplifies program design and coding
E.g., Centralise instituion selection to a single feature instead of distributing it in different features

-->


<!-- TODO: need to decide how does a user select an institution first -->

<!-- Hide them from main menu, but keep them for reference -->


@if(Auth::user()->can('select institution'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('selected_organisation') }}"><i class="la la-home nav-icon"></i> Select Institution</a></li>
@endif


@if(Auth::user()->can('view institutions'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation') }}"><i class="la la-home nav-icon"></i> Institutions</a></li>
@endif



<!-- TODO: this page requires a full review on Policy and permisssions -->
@if(Auth::user()->canAny(['invite institutional members', 'update role of institutional members', 'maintain institutional members']))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation-members') }}"><i class="la la-user-friends nav-icon"></i> Institution Members</a></li>
    <hr/>
@endif



@if(Auth::user()->can('view portfolios'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('portfolio') }}'><i class='nav-icon la la-object-group'></i> Portfolios</a></li>
@endif

@if(Auth::user()->can('view projects'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('project') }}'><i class='nav-icon la la-project-diagram'></i> Initiatives</a></li>
@endif

@if(Auth::user()->canAny(['view portfolios', 'view projects']))
    <hr/>
@endif



@if(Auth::user()->can('view red lines'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('red-line') }}'><i class='nav-icon la la-exclamation-triangle'></i> Red lines</a></li>
@endif

@if(Auth::user()->can('view principles'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('principle') }}"><i class="nav-icon la la-stream"></i> Principles</a></li>
@endif

@if(Auth::user()->can('view score tags'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('score-tag') }}"><i class="nav-icon la la-tag"></i> Score tags</a></li>
@endif

@if(Auth::user()->canAny(['view red lines', 'view principles', 'view score tags']))
    <hr/>
@endif

@if(Auth::user()->canAny(['maintain custom principles', 'view custom principles']))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('additional-criteria') }}"><i class="nav-icon la la-tag"></i> Additional Assessment criteria</a></li>
@endif


@if(Auth::user()->can('view users'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> Users</a></li>
@endif

@if(Auth::user()->can('view admin user invites'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role-invite') }}"><i class="nav-icon la la-envelope"></i> Admin User Invites</a></li>
@endif

@if(Auth::user()->canAny(['view users', 'view admin user invites']))
    <hr/>
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

@if(Auth::user()->canAny(['view continents', 'view regions', 'view countries']))
    <hr/>
@endif



@if(Auth::user()->canAny(['view institution-level dashboard', 'view portfolio-level dashboard']))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('generic-dashboard') }}"><i class="nav-icon la la-tachometer-alt"></i> Dashboard</a></li>
@endif


@if(Auth::user()->canAny(['view institution-level dashboard', 'view portfolio-level dashboard', 'view project-level dashboard']))
    <hr/>
@endif



@if(Auth::user()->can('maintain institutions'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('removal-request') }}"><i class="nav-icon la la-trash-alt"></i> Removal Requests</a></li>
    <hr/>
@endif

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('initiative-category') }}"><i class="nav-icon la la-question"></i> Initiative categories</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('institution-type') }}"><i class="nav-icon la la-question"></i> Institution types</a></li>