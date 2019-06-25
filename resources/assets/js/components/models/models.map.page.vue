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
    <div class="container white p-2 models-map-container">
        <ui-tree-component
            :items="items"
            v-on:select="selected"
        ></ui-tree-component>
    </div>
</template>

<script>
    import UiTreeComponent from '../ui/tree';
    import ModelsProvider from '../../providers/models.provider';

    export default {
        name: "models-map",
        components: {
            UiTreeComponent
        },
        data () {
            /*
            Example
            return {
                items: [
                    {
                        id: 1,
                        name: 'Model 1'
                    },
                    {
                        id: 2,
                        name: 'Model 2',
                        visible: true,
                        children: [
                            {
                                id: 3,
                                name: 'sub model 1'
                            },
                            {
                                id: 4,
                                name: 'sub model 2',
                                children: [
                                    {
                                        id: 5,
                                        name: 'sub sub model 1'
                                    },
                                    {
                                        id: 6,
                                        name: 'sub sub model 2'
                                    }
                                ]
                            },
                        ]
                    }
                ]
            };*/
            return {items: []};
        },
        created: function () {
            this.reloadModels();
        },
        methods: {
            reloadModels() {
                ModelsProvider.getModels().then(response => {
                    this.items = response.data.map(row => {
                        const children = [];
                        for (let param in row.params) {
                            children.push({
                                name: row.params[param],
                                type: 'param'
                            });
                        }

                        return {
                            name: row.id,
                            type: 'model',
                            children: children
                        };
                    });
                });
            },
            selected(model) {
                if (model.type === 'model') {
                    console.log('selected model');
                    this.higlightRelations(model);
                }
            },
            higlightRelations(model) {
                ModelsProvider.getRelations(model.name).then(response => {
                    console.log(response);
                });
            }
        }
    }
</script>

<style scoped>

</style>