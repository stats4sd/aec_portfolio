<i
    {{ $attributes->class(['ml-2 cursor-help la la-question-circle']) }}
    data-toggle="{{ $type }}"
    data-container="body"
    data-html="true"
    data-trigger="focus"
    tabindex="0"
    role="button"
    aria-expanded="false"
    data-target="#{{ \Illuminate\Support\Str::slug($location)  }}"
    data-content="{{ $helpTextEntry->text }}"

>
</i>
