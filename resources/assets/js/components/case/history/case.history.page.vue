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
            <div class="row">
                <div class="col-sm-12">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CaseAutosuggest from "../case.autosuggest";

    export default {
        name: "case-history-page",
        components: {
            CaseAutosuggest,
        },
        data() {
            return {
                selected: '',
            }
        },
        methods: {
            onCaseSelected (event) {
                console.log(event);
            }
        }
    }
</script>

<style scoped>

</style>