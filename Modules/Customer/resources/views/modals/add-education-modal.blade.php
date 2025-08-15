<div class="modal-body">
  <form action="{{ route('admin.customer-education-store', $user->id) }}" method="POST" class="instructor__profile-form">
    @csrf
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
            <label for="company">{{ __('Organization') }} <code>*</code></label>
            <input id="company" name="organization" type="text" value="" required class="form-control">
        </div>
      </div>
  
      <div class="col-md-12">
        <div class="form-group">
            <label for="position">{{ __('Degree') }} <code>*</code></label>
            <input id="position" name="degree" type="text" value="" required class="form-control">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
            <label for="start_date">{{ __('Start Date') }} <code>*</code></label>
            <input id="start_date" name="start_date" type="text" value="{{ date('Y-m-d') }}" required class="form-control datepicker">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
            <label for="end_date">{{ __('End Date') }} <code>*</code></label>
            <input id="end_date" name="end_date" type="text" value="{{ date('Y-m-d') }}" required class="form-control datepicker">
        </div>
      </div>
      <div class="col">
        <div class="d-flex">
          <div class="form-group">
              <label>{{ __('Current') }}</label>
          </div>
          <div class="">
              <div class="switcher ml-3">
                  <label for="toggle-2">
                      <input type="checkbox" id="toggle-2" value="1" name="current" />
                      <span><small></small></span>
                  </label>
              </div>
          </div>
      </div>
      </div>
    </div>
    <div class="p-2"></div>
    <div class="text-end">
      <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
  </form>
</div>

