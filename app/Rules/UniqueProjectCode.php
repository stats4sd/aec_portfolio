<?php

namespace App\Rules;

use App\Models\Project;
use App\Models\Organisation;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class UniqueProjectCode implements Rule, DataAwareRule
{

    protected $data = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $projects = Organisation::find($this->data['organisation_id'])->projects;

        foreach ($projects as $project) {
            // if there is a current project, skip it for checking for uniqueness;
            if (isset($this->data['id']) && $project->id === (int)$this->data['id']) {
                break;
            }

            
            if ($value === $project->code) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This project code is not unique for your organisation. Please enter a unique code to make it easier to identify the projects.';
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}