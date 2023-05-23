<?php

namespace Tests\Feature;

use App\Models\AssessmentCriteria;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function Pest\Laravel\assertDatabaseHas;

test('assessment criteria can be created', function() {

    $orgId = Organisation::first()->id;

    $item = AssessmentCriteria::factory()
        ->create([
            'organisation_id' => $orgId,
        ]);

    $this->assertDatabaseHas('assessment_criteria',
    [
        'name' => $item->name,
        'organisation_id' => $orgId,
    ]);

});
