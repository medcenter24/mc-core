<table>
    <!-- Insurance -->
    <tr>
        <td>
            @foreach($report->insurance() as $insurance)

            @endforeach
        </td>
    </tr>

    <!-- Passport -->
    <tr>
        <td>
            @foreach($report->passport() as $k => $insurance)
                <img src="data:image/jpeg;base64, {{ $insurance }}" alt="">
            @endforeach
        </td>
    </tr>
</table>