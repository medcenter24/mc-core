<table style="font-size: 10px;">
    <tr>
        @if ($report->hasFloatingLine())
            <!-- Left floating line -->
                <td style="display: flex;
                    transform: rotate(-180deg);
                    writing-mode: tb-lr;
                    writing-mode: vertical-lr;
                    min-height: 800px">

                    {{ $report->floatingLine() }}
                </td>
        @endif
        <td>
            <!-- Header with logo and company info -->
            <table>
                <tr>
                    <td>
                        <img src="{{ $report->companyLogoUrl() }}" alt="{{ strip_tags($report->companyTitle()) }}">
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td style="font-size: 14px;">
                                    {!! $report->companyDescription() !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! $report->companyTitle() !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! $report->companyContacts() !!}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- Company Info -->
            <table style="font-size: 7px">
                <tr>
                    <td>
                        <i>{{ $report->companyInfo() }}</i>
                    </td>
                </tr>
            </table>

            <!-- Title -->
            <table style="font-size: 12px; width: 100%;">
                <tr>
                    <td align="center">
                        <b>{{ $report->title() }}</b>
                    </td>
                </tr>
            </table>

            <!-- Assistance -->
            <table style="width: 100%;background-color: #dbe5f1;">
                <tr>
                    <td style="font-size: 8px">
                        <i>{{ $report->assistantLabel() }}</i>
                    </td>
                </tr>
                <tr>
                    <td style="color: #365f91;font-size: 12px;">
                        <b>{{ $report->assistantTitle() }}</b>
                    </td>
                </tr>
            </table>

            <!-- Patient -->
            <table style="width: 100%;background-color: #dbe5f1;">
                <tr style="font-size: 8px">
                    <td>
                        <i>{{ $report->patientLabel() }}</i>
                    </td>
                    <td>
                        <i>{{ $report->assistantRefNumLabel() }}</i>
                    </td>
                    <td>
                        <i>{{ $report->refNumLabel() }}</i>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">
                        <b>{{ $report->patientName() }}</b>
                        @if ($report->patientHasBirthDate())
                            <b>, {{ $report->patientBirthday() }}</b>
                        @endif
                    </td>
                    <td style="font-size: 10px;">
                        {{ $report->assistantRefNum() }}
                    </td>
                    <td style="font-size: 10px; color: #365f91">
                        {{ $report->refNum() }}
                    </td>
                </tr>
            </table>

            <!-- Symptoms -->
            <table>
                <tr>
                    <td style="font-size: 10px;">
                        <b style="font-size: 9px;"><i>{{ $report->symptomsLabel() }}</i></b>
                        {{ $report->symptoms() }}
                    </td>
                </tr>
            </table>

            <!-- Surveys -->
            <table>
                <tr>
                    <td style="font-size: 10px;">
                        <b style="font-size: 9px;"><i>{{ $report->surveysLabel() }}</i></b>
                        {{ $report->surveys() }}
                    </td>
                </tr>
            </table>

            <!-- Investigation -->
            @if ($report->hasInvestigation())
                <table>
                    <tr>
                        <td style="font-size: 10px;">
                            <b style="font-size: 9px;"><i>{{ $report->investigationLabel() }}</i></b>
                            {{ $report->investigation() }}
                        </td>
                    </tr>
                </table>
            @endif

        <!-- Diagnose -->
            @if ($report->hasDiagnose())
                <table>
                    <tr>
                        <td style="font-size: 10px;">
                            <b style="font-size: 9px;"><i>{{ $report->diagnoseLabel() }}</i></b>
                            {{ $report->diagnose() }}
                        </td>
                    </tr>
                </table>
            @endif

        <!-- Diagnostico -->
            <table style="width: 100%;">
                <tr>
                    <td align="left">
                        {{ $report->diagnosticTitle() }}
                    </td>
                    <td align="right">
                        {{ $report->diagnosticDescription() }}
                    </td>
                </tr>
            </table>
            <table style="font-size: 10px; background-color: #dbe5f1; color: #365f91; width: 100%; text-transform: uppercase;">
                @foreach($report->diagnostics() as $diagnostic)
                <tr>
                    <td>
                        {{ $diagnostic->title }}, {{ $diagnostic->disease_code }}
                    </td>
                </tr>
                @endforeach
            </table>

        <!-- Doctor -->
            @if ($report->hasDoctor())
                <table style="font-size: 9px; width: 100%">
                    <tr>
                        <td align="right">
                            <b>{{ $report->doctorName() }}, {{ $report->doctorBoardNumSeparator() }} {{ $report->doctorBoardNum() }}</b>
                        </td>
                    </tr>
                </table>
            @endif
        </td>
    </tr>
</table>
