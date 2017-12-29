<table style="font-family: Arial, Verdana, Tahoma; font-size: 10px; -webkit-print-color-adjust: exact;">
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
        <td valign="top">
            <!-- Header with logo and company info -->
            <table>
                <tr>
                    <td>
                        <img src="{{ $report->companyLogoUrl() }}"
                             height="100"
                             alt="{{ strip_tags($report->companyTitle()) }}">
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

        <!-- Diagnostics -->
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

        <!-- Services -->
            <table style="background-color: #dbe5f1; width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="font-size: 9px;">
                        <b><i>{{ $report->serviceTitle() }}</i></b>
                    </td>
                    <td style="font-size: 10px;">
                        <b>{{ $report->serviceDescription() }}</b>
                    </td>
                </tr>
                @foreach($report->services() as $service)
                    <tr style="font-size: 11px;">
                        <td>
                            <b>{{ $service->title }}</b>
                        </td>
                        <td>
                            <b>{{ $service->price }}</b>
                        </td>
                    </tr>
                @endforeach
            </table>

            <table style="background-color: #dbe5f1; width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="font-size: 8px">
                        <b><i>{{ $report->serviceFooterDescription() }}</i></b>
                    </td>
                    <td style="font-size: 9px">
                        <b>{{ $report->serviceFooterTitle() }}</b>
                    </td>
                    <td style="font-size: 12px">
                        <b>{{ $report->totalAmount() }}</b>
                    </td>
                </tr>
            </table>

        <!-- Date and Location -->
            <table style="background-color: #dbe5f1; width: 100%;">
                <tr>
                    <td style="font-size: 9px; white-space: nowrap;">
                        <b><i>{!! $report->visitInfoTitle() !!}</i></b>
                    </td>
                    <td style="font-size: 7px; width: 90%;" valign="top">
                        [{{ $report->visitTime() }}]
                    </td>
                    <td valign="top" style="font-size: 10px; color: #365f91; text-transform: uppercase; white-space: nowrap">
                        <b>{{ $report->visitDate() }}, {{ $report->city() }}</b>
                    </td>
                </tr>
            </table>

        <!-- Footer -->
            <table style="width: 100%">
                <tr>
                    <!-- Bank -->
                    <td>
                        <table style="font-size: 8px">
                            <tr>
                                <td colspan="2">
                                    <b>{{ $report->bankTitle() }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    {{ $report->bankAddress() }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    {{ $report->bankDetailsLabel() }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ $report->bankHolderLabel() }}
                                </td>
                                <td style="font-size: 10px">
                                    <b>{{ $report->bankHolder() }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ $report->bankIbanLabel() }}
                                </td>
                                <td style="font-size: 10px">
                                    <b>{{ $report->bankIban() }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ $report->bankSwiftLabel() }}
                                </td>
                                <td style="font-size: 10px">
                                    <b>{{ $report->bankSwift() }}</b>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Stamp -->
                    <td>
                        <img src="data:image/png;base64,{{ $report->stampUrl() }}"
                             height="100"
                             alt="{{ strip_tags($report->companyTitle()) }}">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
