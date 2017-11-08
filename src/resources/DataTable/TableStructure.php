<?php

namespace App\DataTable;

namespace TableStructure;

use LaravelEnso\DataTable\app\Classes\TableStructure;

class ModelTableStructure extends TableStructure
{
    public function __construct()
    {
        $this->data = [

            /* The table name that will be shown in front end
             * It will be also used as the file name if the excel export is called
             * Optional.
             */
            'name' => '__("Table")',

            /* the icon shown in the title bar, next to the title
             * Optional.
             */
            'icon' => 'fa fa-list-alt',

            /* current number for each line, with the header name
             * If given, it will be appended as the first column of the table
             * Optional.
             */
            'crtNo' => __('#'),

            /* actions column label, if you need it
             * Optional.
             */
            'actions' => '__("Actions")',

            /* list of the standard actions - show, edit, download, delete.
             * Optional.
             * Note: the buttons for standard actions are added depending on the users permissions
             */
            'actionButtons' => ['show', 'edit', 'download', 'destroy'],

            /* list of buttons to display in table header area, next to the default ones
             * Possible values: create, exportExcel
             * Optional.
             * Note: the buttons are added depending on the users permissions
             */
            'headerButtons' => ['create', 'exportExcel'],

            /* list of action buttons for custom actions
             * icon is used for the button's icon
             * class is used for styling the custom button
             * event is the event triggered by the button
             * route is OPTIONAL, and if given, it will be checked for permission i.e. if the current user doesn't have
             * the necessary permission, that specific button is not rendered.
             * Optional.
             */
            'customActionButtons' => [
                ['icon' => 'fa fa-sliders', 'class' => 'is-info', 'event' => 'custom-event', 'route' => 'route.getData'],
            ],

            /* columns where custom rendering is applied
             * Note: a 'customRender' method needs to exist in the parent vue instance. If you're custom rendering
             * multiple columns, in your js method you'll have to make the distinction
             * Optional.
             */
            'render' => [columnArrayIndex],

            /* column number for which the total value is displayed
             * and, at the same time, the table's column name used in the query that computes the total
             * Optional.
             */
            'totals' => [columnArrayIndex],

            /* computed responsive priority will be 1 for first column
             * and will increment with one for each consecutive column
             * Optional.
             */
            'responsivePriority' => [columnArrayIndex],

            /* list of columns that are NOT searchable,
             * such as columns that are translated, are results of custom attributes, etc.
             * Optional.
             */
            'notSearchable' => [columnIndexArray],

            /* list of columns that you don't want to be sortable,
             * such as columns that are translated, are results of custom attributes, etc.
             * Optional.
             */
            'notSortable' => [columnIndexArray],

            /* list of editable columns
             * Note:  the  parameter is needed, and
             * only attributes of this model are editable i.e. you can't
             * edit attributes of 'joined' models/tables
             * Optional. Experimental, see README
             */
            'editable' => [columnIndexArray],

            /* if none is given, by default, 'Bfrtip' is used.
             * See datatables.net documentation.
             * Optional.
             */
            'dom' => 'Bfrtip',

            /* table header alignment. The dt-head-* class is used,
             * i.e. dt-head-center in this example.
             * Possible values are: left, center, right
             * Required.
             */
            'headerAlign' => 'center',

            /* table body alignment. The dt-body-center class is used in this example
             * Possible values are: left, center, right
             * Required.
             */
            'bodyAlign' => 'center',

            /* custom css classes for the <table> element
             * Optional.
             */
            'tableClass' => 'custom-class',

            /* array of columns whose values should be displayed 'translated'
             * Note: in order to work it needs an Enum class
             * Optional.
             */
            'enumMappings' => [
                'status' => StatusesEnum::class,
            ],

            /* array of eloquent model accessors that will be appended
             * Note: if this attribute is missing appends will be set an empty array
             * Optional.
             */
            'appends' => [accessorList],

            /* configuration of the columns to be displayed
             * Required.
             */
            'columns' => [
                0 => [
                    'label' => 'labelName',
                    'data'  => 'attributeName',
                    'name'  => 'attributeSource',
                    'class' => 'className', //optional
                ],
            ],
        ];
    }
}
