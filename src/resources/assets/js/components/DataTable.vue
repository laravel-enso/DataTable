<template>

    <div :class="'box box-' + headerClass">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-table"></i>
                <slot name="data-table-title"></slot>
            </h3>
            <div class="box-tools pull-right">
                <button type="button"
                    class="btn btn-box-tool btn-sm"
                    @click="getData()">
                    <i class="fa fa-refresh"></i>
                </button>
                <button class="btn btn-box-tool btn-sm" data-widget="collapse">
                    <i class="fa fa-minus">
                    </i>
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table :id="'table-' + _uid" :class="tableClass"
                width="100%">
                <thead>
                    <tr>
                        <th v-for="label in header" :class="'text-' + headerAlign">
                            {{ label }}
                        </th>
                    </tr>
                </thead>
                <tbody :class="'text-' + bodyAlign"></tbody>
                <tfoot v-if="hasTotals">
                <tr>
                    <td v-for="i in header.length" class="text-center">
                        <span v-if="i == 2" class="totals"><slot name="data-table-total">Totals</slot></span>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="overlay" v-if="loading">
            <i class="fa fa-spinner fa-spin spinner-custom" ></i>
        </div>
        <modal :show="showModal" @cancel-action="showModal = false; deleteRoute = null" @commit-action="deleteModel()">
            <span slot="modal-body"><slot name="modal-body"></slot></span>
            <span slot="modal-cancel"><slot name="modal-cancel"></slot></span>
            <span slot="modal-ok"><slot name="modal-ok"></slot></span>
        </modal>
    </div>

</template>

<script>

    export default {
        props: {
            source: {
                type: String,
                required: true
            },
            headerClass: {
                type: String,
                default: 'primary'
            },
            extraFilters: {
                type: Object,
            },
            customParams: {
                type: Object,
            },
            intervalFilters: {
                type: Object,
            }
        },

        computed: {
            hasTotals() {
                return Object.keys(this.totals).length > 0;
            }
        },

        data (self = this) {
            return {
                showModal: false,
                loading: false,
                hasEditor: false,
                dtHandle: null,
                dtEditorHandle: null,
                header: {},
                totals: {},
                actionButtons: {},
                headerAlign: null,
                bodyAlign: null,
                tableClass: null,
                deleteRoute: null,
                editedCellValue: null,
                firstColumn: {
                    render(data, type, row, meta) {
                        return meta.settings._iDisplayStart + meta.row + 1;
                    },
                    responsivePriority: 1,
                    searchable: false,
                    orderable: false,
                },
                lastColumn: {
                    data: 'DT_RowId',
                    render(data, type, row, meta) {
                        return self.getActionButtons(data);
                    },
                    responsivePriority: 1,
                    searchable: false,
                    orderable: false,
                },
                tableOptions: {
                    dom: 'lfrtip',
                    columns: [],
                    ajax: {
                        url: this.source + '/getTableData',
                        type: 'GET',
                        data: {
                            totals() { return JSON.stringify(self.totals); },
                            extraFilters() { return JSON.stringify(self.extraFilters); },
                            intervalFilters() { return JSON.stringify(self.intervalFilters); },
                            customParams () { return JSON.stringify(self.customParams); }
                        }
                    },
                    drawCallback () {
                        for (let index in self.totals) {
                            self.drawColumnTotal(this.api(), index, self.dtHandle.context[0].json.totals[index]);
                        }

                        $('table .delete-model').off('click').on('click', event => {
                            self.deleteRoute = $(event.currentTarget).data('route');
                            self.showModal = true;
                        });

                        if(self.actionButtons.custom) {
                            self.actionButtons.custom.forEach(button => {
                                $('table a.' + button.event.toLowerCase()).off('click').on('click', function() {
                                    self.$emit($(this).data('event'), $(this).data('id'));
                                });
                            });
                        }
                    },
                },
                editorOptions: {
                    ajax: {
                        url: this.source + '/setTableData',
                        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    },
                    table: '#table-' + this._uid,
                    fields: []
                }
            }
        },

        watch: {
            'extraFilters': {
                handler: 'getData',
                deep: true
            },
            'intervalFilters': {
                handler: 'getData',
                deep: true
            },
            'customParams': {
                handler: 'getData',
                deep: true
            }
        },

        methods: {
            initTable() {
                axios.get(this.source + '/initTable').then(response => {
                    this.processInitData(response.data);
                }).catch(error => {
                    if (error.response.data.level) {
                        toastr[error.response.data.level](error.response.data.message);
                    }
                }).then(() => {
                    this.mountDataTable();
                });
            },
            processInitData(data) {
                this.setStyle(data);
                this.header = data.header;
                this.tableOptions.columns = data.columns;
                this.computeExtraColumns(data);
                this.totals = data.totals || {};
                this.computeRender(data);
                this.computeEditor(data);
            },
            setStyle(data) {
                this.tableClass = data.tableClass;
                this.headerAlign = data.headerAlign;
                this.bodyAlign = data.bodyAlign;
                this.tableOptions.dom = data.dom || this.tableOptions.dom;
            },
            computeExtraColumns(data) {
                if (data.actionButtons) {
                    this.tableOptions.columns.push(this.lastColumn);
                    this.header.push(data.actionButtons.label);
                    this.actionButtons = data.actionButtons;
                }

                if (data.crtNo) {
                    this.tableOptions.columns.unshift(this.firstColumn);
                    this.header.unshift(data.crtNo);
                }
            },
            computeRender(data) {
                if (data.render) {
                    let renderFunction,
                        self = this;

                    data.render.forEach(index => {
                        renderFunction = (data, type, row, meta) => {
                            return self.$parent.customRender(self.tableOptions.columns[index].data, data, type, row, meta);
                        };

                        self.tableOptions.columns[index] = Object.assign({}, { render: renderFunction }, self.tableOptions.columns[index]);
                    });
                }
            },
            computeEditor(data) {
                let self = this;
                this.hasEditor = data.editable && data.editable.length > 0;
                if (!this.hasEditor) {
                    return false;
                }

                data.editable.forEach(column => {
                    self.editorOptions.fields.push(column);
                });
            },
            mountDataTable() {
                if (this.hasEditor) {
                    this.initEditor();
                };

                this.dtHandle = $('#table-' + this._uid).DataTable(this.tableOptions);
                this.addProcessingListener();
            },
            addProcessingListener() {
                let self = this;

                this.dtHandle.on('processing.dt', (e, settings, processing) => {
                    self.updateLoadingState(processing);
                });
            },
            initEditor() {
                this.dtEditorHandle = new $.fn.dataTable.Editor(this.editorOptions);
                this.addClickListener();
                this.addSelectOnFocusListener();
                this.addBlurListener();
                this.addPostSubmitListener();
            },
            addClickListener() {
                let self = this;

                $('#table-' + this._uid).on('click', 'tbody tr td.editable', function() {
                    self.editedCellValue = self.dtHandle.cell(this).data();
                    self.dtEditorHandle.inline(this);
                });
            },
            addSelectOnFocusListener () {
                let self = this;

                $('input', this.dtEditorHandle.node()).on('focus', function() {
                    $(this).val(self.editedCellValue);
                    this.select();
                });
            },
            addBlurListener() {
                let self = this;

                $('input', this.dtEditorHandle.node()).on('blur', function() {
                    $('div.DTE_Field_InputControl').removeClass('has-error');
                    self.editedCellValue = null;
                });
            },
            addPostSubmitListener() {
                this.dtEditorHandle.on('postSubmit', function(event, response) {
                    if (response.level) {
                        $('div.DTE_Field_InputControl').addClass('has-error');
                        return toastr[response.level](response.message);
                    }

                    $('div.DTE_Field_InputControl').removeClass('has-error');
                    toastr.success(response.message);
                });
            },
            updateLoadingState(processing) {
                return (this.loading = processing) ? NProgress.start() : NProgress.done();
            },
            drawColumnTotal(api, column, total) {
                $(api.table().column(column).footer()).html('<b>' +
                    this.$options.filters.numberFormat(parseFloat(total).toFixed(2)) + '</b>');
            },
            getActionButtons(data) {
                return '<span style="display: inline-flex">' +
                    this.getCustomActionButtons(data) +
                    this.getStandardActionButtons(data) +
                    '</span>';
            },
            getCustomActionButtons(data) {
                let buttons = '';

                this.actionButtons.custom.forEach(action => {
                    buttons += '<a class="margin-left-xs ' + action.event.toLowerCase() + '"data-id="' +
                        data + '" data-event="' + action.event + '"><i class="btn btn-xs ' + action.class + '"></i></a>';
                });

                return buttons;
            },
            getStandardActionButtons(data) {
                return ''
                + (this.actionButtons.standard.show ? '<a class="btn btn-xs btn-success margin-left-xs" href="' + this.source + '/' + data + '"><i class="fa fa-eye"></i></a>' : '')
                + (this.actionButtons.standard.edit ? '<a class="btn btn-xs btn-warning margin-left-xs" href="' + this.source + '/' + data + '/edit"><i class="fa fa-pencil-square-o"></i></a>' : '')
                + (this.actionButtons.standard.download ? '<a class="btn btn-xs btn-success margin-left-xs" href="' + this.source + '/' + 'download/' + data + '"><i class="fa fa-cloud-download"></i></a>' : '')
                + (this.actionButtons.standard.delete ? '<a class="btn btn-xs btn-danger margin-left-xs delete-model" data-route="' + this.source + '/' + data + '"><i class="fa fa-trash-o"><i class=""></i></a>' : '');
            },
            getData: _.debounce(function() {
                if (!this.dtHandle) {
                    return this.initTable();
                }

                let pageNumber = this.dtHandle.page.info().page;
                this.dtHandle.page(pageNumber).draw('page');
            }, 500),
            deleteModel() {
                this.showModal = false;

                axios.delete(this.deleteRoute).then(response => {
                    this.dtHandle.ajax.reload();
                    toastr.success(response.data.message);
                }).catch(error => {
                    if (error.response.data.level) {
                        toastr[error.response.data.level](error.response.data.message);
                    }
                });
            }
        },

        mounted() {
            this.initTable();
        },

        beforeDestroy() {
            this.dtHandle.destroy();
        }
    };

</script>

<style>

    .DTE_Field_Input input {
        height: 26px;
    }

    tfoot > tr > td > span.totals {
        font-weight: bold;
    }

    td.editable {
        color: #00776c;
    }

</style>