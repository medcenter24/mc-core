<style>
    .floating-line {
        padding-top: 230px;
    }
    @media print {
        .floating-line {
            padding-top: 0;
        }
        .report-data {
            page-break-after: always;
        }
    }
</style>

<table style="font-family: 'Arial Rounded MT Bold', Arial, Verdana, sans-serif;
    font-size: 10px;
    -webkit-print-color-adjust: exact;">
    <tr>
        @if ($report->hasFloatingLine())
            <td>
                <table>
                    <tr><td height="0"></td></tr>
                    <tr>
                        <!-- Left floating line -->
                        <td style="display: flex;
                        transform: rotate(-180deg);
                        writing-mode: tb-lr;
                        writing-mode: vertical-lr;
                        font-size: 7px;
                        padding-right: 10px;
                        min-height: 800px;
                        "
                            class="floating-line"
                            text-rotate="90">
                            {{ $report->floatingLine() }}
                        </td>
                    </tr>
                </table>
            </td>
        @endif
        <td valign="top">
            <!-- Header with logo and company info -->
            <table>
                <tr>
                    <td>
                        <img src="data:image/jpg;base64, {{ $report->companyLogoB64() }}"
                             height="100"
                             alt="{{ strip_tags($report->companyTitle()) }}">
                    </td>
                    <td style="padding-left: 10px">
                        <table>
                            <tr>
                                <td style="color: #333333;">
                                    {!! $report->companyDescription() !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! $report->companyTitle() !!}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px;color: #4b4b4b;">
                                    <b><i>{!! $report->companyContacts() !!}</i></b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Company Info -->
            <table style="font-size: 9px; padding-left: 10px;">
                <tr>
                    <td>
                        <i>{{ $report->companyInfo() }}</i>
                    </td>
                </tr>
            </table>

            <!-- Title -->
            <table style="font-size: 16px; width: 100%;text-align: center;color: #4b4b4b;">
                <tr>
                    <td align="center">
                        <b>{{ $report->title() }}</b>
                    </td>
                </tr>
            </table>

            <!-- Assistance -->
            <table style="width: 100%;background-color: #dbe5f1;margin-bottom: 5px;">
                <tr>
                    <td style="font-size: 10px">
                        <i>{{ $report->assistantLabel() }}</i>
                    </td>
                </tr>
                <tr>
                    <td style="color: #365f91;font-size: 16px;">
                        <b><i>{{ $report->assistantTitle() }}</i></b>
                        <i style="font-size: 12px;">{{ $report->assistantAddress() }}</i>
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
                    <td style="font-size: 16px;">
                        <b>{{ $report->patientName() }}</b>
                        @if ($report->patientHasBirthDate())
                            <b>, {{ $report->patientBirthday() }}</b>
                        @endif
                    </td>
                    <td style="font-size: 14px;">
                        {{ $report->assistantRefNum() }}
                    </td>
                    <td style="font-size: 14px; color: #365f91">
                        {{ $report->refNum() }}
                    </td>
                </tr>
            </table>

            <!-- Symptoms -->
            <table>
                <tr>
                    <td style="font-size: 12px;">
                        <b><i>{{ $report->symptomsLabel() }}</i></b>
                        {{ $report->symptoms() }}
                    </td>
                </tr>
            </table>

            <!-- Surveys -->
            <table>
                <tr>
                    <td style="font-size: 12px;">
                        <b><i>{{ $report->surveysLabel() }}</i></b>
                        {{ $report->surveys() }}
                    </td>
                </tr>
            </table>

            <!-- Investigation -->
            @if ($report->hasInvestigation())
                <table>
                    <tr>
                        <td style="font-size: 12px;">
                            <b><i>{{ $report->investigationLabel() }}</i></b>
                            {{ $report->investigation() }}
                        </td>
                    </tr>
                </table>
            @endif

        <!-- Diagnose -->
            @if ($report->hasDiagnose())
                <table>
                    <tr>
                        <td style="font-size: 12px;">
                            <b><i>{{ $report->diagnoseLabel() }}</i></b>
                            {{ $report->diagnose() }}
                        </td>
                    </tr>
                </table>
            @endif

        <!-- Diagnostics -->
            <table style="width: 100%;">
                <tr>
                    <td align="left">
                        <b style="font-size: 12px">{{ $report->diagnosticTitle() }}</b>
                    </td>
                    <td align="right">
                        <b><i>{{ $report->diagnosticDescription() }}</i></b>
                    </td>
                </tr>
            </table>
            <table style="font-size: 14px; background-color: #dbe5f1; color: #365f91; width: 100%; text-transform: uppercase;">
                @foreach($report->diagnostics() as $diagnostic)
                    <tr>
                        <td>
                            <b>{{ $diagnostic->title }}, {{ $diagnostic->disease_code }}</b>
                        </td>
                    </tr>
                @endforeach
            </table>

            <!-- Doctor -->
            @if ($report->hasDoctor())
                <table style="font-size: 12px; width: 100%">
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
                    <td style="font-size: 12px;">
                        <b><i>{{ $report->serviceTitle() }}</i></b>
                    </td>
                    <td style="font-size: 12px;">
                        <b>{{ $report->serviceDescription() }}</b>
                    </td>
                </tr>
                @foreach($report->services() as $service)
                    <tr>
                        <td style="font-size: 14px;">
                            <b>{{ $service->title }}</b>
                        </td>
                        <td style="font-size: 14px;">
                            <b>{{ $service->price }}</b>
                        </td>
                    </tr>
                @endforeach
            </table>

            <!-- Total -->
            <table style="background-color: #dbe5f1; width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="font-size: 10px">
                        <i>{{ $report->serviceFooterDescription() }}</i>
                    </td>
                    <td style="font-size: 12px">
                        <b>{{ $report->serviceFooterTitle() }}</b>
                    </td>
                    <td style="font-size: 14px">
                        <b>{{ $report->totalAmount() }} {{ $report->currency() }}</b>
                    </td>
                </tr>
            </table>

            <!-- Date and Location -->
            <table style="background-color: #dbe5f1; width: 100%;margin-bottom: 5px">
                <tr>
                    <td style="font-size: 12px;">
                        <b><i>{{ $report->visitInfoTitle() }}</i></b>
                        <span style="font-size: 10px">[{{ $report->visitTime() }}]</span><br>
                        <b><i>{{ $report->visitInfoPlace() }}</i></b>
                        <span style="font-size: 10px">[{{ $report->visitCountry() }}]</span>
                    </td>
                    <td valign="bottom" align="right" style="font-size: 14px; color: #364e80; text-transform: uppercase;">
                        <b>{{ $report->visitDate() }}, {{ $report->city() }}</b>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <table style="width: 100%;font-size: 8px">
                <tr>
                    <!-- Bank -->
                    <td>
                        <table style="font-size: 10px">
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
                        <img src="data:image/jpeg;base64, {{ $report->stampB64() }}"
                             height="100"
                             alt="{{ strip_tags($report->companyTitle()) }}">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if ($report->hasDocuments())
    <div class="report-data"></div>
    <table>
        <!-- Insurance -->
        <!-- Passport -->
        @foreach($report->b64Docs() as $doc)
            <tr>
                <td>
                    <img src="data:image/jpg;base64, {{ $doc['b64'] }}" alt="{{ $doc['title'] }}">
                </td>
            </tr>
        @endforeach
    </table>
@endif
