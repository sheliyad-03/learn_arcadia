<div class="modal-header">
    <h1 class="modal-title fs-5" id="">{{ __('Add Quize Question') }}</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="p-3">
    <form action="{{ route('instructor.course-chapter.quiz-question.store', $quizId) }}" method="POST"
        class="add_lesson_form instructor__profile-form">
        @csrf

        <div class="row">
            <div class="col-md-10">
                <div class="form-grp">
                    <label for="title">{{ __('Question Title') }} <code>*</code></label>
                    <input id="title" name="title" type="text" value="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-grp">
                    <label for="title">{{ __('Grade') }} <code>*</code></label>
                    <input id="title" name="grade" type="text" value="">
                </div>
            </div>
        </div>

        <div>
            <button class="add-answer btn" type="button">{{ __('Add Answer') }}</button>
        </div>

        <div class="answer-container">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="form-grp">
                            <div class="d-flex justify-content-between">
                                <label for="answer">{{ __('Answer Title') }} <code>*</code></label>
                                <button class="remove-answer" type="button"><i class="fas fa-trash-alt"></i></button>
                            </div>
                            <input class="answer" name="answers[]" type="text" value="" required>
                        </div>
                        <div class="switcher d-flex">
                            <p class="me-3">{{ __('Correct Answer') }}</p>
                            <label for="toggle-0">
                                <input type="checkbox" class="correct" id="toggle-0" value="1" name="correct[]" />
                                <span><small></small></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal-footer">
            <button type="submit" class="btn btn-primary submit-btn">{{ __('Create') }}</button>
        </div>
    </form>
</div>
