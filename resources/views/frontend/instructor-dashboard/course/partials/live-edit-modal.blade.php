<div class="modal-header">
    <h1 class="modal-title fs-5" id="">{{ __('Edit live lesson') }}</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="p-3">
    <form action="{{ route('instructor.course-chapter.lesson.update') }}" method="POST"
        class="update_lesson_form instructor__profile-form">
        @csrf
        <input type="hidden" name="course_id" value="{{ $courseId }}">
        <input type="hidden" name="chapter_item_id" value="{{ $chapterItem->id }}">
        <input type="hidden" name="type" value="{{ $chapterItem->type }}">

        <div class="col-md-12">
            <div class="form-grp">
                <label for="chapter">{{ __('Chapter') }} <code>*</code></label>
                <select name="chapter" id="chapter" class="chapter form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($chapters as $chapter)
                        <option @selected($chapterItem->chapter_id == $chapter->id) value="{{ $chapter->id }}">{{ $chapter->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-grp">
                <label for="title">{{ __('Title') }} <code>*</code></label>
                <input id="title" name="title" type="text" value="{{ $chapterItem->lesson->title }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-grp">
                    <label for="live_type">{{ __('Live Platform') }} <code>*</code></label>
                    <select name="live_type" id="live_type" class="form-select">
                        @foreach (config('course.live_types') as $key => $value)
                            <option @selected($chapterItem->lesson->live->type == $key) value="{{ $key }}">{{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-grp">
                    <label for="start_time">{{ __('Start Time') }} <code>*</code></label>
                    <input id="start_time" name="start_time" type="datetime-local"
                        value="{{ $chapterItem->lesson->live->start_time }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-grp">
                    <label for="duration">{{ __('Duration') }} <code>* ({{ __('in minutes') }})</code></label>
                    <input id="duration" name="duration" type="text" value="{{ $chapterItem->lesson->duration }}">
                </div>
            </div>
        </div>
        <div class="col-12 zoom-alert-box {{ $chapterItem->lesson->live->type == 'jitsi' ? 'd-none' : '' }}">
            <div class="alert alert-warning" role="alert">
                {{__('The meeting ID, password, and Zoom settings must be configured using the same Zoom account. The course creator needs to set up the')}} <a
                    href="{{ route('instructor.zoom-setting.index') }}">{{ __('Zoom live setting') }}</a>.
            </div>
        </div>
        <div class="col-12 jitsi-alert-box {{ $chapterItem->lesson->live->type == 'zoom' ? 'd-none' : '' }}">
            <div class="alert alert-warning" role="alert">
                {{__('The meeting ID and Jitsi settings must be configured. The course creator needs to set up the')}} <a
                    href="{{ route('instructor.jitsi-setting.index') }}">{{ __('Jitsi setting') }}</a>.
            </div>
        </div>
        <div class="row">
            <div class="col-md-{{ $chapterItem->lesson->live->type == 'jitsi' ? '12' : '6' }} meeting-id-box">
                <div class="form-grp">
                    <label for="meeting_id">{{ __('Meeting ID') }} <code>*</code></label>
                    <input id="meeting_id" name="meeting_id" type="text"
                        value="{{ $chapterItem->lesson->live->meeting_id }}">
                </div>
            </div>
            <div class="col-md-6 zoom-box {{ $chapterItem->lesson->live->type == 'jitsi' ? 'd-none' : '' }}">
                <div class="form-grp">
                    <label for="password">{{ __('Password') }} <code>*</code></label>
                    <input id="password" name="password" type="text"
                        value="{{ $chapterItem->lesson->live->password }}">
                </div>
            </div>
            <div class="col-md-12 zoom-box {{ $chapterItem->lesson->live->type == 'jitsi' ? 'd-none' : '' }}">
                <div class="form-grp">
                    <label for="join_url">{{ __('Join URL') }}</label>
                    <input id="join_url" name="join_url" type="url" value="{{ $chapterItem->lesson->live->join_url }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-grp">
                    <label for="source">{{ __('Source') }}</label>
                    <select name="source" id="source" class="source form-select">
                        <option value="">{{ __('Live') }}</option>
                        @foreach (config('course.storage_source') as $key => $value)
                            @if (in_array($key, ['upload', 'youtube', 'vimeo','external_link']))
                                <option @selected($chapterItem->lesson->storage == $key) value="{{ $key }}">{{ $value }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-8 upload {{ $chapterItem->lesson->storage == 'upload' ? '' : 'd-none' }}">
                <div class="from-group mb-3">
                    <label class="form-file-manager-label" for="">{{ __('Path') }}
                        <code></code></label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <a data-input="path" data-preview="holder" class="file-manager">
                                <i class="fa fa-picture-o"></i> {{ __('Choose') }}
                            </a>
                        </span>
                        <input id="path" readonly class="form-control file-manager-input" type="text"
                            name="upload_path"
                            value="{{ $chapterItem->lesson->storage == 'upload' ? $chapterItem->lesson->file_path : '' }}">
                    </div>
                </div>
            </div>
            <div class="col-md-8 link_path {{ $chapterItem->lesson->storage != 'upload' ? '' : 'd-none' }} ">
                <div class="form-grp">
                    <label for="meta_description">{{ __('Path') }} <code></code></label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-link"></i></span>
                        <input type="text" class="form-control" name="link_path"
                            placeholder="{{ __('paste source url') }}"
                            value="{{ $chapterItem->lesson->storage != 'upload' ? $chapterItem->lesson->file_path : '' }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-grp">
                <label for="description">{{ __('Description') }} <code></code></label>
                <textarea name="description" class="form-control">{{ $chapterItem->lesson->description }}</textarea>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="account__check-remember">
                <input id="student_mail_sent" type="checkbox" class="form-check-input" name="student_mail_sent">
                <label for="student_mail_sent" class="form-check-label">{{ __('Email to all students.') }}</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary submit-btn">{{ __('Update') }}</button>
        </div>
    </form>
</div>
