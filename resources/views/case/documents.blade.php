<table>
    <!-- Insurance -->
    <!-- Passport -->
    @foreach($report->b64Docs() as $b64)
    <tr>
        <td>
            <img src="{{ $b64 }}" alt="Document">
        </td>
    </tr>
    @endforeach
</table>
