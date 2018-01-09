<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
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