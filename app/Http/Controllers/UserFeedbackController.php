<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFeedbackRequest;
use App\Mail\UserFeedbackSubmitted;
use App\Models\User;
use App\Models\UserFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserFeedbackController extends Controller
{
    public function store(UserFeedbackRequest $request)
    {
        $validated = $request->validated();

        $feedback = UserFeedback::create($validated);

        $feedback->syncFromMediaLibraryRequest($request->uploads)
            ->toMediaLibrary();

        // send email

        $managers = User::whereHas('roles', function (Builder $query) {
            $query->whereIn('roles.name', ['Site Admin', 'Site Manager']);
        })->get();

        foreach ($managers as $manager) {
            Mail::to($manager->email)->send(new UserFeedbackSubmitted($feedback));
        }

        return $feedback;
    }
}
