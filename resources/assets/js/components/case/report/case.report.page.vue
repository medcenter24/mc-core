<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="case-report-container">
        <div class="container white">
            <case-autosuggest
                    v-on:select="onCaseSelected"
            ></case-autosuggest>
            <div class="row">
                <div class="col-sm-12">
                    <case-report-html
                            ref="caseReportHtml"
                    ></case-report-html>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CaseReportContent from './case.report.content.vue'
    import CaseAutosuggest from "../case.autosuggest";

    export default {
        name: "case-report-page",
        components: {
            CaseAutosuggest,
            CaseReportHtml: CaseReportContent,
        },
        methods: {
            onCaseSelected(ref) {
                this.$refs.caseReportHtml.loadReport(ref);
            }
        }
    }
</script>

<style scoped>

</style>