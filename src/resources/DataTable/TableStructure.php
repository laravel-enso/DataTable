<?php

namespace App\DataTable;

namespace TableStructure;

use App\Utils\DataTable\TableStructure;

class ModelTableStructure extends TableStructure
{
    public function __construct()
    {
        $this->data = [
            /* The table name that will be shown in front end
             * It will be also used as the file name if the excel export is called
             */
            'name' => '__("Table")',
            /* current number for each line, with the header name
             * If given, it will be appended as the first column of the table
             */
            'crtNo'         => '#',
            /* actions column label
            */
            'actions' => '__("Actions")',
            /* list of the standard actions - show, edit, download, delete.
             * Note: the buttons for standard actions are added depending on the users permissions
             */
            'actionButtons' => ['show', 'edit', 'download', 'destroy'],
            /* list of action buttons for custom actions
             * cssSelectorClass is used for adding the button event listener
             * cssClass is used for styling the custom button
             * event is the event triggered by the button
             * route is OPTIONAL, and if given, it will be checked for permission i.e. if the user doesn't have
             * the necessary permission, that specific button is not drawn
             */
            'customActionButtons'     => [
                ['cssClass' => 'btn-success fa fa-info-circle', 'event'=>'custom-event', 'route' => 'route.getData'],
            ],
            /* list of the header buttons - create, export excel.
             * Note: the buttons are added depending on the users permissions
             */
            'headerButtons' => ['create', 'exportExcel'],
            /* columns where custom rendering is applied
             * Note: a 'customRender' method needs to exist in the parent vue instance
             */
            'render'        => [columnIndexArray],
            /* column number where the total value is displayed
             * and the table's column name used in the query, to compute the total
             */
            'totals'        => [columnIndexArray],
            /* computed responsive priority will be 1 for first column
             * and will increment with one for each consecutive column
             */
            'responsivePriority' => [columnIndexArray],
            /* list of columns that are not searchable,
             * such as columns that are translated */
            'notSearchable' => [columnIndexArray],
            /* list of columns that you don't want to be sortable */
            'notSortable' => [columnIndexArray],
            /* list of editable columns
             * Note:  the  parameter is needed, and
             * only attributes of this model are editable i.e. you can't
             * edit attributes of 'joined' models/tables
             */
            'editable' => [columnIndexArray],
            /* if none is given, by default, 'Bfrtip' is used.
             * See datatables.net documentation */
            'dom' => 'Bfrtip',
            /* table header alignment. The dt-head-* class is used,
             * i.e. dt-head-center in this example  */
            'headerAlign'        => 'left center right',
            /* table body alignment. The dt-body-center class is used in this example
             */
            'bodyAlign'        => 'left center right',
            /* custom css classes for the <table> element
            */
            'tableClass'         => 'custom-class',
            /* array of columns whose values should be displayed translated.
            * Note: in order to work it needs a ColumnEnum class
             */
            'enumMappings' => [
                'status' => 'StatusesEnum',
            ],
            /* array of eloquent model accessors that will be appended
            * Note: if this attribute is missing appends will be set an empty array
             */
            'appends' => [accessorList],
            /* configuration of the columns to be displayed
             */
            'columns' => [
                0 => [
                    'label' => 'labelName',
                    'data'  => 'attributeName',
                    'name'  => 'attributeSource',
                    'class' => 'className',
                ],
            ],
        ];
    }
}
