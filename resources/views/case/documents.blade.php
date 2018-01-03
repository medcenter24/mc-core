<table>
    <!-- Insurance -->
    <!-- Passport -->
    @foreach($report->b64Docs() as $b64)
    <tr>
        <td>
            <img src="data:image/jpeg;base64, {{ $b64 }}" alt="Document">
        </td>
    </tr>
    @endforeach
</table>
