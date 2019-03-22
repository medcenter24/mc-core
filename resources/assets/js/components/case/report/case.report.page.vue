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
    <div class="case-report-container">
        <div class="container white p-2">
            <case-autosuggest
                    v-on:select="onCaseSelected"
            ></case-autosuggest>
            <div class="row mt-2">
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