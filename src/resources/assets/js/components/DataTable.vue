<template>

    <div :class="'box box-' + headerClass">
        <div class="box-header with-border" :class="{'draggable': draggable}">
            <h3 class="box-title">
                <i class="fa fa-table"></i>
                <slot name="data-table-title"></slot>
            </h3>
            <div class="box-tools pull-right">
                <button type="button"
                    class="btn btn-box-tool btn-sm"
                    @click="getData">
                    <i class="fa fa-refresh"></i>
                </button>
                <button class="btn btn-box-tool btn-sm" data-widget="collapse">
                    <i class="fa fa-minus">
                    </i>
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table :class="tableClass"
                :id="'table-' + _uid"
                width="100%">
                <thead>
                <tr>
                    <th v-for="label in header">
                        {{ label }}
                    </th>
                </tr>
                </thead>
                <tfoot v-if="hasTotals">
                <tr>
                    <td v-for="i in header.length">
                        <span v-if="i == 2" style="font-weight: bold"><slot name="data-table-total">Total</slot></span>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="overlay" v-if="loading">
            <i class="fa fa-spinner fa-spin spinner-custom" ></i>
        </div>
        <modal :show="showModal" @cancel-action="showModal = false" @commit-action="deleteModel">
            <span slot="modal-body"><slot name="modal-body"></slot></span>
            <span slot="modal-cancel"><slot name="modal-cancel"></slot></span>
            <span slot="modal-ok"><slot name="modal-ok"></slot></span>
        </modal>
    </div>

</template>

<!--

* extra filters should be an object where the first level is the table name, and the next level (property name) is the
* column name, and the property values are the values we're using for filtering.
* The structure is important because the object is used in the Back-End mechanism of the component.
* Note 1: this object is reactive, so if it is changed, the table is reloaded, with the new, filtered, data

 extraFilters: {
            proposals: {
                offer_sent: '',
                has_discounted_price: '',
                ordered: '',
                ready_to_ship: '',
                shipped: '',
                delivered: ''
            }
        }

* the custom params should be a json object with whatever format you need it to. It is meant as a mechanism
* to pass parameters from the FE to the BE, params self are to be used in a non standard fashion. They may be used
* anywhere in the controller (e.g. in the getTableQuery method or in any method of the DataTablesBuilder trait
* by overriding it and using the params )
* The structure is NOT important because the object is NOT implicitly used in the Back-End mechanism of the component.
* As opposed to the extra filters which should have the documented structure and which are applied as filters, on the
* main query, the custom query params may be used when composing the main query.
* The two, 'custom params' & 'extra filters' while similar, are not interchangeable.
* Note 2: this object is IS reactive, so if it is changed, the table is IS reloaded

  customParams: {
            orders: {
                dispatched: ''
            }
        }

* the interval filters should have the structure presented below and are meant to be used to filter the results
* loaded in the datatable using a min-max interval, such as dates. The comparison is inclusive (<=, >=).
* The structure is important because the object is used in the Back-End mechanism of the component.
* Note 3: dbDateFormat is REQUIRED if the filter values are dates, and the given format has to match the format used for
* the column in the DB
* Note 4: this object is reactive, so if it is changed, the table is reloaded, with the new, filtered, data

"intervalFilters": {
   "table":{
      "column":{
         "min":"value",
         "max":"value",
         "dbDateFormat": "Y-m-d"
      }
   }
}

* Note 5: the 'tableOptions.language' and 'tableOptions.stateSave' attributes should either be updated or
* you need to make sure self 1) the Preferences object exists and 2) the path to the translation file is correct

-->

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
            draggable: {
                type: Boolean,
                default: false
            },
            extraFilters: {
                type: Object,
                default: null
            },
            customParams: {
                type: Object,
                default: null
            },
            intervalFilters: {
                type: Object,
                default: null
            }
        },
        computed: {
            hasTotals: function() {
                return Object.keys(this.tableOptions.ajax.data.totals).length;
            }
        },
        data: function (self = this) {
            return {
                showModal: false,
                deleteRoute: null,
                dtHandle: null,
                dtEditorHandle: null,
                editedCellValue: null,
                loading: false,
                is_collapsed: this.collapsed,
                hasEditor: false,
                tableClass: null,
                header: {},
                firstColumn: {
                    render: function(data, type, row, meta) {
                        return meta.settings._iDisplayStart + meta.row + 1;
                    },
                    responsivePriority: 1,
                    searchable: false,
                    orderable: false,
                    class: 'dt-center'
                },
                lastColumn: {
                    data: 'DT_RowId',
                    name: this.source + '.id',
                    render: function(data, type, row, meta) {
                        let actionButtons = self.dtHandle.context[0].json.actionButtons;
                        return self.buildActionButtons(actionButtons, data);
                    },
                    responsivePriority: 1,
                    searchable: false,
                    orderable: false,
                    class: 'dt-center'
                },
                tableOptions: {
                    dom: 'lrftip',
                    pageLength: 10,
                    //see 'Note 5' above
                    language: {"sUrl": "/libs/datatables-lang/" + Preferences.lang + ".json"},
                    //see 'Note 5' above
                    stateSave: Preferences.dtStateSave,
                    lengthChange: true,
                    lengthMenu: [10, 15, 20, 25, 30],
                    autoWidth: true,
                    pagingType: "simple",
                    filter: true,
                    stateDuration: 60 * 60 * 24 * 90,
                    ajax: {
                        url: this.source + '/getTableData',
                        type: 'GET',
                        data: {
                            totals: [],
                            extraFilters: function() {
                                return JSON.stringify(self.extraFilters);
                            },
                            intervalFilters: function() {
                                return JSON.stringify(self.intervalFilters);
                            },
                            customParams: function () {
                                return JSON.stringify(self.customParams);
                            }
                        }
                    },
                    responsive: true,
                    serverSide: true,
                    columnDefs: [],
                    columns: [],
                    drawCallback: function () {
                        let api = this.api();
                        let totals = self.dtHandle.context[0].json.totals;

                        Object.keys(totals).forEach(function(key) {
                            self.drawColumnTotal(api, key, totals[key]);
                        });

                        $('table .delete-model').off('click').on('click', function() {
                            self.deleteRoute = $(this).data('route');
                            self.showModal = true;
                        });

                        /* attaches emit events for custom buttons */
                        let actionButtons = self.dtHandle.context[0].json.actionButtons;

                        if(actionButtons.custom instanceof Array) {
                            for(let i=0; i<actionButtons.custom.length; i++) {
                                let elem = actionButtons.custom[i];

                                $('table .' + elem.cssSelectorClass).off('click').on('click', function() {
                                    let id = $(this).data('id'); //id for self event so we know what we act on
                                    let event = $(this).data('event'); //type of event
                                    eventHub.$emit(event, id);
                                });
                            }
                        }
                    },
                    initComplete: function(settings, json) {

                        initBootstrapSelect('.dataTables_length select', '60px', false);
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
            };
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
            initTable: function() {
                axios.get(this.source + '/initTable').then((response) => {
                    let self = this;
                    this.tableOptions.columns = response.data.columns;
                    this.buildEditorFields();
                    this.tableClass = response.data.tableClass;
                    this.header = response.data.header;
                    this.tableOptions.ajax.data.totals = response.data.totals ? response.data.totals : [];
                    this.computeCrtNo(response.data);
                    this.computeActionButtons(response.data);
                    this.tableOptions.dom = response.data.hasOwnProperty('dom') ? response.data.dom : this.tableOptions.dom;
                    this.hasEditor = response.data.hasOwnProperty('editable');
                    this.computeRender(response.data);
                }).then(() => {

                    if (this.hasEditor) {
                        this.initEditor();
                    };

                    this.dtHandle = $('#table-' + this._uid).DataTable(this.tableOptions);
                    this.addProcessingListener();
                });
            },
            buildEditorFields: function(columns) {
                let self = this;

                this.tableOptions.columns.forEach(function(record) {
                    self.editorOptions.fields.push({name: record.name});
                });
            },
            computeCrtNo: function(data) {
                if (data.hasOwnProperty('crtNo')) {
                    this.tableOptions.columns.unshift(this.firstColumn);
                    this.header.unshift(data.crtNo);
                }
            },
            computeActionButtons: function(data) {
                if (data.hasOwnProperty('actionButtons')) {
                    this.tableOptions.columns.push(this.lastColumn);
                    this.header.push(data.actionButtons);
                }
            },
            computeRender: function(data) {
                if (data.hasOwnProperty('render')) {
                    let renderFunction,
                        self = this;

                    data.render.forEach(function(columnIndex) {
                        renderFunction = function(data, type, row, meta) {
                            return self.$parent.customRender(self.tableOptions.columns[columnIndex].data, data, type, row, meta);
                        };

                        self.tableOptions.columns[columnIndex] = Object.assign({}, { render: renderFunction }, self.tableOptions.columns[columnIndex]);
                    });
                }
            },
            addProcessingListener: function() {

                let self = this;

                this.dtHandle.on( 'processing.dt', function ( e, settings, processing ) {
                    self.updateLoadingState(processing);
                });
            },
            initEditor: function() {
                this.dtEditorHandle = new $.fn.dataTable.Editor(this.editorOptions);
                this.addClickListener();
                this.addSelectOnFocusListener();
                this.addBlurListener();
                this.addPostSubmitListener();
            },
            addClickListener: function() {
                let self = this;

                $('#table-' + this._uid).on( 'click', 'tbody tr td.editable', function (e) {
                    self.editedCellValue = self.dtHandle.cell(this).data();
                    self.dtEditorHandle.inline(this);
                });
            },
            addSelectOnFocusListener: function () {
                let self = this;

                $( 'input', this.dtEditorHandle.node() ).on( 'focus', function () {
                    $(this).val(self.editedCellValue);
                    this.select();
                });
            },
            addBlurListener: function() {
                let self = this;

                $( 'input', this.dtEditorHandle.node() ).on( 'blur', function () {
                    self.editedCellValue = null;
                });
            },
            addPostSubmitListener: function() {
                let self = this;

                this.dtEditorHandle.on( 'postSubmit', function ( e, msg ) {
                    if(msg.hasOwnProperty("error")) {
                        toastr.error(msg.error);
                    } else {
                        toastr.success(msg.message);
                    }
                });
            },
            updateLoadingState: function(processing) {
                this.loading = processing;

                if (processing) {
                    NProgress.start();
                } else {
                    NProgress.done();
                }
            },
            drawColumnTotal: function(api, column, total, dec = 2, char = '') {
                $(api.table().column(column).footer()).html('<b>' + this.numberFormat(parseFloat(total).toFixed(dec)) + char + '</b>');
            },
            buildActionButtons: function(actions, data) {
                let buttons = '<span style="display: inline-flex">';
                buttons += this.getCustomActionButtons(actions, data);
                buttons += this.getStandardActionButtons(actions, data);
                buttons += '</span>';

                return buttons;
            },
            getCustomActionButtons: function(actions, data) {
                let buttons = '';

                if (actions.custom instanceof Array) {
                    for(let i = 0; i < actions.custom.length; i++) {
                        let button = actions.custom[i];
                        buttons += '<a class="margin-left-xs ' + button.cssSelectorClass + '"data-id="' + data + '" data-event="' + button.event + '"><i class="btn btn-xs ' + button.cssClass + '"></i></a>';
                    }
                }

                return buttons;
            },
            getStandardActionButtons: function(actions, data) {
                let buttons = '';
                buttons += actions.show ? '<a class="btn btn-xs btn-success margin-left-xs" href="' + this.source + '/' + data + '"><i class="fa fa-eye"></i></a>' : '';
                buttons += actions.edit ? '<a class="btn btn-xs btn-warning margin-left-xs" href="' + this.source + '/' + data + '/edit"><i class="fa fa-pencil-square-o"></i></a>' : '';
                buttons += actions.download ? '<a class="btn btn-xs btn-success margin-left-xs" href="' + this.source + '/' + 'download/' + data + '"><i class="fa fa-cloud-download"></i></a>' : '';
                buttons += actions.delete ? '<button class="btn btn-xs btn-danger margin-left-xs delete-model" data-route="' + this.source + '/' + data + '"><i class="fa fa-trash-o"></i></button>' : '';

                return buttons;
            },
            getData: function() {
                if (!this.dtHandle) {
                    this.initTable();
                } else if (!this.is_collapsed) {
                    let info = this.dtHandle.page.info();
                    this.dtHandle.ajax.reload();
                    this.dtHandle.page(info.page).draw('page');
                }
            },
            deleteModel: function() {
                this.showModal = false;
                let self = this;

                axios.delete(this.deleteRoute).then(function(response) {
                    self.dtHandle.ajax.reload();
                    toastr[response.data.level](response.data.message);
                }).catch(function (error) {
                    toastr[error.response.data.level](error.response.data.message);
                });
            },
            resize: function() {
                if (this.is_collapsed) {
                    return false;
                }

                this.dtHandle.columns.adjust();
            },
            numberFormat: function(value) {
                let x = value.split('.'),
                    x1 = x[0],
                    x2 = x.length > 1 ? '.' + x[1] : '',
                    rgx = /(\d+)(\d{3})/;

                value += '';

                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }

                return x1 + x2;
            }
        },
        mounted: function() {

            if (!this.is_collapsed) {
                this.initTable();
            }
        }
    };

</script>

<style>

    .DTE_Field_Input input {
        height: 28px;
    }

</style>