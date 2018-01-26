<!--
  - Copyright (c) 2018.
  -
  - @author Alexander Zagovorichev <zagovorichev@gmail.com>
  -->

<template>
    <div class="case-autosuggest-component">
        <div class="row">
            <div class="col-sm-6">
                <vue-autosuggest
                        :suggestions="filteredOptions"
                        :onSelected="clickHandler"
                        :inputProps="inputProps"
                        :limit="limit"
                />
            </div>
            <div class="col-sm-6">
                <div class="lead" v-if="selected">You have selected: '{{selected}}'</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { VueAutosuggest } from 'vue-autosuggest'
    import CasesProvider from '../../providers/cases.provider'

    export default {
        name: "case-autosuggest",
        components: {
            VueAutosuggest
        },
        data() {
            return {
                selected: '',
                options: [{
                    data: ['']
                }],
                filteredOptions: [],
                inputProps: {
                    id: "autosuggest__input",
                    onInputChange: this.onInputChange,
                    placeholder: "Type case id or referral number"
                },
                limit: 10
            }
        },
        methods: {
            clickHandler(option) {
                this.selected = option.item;
                this.$emit('select', this.selected);
            },
            onInputChange(text) {
                if (text === '' || text === undefined) {
                    return;
                }

                CasesProvider.search({params: { query: text }})
                    .then(response => {
                        this.filteredOptions = [{
                            data: response.data
                        }];
                    })
                    .catch(err => console.log(err));
            }
        }
    }
</script>

<style scoped>
    #autosuggest__input {
        outline: none;
        position: relative;
        display: block;
        font-family: monospace;
        font-size: 20px;
        border: 1px solid #616161;
        padding: 10px;
        width: 100%;
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    #autosuggest__input.autosuggest__input-open {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .autosuggest__results-container {
        position: relative;
        width: 100%;
    }

    .autosuggest__results {
        font-weight: 300;
        margin: 0;
        position: absolute;
        z-index: 10000001;
        width: 100%;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        background: white;
        padding: 0px;
    }

    .autosuggest__results ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .autosuggest__results .autosuggest__results_item {
        cursor: pointer;
        padding: 15px;
    }

    #autosuggest ul:nth-child(1) > .autosuggest__results_title {
        border-top: none;
    }

    .autosuggest__results .autosuggest__results_title {
        color: gray;
        font-size: 11px;
        margin-left: 0;
        padding: 15px 13px 5px;
        border-top: 1px solid lightgray;
    }

    .autosuggest__results .autosuggest__results_item:active,
    .autosuggest__results .autosuggest__results_item:hover,
    .autosuggest__results .autosuggest__results_item:focus,
    .autosuggest__results .autosuggest__results_item.autosuggest__results_item-highlighted {
        background-color: #ddd;
    }
</style>