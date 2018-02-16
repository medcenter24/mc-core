{{ $doctorAccident->accident->ref_num }}, {{ $doctorAccident->accident->assistant_ref_num }}
<b>{{ $doctorAccident->accident->patient->name }}</b>, {{ $doctorAccident->accident->patient->birthday->format(config('date.dateFormat')) }}
<code>{{ $doctorAccident->accident->city->title }}, {{ $doctorAccident->accident->contacts }}</code>
<pre>{{ $doctorAccident->accident->symptoms }}</pre>