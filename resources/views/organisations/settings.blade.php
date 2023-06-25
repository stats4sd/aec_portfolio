<div class="card-header">

    <h2>Institution Details</h2>
    <p class="help-block">Add or edit the relevant information for the institution.</p>

</div>

<div class="card-body">
    <form action="{{ route('organisation.self.update') }}" method="POST">
        @method('PUT')
        @csrf

                    @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        <div class="form-group row mt-16">
            <label for="input_name" class="col-sm-4 col-form-label text-right pr-2">Institution Name</label>
            <div class="col-sm-8">
                <input name="name" class="form-control" id="input_name" value="{{ old('name') ?? $organisation->name }}">
                @if($errors->has('name'))
                    <span class="text-danger emphasis show" role="alert">
                            <strong>{{ collect($errors->get('currency'))->join('<br/>') }}</strong>
                        </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="input_currency" class="col-sm-4 col-form-label text-right pr-2">Main Currency (3-letter code)</label>
            <div class="col-sm-8 col-lg-4">
                <input name="currency" class="form-control" id="input_currency" value="{{ old('currency') ?? $organisation->currency }}">
                @if($errors->has('currency'))
                    <span class="text-danger emphasis show" role="alert">
                            <strong>{{ collect($errors->get('currency'))->join('<br/>') }}</strong>
                        </span>
                @endif
                <small id="emailHelp" class="form-text font-sm">This currency will be used for the summary dashboard. All initiative budgets for your institution will be converted into this currency.</small>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="additional_criteria_check" name="has_additional_criteria" @if($organisation->has_additional_criteria) checked @endif value="1">
                    <label class="form-check-label" for="additional_criteria_check">
                        This Institution uses additional assessment criteria
                    </label>
                    @if($errors->has('has_additional_criteria'))
                        <span class="text-danger emphasis show" role="alert">
                            <strong>{{ collect($errors->get('currency'))->join('<br/>') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>


        <div class="form-group d-flex justify-content-end mt-16">
            <button type="cancel" class="btn btn-secondary mr-4">Discard Changes</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
