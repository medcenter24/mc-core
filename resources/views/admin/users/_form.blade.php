{{ csrf_field() }}

<div class="form-group">
    <div class="row">
        <div class="col-sm-5 col-lg-4">
            <label for="name">@lang('content.name')</label>
            <input type="text" class="form-control" name="name" id="name" title="@lang('content.name')"
                   placeholder="@lang('content.name')" value="{{ old('name', $user->name) }}">
        </div>
        <div class="col-sm-5 col-lg-4">
            <label for="confirmation">@lang('content.email')</label>
            <input type="text" class="form-control" name="email" id="email"
                   title="@lang('content.email')"
                   placeholder="@lang('content.email')" value="{{ old('email', $user->email) }}"
                   autocomplete="off">
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-5">
            <label for="roles">@lang('content.roles')</label>
            <select name="roles[]" id="roles" size="4" class="form-control" multiple title="@lang('admin.roles')">
                @foreach(\App\Role::all() as $role)
                    <option value="{{ $role->id }}"{{ count(old('roles')) ? (in_array($role->id, old('roles')) ? 'selected' : '') : ($user->hasRole($role->title) ? ' selected' : '') }}>
                        {{ $role->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-5 col-lg-4">
            <label for="password">@lang('content.password')</label>
            <input type="password" class="form-control" name="password" id="password" title="@lang('content.password')"
                   placeholder="@lang('content.password')" value="{{ old('password', '') }}" autocomplete="off">

            <label for="confirmation">@lang('content.confirmation')</label>
            <input type="password" class="form-control" name="confirmation" id="confirmation"
                   title="@lang('content.confirmation')"
                   placeholder="@lang('content.confirmation')" value="{{ old('confirmation', '') }}"
                   autocomplete="off">

        </div>
    </div>
</div>

<button type="submit" class="btn btn-success">{{ $submit_button or trans('content.send') }}</button>
