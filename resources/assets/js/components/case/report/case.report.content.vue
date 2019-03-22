<!--
  - This program is free software; you can redistribute it and/or
  - modify it under the terms of the GNU General Public License
  - as published by the Free Software Foundation; under version 2
  - of the License (non-upgradable).
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  - You should have received a copy of the GNU General Public License
  - along with this program; if not, write to the Free Software
  - Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  -
  - Copyright (c) 2019 (original work) MedCenter24.com;
  -->

<template>
    <div class="case-report-html">
        <div class="row" v-if="reportPage">
            <div class="col-sm-12">
                <button class="btn" v-on:click="reportPrint()">Print</button>
                <button class="btn" v-on:click="reportPdf()">PDF</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="preview-content" v-html="reportPage"></div>
            </div>
        </div>
        <iframe id="printf" name="printf" style="display: none;"></iframe>
    </div>
</template>

<script>
    import CasesProvider from '../../../providers/cases.provider'

    export default {
        name: "case-report-content",
        data (){
            return {
                reportPage: '',
                refNum: ''
            };
        },
        methods: {
            loadReport (refNum) {
                this.refNum = refNum;
                CasesProvider
                    .getReportHtml(refNum)
                    .then(response => this.reportPage = response.data)
                    .catch(err => console.error(err));
            },
            reportPrint() {
                let newWin = window.frames["printf"];
                newWin.document.write('<body onload="window.print()">' + this.reportPage + '</body>');
                newWin.document.close();

                /*let WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
                WinPrint.document.write(this.reportPage);
                WinPrint.document.close();
                WinPrint.focus();
                WinPrint.print();
                WinPrint.close();
                window.focus();*/
            },
            reportPdf() {
                CasesProvider.downloadPdf(this.refNum);
            }
        }
    }
</script>

<style scoped>

</style>