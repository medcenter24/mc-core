======================
<b>@lang('content.new_case_created')</b>

@if ($doctorAccident && $doctorAccident->accident)
<b>@lang('content.npp_sign') {{ $doctorAccident->accident->id ?? __('content.id_not_provided') }}{{ $doctorAccident->accident->ref_num ? ' ['.$doctorAccident->accident->ref_num.']' : '' }}</b>, {{ $doctorAccident->accident->assistant_ref_num ?? __('content.assistant_ref_num_not_provided') }}

@if ($doctorAccident->accident->patient)
<b>{{ $doctorAccident->accident->patient->name ?? __('content.patient_name_not_provided') }}</b>, {{ $doctorAccident->accident->patient->birthday ? $doctorAccident->accident->patient->birthday->format(config('date.dateFormat')) : __('content.birthday_not_provided') }}
@else
<b>@lang('content.patient_not_provided')</b>
@endif
@lang('content.address'): <code>{{ $doctorAccident->accident->city ? $doctorAccident->accident->city->title : __('content.city_not_provided') }}, {{ $doctorAccident->accident->contacts ?? __('content.contacts_not_provided') }}</code>
@lang('content.symptoms'): <pre>{{ $doctorAccident->accident->symptoms ?? __('content.symptoms_not_provided') }}</pre>
<a href="{{ config('api.originDoctor') }}/accidents/{{ $doctorAccident->accident->id }}">@lang('content.go_to_accident_page')</a>
@else
<b>@lang('content.accident_not_provided')</b>
@endif