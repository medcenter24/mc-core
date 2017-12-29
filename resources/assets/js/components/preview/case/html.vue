<!--
  - Copyright (c) 2017.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="case-report-html">
        <div class="row" v-if="reportPage">
            <div class="col-sm-12">
                <button v-on:click="reportPrint()">Print</button>
                <button v-on:click="reportPdf()">PDF</button>
            </div>
        </div>
        <div class="preview-content" v-html="reportPage"></div>
    </div>
</template>

<script>
    import CasesProvider from '../../../providers/cases.provider'

    export default {
        name: "case-report-html",
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
                let WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
                WinPrint.document.write(this.reportPage);
                WinPrint.document.close();
                WinPrint.focus();
                WinPrint.print();
                WinPrint.close();
                window.focus();
            },
            reportPdf() {
                CasesProvider.downloadPdf(this.refNum);
            }
        }
    }
</script>

<style scoped>

</style>