<!--
  - Copyright (c) 2017.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="case-report-html">{{ reportPage }}</div>
</template>

<script>
    import CasesProvider from '../../../providers/cases.provider'

    export default {
        name: "case-report-html",
        data (){
            return {
                reportPage: ''
            };
        },
        methods: {
            loadReport (refNum) {
                CasesProvider.getReportHtml(refNum).then(
                    html => this.reportPage = html
                ).catch(err => console.error(err));
            }
        }
    }
</script>

<style scoped>

</style>