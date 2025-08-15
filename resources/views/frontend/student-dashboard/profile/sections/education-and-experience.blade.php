<div class="tab-pane fade show {{ session('profile_tab') == 'education' ? 'active' : '' }}" id="itemFive-tab-pane"
    role="tabpanel" aria-labelledby="itemFive-tab" tabindex="0">
    <!-- Experience -->
    <div class="instructor__profile-form-wrap">
        <div class="dashboard__content-title d-flex justify-content-between">
            <h4 class="title">{{ __('Experience') }}</h4>
            <button type="button" class="btn btn-primary btn-hight-basic show-modal"
                data-url="{{ route('student.setting.experience-modal') }}">
                {{ __('Add Experience') }}
            </button>

        </div>
        <div class="row">
            <div class="col-12">
                <div class="dashboard__review-table table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Compnay') }}</th>
                                <th>{{ __('Position') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($experiences as $experience)
                                <tr>
                                    <td>
                                        <p>{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $experience->company }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $experience->position }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $experience->start_date }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $experience->current ? 'Present' : $experience->end_date }}</p>
                                    </td>
                                    <td>
                                        <div class="dashboard__review-action">
                                            <a href="#" class="show-modal"
                                                data-url="{{ route('student.setting.edit-experience-modal', $experience->id) }}"
                                                title="Edit"><i class="far fa-edit"></i></i></a>
                                            <a href="{{ route('student.setting.experience.destroy', $experience->id) }}"
                                                class="delete-item" title="Delete"><i class="far fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="6" class="text-center">
                                    <span class="text-muted">{{ __('No Data!') }}</span>
                                </td>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <!-- Education -->
    <div class="instructor__profile-form-wrap">
        <div class="dashboard__content-title d-flex justify-content-between">
            <h4 class="title">{{ __('Education') }}</h4>
            <button type="button" class="btn btn-primary btn-hight-basic show-modal"
                data-url="{{ route('student.setting.add-education-modal') }}">
                {{ __('Add Education') }}
            </button>

        </div>
        <div class="row">
            <div class="col-12">
                <div class="dashboard__review-table table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Education') }}</th>
                                <th width="20%">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($educations as $education)
                                <tr>
                                    <td>
                                        <p>{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $education->education }}</p>
                                    </td>

                                    <td>
                                        <div class="dashboard__review-action">
                                            <a href="#" class="show-modal"
                                                data-url="{{ route('student.setting.edit-education-modal', $education->id) }}"
                                                title="Edit"><i class="far fa-edit"></i></i></a>
                                            <a href="{{ route('student.setting.education.destroy', $education->id) }}"
                                                class="delete-item" title="Delete"><i class="far fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="6" class="text-center">
                                    <span class="text-muted">{{ __('No Data!') }}</span>
                                </td>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
