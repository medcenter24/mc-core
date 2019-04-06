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
    <div class="case-history-container">
        <div class="container white">
            <case-autosuggest
                v-on:select="onCaseSelected"
            ></case-autosuggest>
            <div class="row" v-if="history.length">
                <div class="col-sm-12">
                    <pre>
                        <code>
                            <div class="preview-content" v-html="history"></div>
                        </code>
                    </pre>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CasesProvider from "../../../providers/cases.provider"
    import CaseAutosuggest from "../case.autosuggest";

    export default {
        name: "case-history-page",
        components: {
            CaseAutosuggest,
        },
        data() {
            return {
                history: '',
            }
        },
        methods: {
            onCaseSelected (refNum) {
                CasesProvider
                    .getHistory(refNum)
                    .then(response => this.history = JSON.stringify(response.data, null, '   |'))
                    .catch(err => console.error(err));
            }
        }
    }
</script>

<style scoped>

</style>