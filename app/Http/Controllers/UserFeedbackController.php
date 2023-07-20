<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFeedbackRequest;
use App\Models\UserFeedback;
use Illuminate\Http\Request;

class UserFeedbackController extends Controller
{
    public function store(UserFeedbackRequest $request)
    {
        $validated = $request->validated();

        $feedback = UserFeedback::create($validated);

        $feedback->syncFromMediaLibraryRequest($request->uploads)
            ->toMediaLibrary();

        return $feedback;
    }
}
