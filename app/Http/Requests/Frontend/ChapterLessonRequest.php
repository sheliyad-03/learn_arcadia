<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ChapterLessonRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        if ($this->type == 'lesson') {
            return $this->lessonRules();
        } elseif ($this->type == 'document') {
            return $this->documentRules();
        } elseif ($this->type == 'live') {
            return $this->liveRules();
        } else {
            return $this->quizRules();
        }
    }

    function lessonRules(): array {
        $rules = [
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'max:10000'],
            'source'      => ['required'],
            'file_type'   => ['required'],
            'duration'    => ['required', 'numeric', 'min:0'],
            'live_status'   => ['sometimes'],
            'student_mail_sent'   => ['sometimes'],
        ];
        if ($this->source == 'upload') {
            $rules['upload_path'] = ['required'];
        } else {
            $rules['link_path'] = ['required'];
        }

        return $rules;
    }
    function documentRules(): array {
        $rules = [
            'chapter'     => ['required', 'exists:course_chapters,id'],
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'max:10000'],
            'file_type'   => ['required', 'in:txt,pdf,docx'],
            'upload_path' => [
                'required',
                function ($attribute, $value, $fail) {
                    $fileType = request()->input('file_type');
                    $extension = pathinfo($value, PATHINFO_EXTENSION);

                    if ($extension !== $fileType) {
                        $fail(__('The upload file extension does not match the required file type.'));
                    }
                },
            ],
        ];

        return $rules;
    }

    function liveRules(): array {
        $rules = [
            'chapter'     => ['required', 'exists:course_chapters,id'],
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'max:10000'],
            'duration'    => ['required', 'numeric', 'min:0'],
            'live_type'   => ['required'],
            'start_time'  => 'required',
            'meeting_id'  => 'required',
        ];
        if (! $this->live_status && !empty($this->source)) {
            $rules[$this->source === 'upload' ? 'upload_path' : 'link_path'] = ['required'];
        }
        if ($this->live_type == 'zoom') {
            $rules['password'] = ['required'];
            $rules['join_url'] = ['sometimes'];
        }

        return $rules;
    }
    function quizRules(): array {
        $rules = [
            'chapter'    => ['required', 'exists:course_chapters,id'],
            'title'      => ['required', 'max:255', 'string'],
            'time_limit' => ['nullable', 'numeric', 'min:1'],
            'attempts'   => ['nullable', 'numeric', 'min:1'],
            'pass_mark'  => ['required', 'numeric', 'min:1'],
            'total_mark' => ['required', 'numeric', 'min:1'],
        ];

        return $rules;
    }

    public function messages() {
        $messages = [
            'title.required'       => __('The title field is required.'),
            'title.max'            => __('The title may not be greater than 255 characters.'),
            'description.required' => __('The description field is required.'),
            'description.max'      => __('Description must be less than 10000 characters long'),
            'source.required'      => __('The source field is required.'),
            'file_type.required'   => __('The file type field is required.'),
            'chapter.required'     => __('Chapter is required'),
            'chapter.exists'       => __('Chapter doesnt exist'),
            'time_limit.required'  => __('Time limit is required'),
            'time_limit.numeric'   => __('Time limit must be a number'),
            'time_limit.min'       => __('Time limit must be at least 0 minute'),
            'attempts.required'    => __('Number of attempts is required'),
            'attempts.numeric'     => __('Number of attempts must be a number'),
            'attempts.min'         => __('Number of attempts must be at least 1'),
            'pass_mark.required'   => __('Pass mark is required'),
            'pass_mark.numeric'    => __('Pass mark must be a number'),
            'pass_mark.min'        => __('Pass mark must be at least 1'),
            'upload_path.required' => __('The upload path field is required.'),
            'link_path.required'   => __('The link path field is required.'),
            'duration.required'    => __('The duration field is required.'),
            'live_type.required'   => __('Live lesson platform is required'),
            'start_time.required'  => __('Live lesson start time is required'),
            'meeting_id.required'  => __('Meeting ID is required'),
            'password.required'    => __('Password is required'),
            'join_url.required'    => __('Join URL is required'),
        ];

        return $messages;
    }

}
